<?php

namespace App\Service\YearReviewService;

use App\DTO\Playlist\CreatePlaylistDTO;
use App\DTO\Playlist\PlaylistTrackDTO;
use App\DTO\Playlist\UpdatePlaylistDTO;
use App\DTO\YearReview\YearReviewModuleDTO;
use App\Exceptions\NotFoundException;
use App\Models\Playlist;
use App\Models\Track;
use App\Models\TrackPlaylist;
use App\Repositories\Mediateka\MediatekaRepositoryInterface;
use App\Repositories\Playlist\PlaylistRepositoryInterface;
use App\Service\FileService\FileServiceInterface;
use App\Service\PlaylistService\PlaylistServiceInterface;
use App\Service\TrackService\TrackServiceInterface;
use App\Shared\Enums\PlaylistTypes;
use App\Shared\Enums\YearReviewBlocks;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class YearReviewService implements YearReviewServiceInterface
{
    private array $config = [];
    private array $datasets = [];
    private YearReviewModuleDTO $context;
    private function getDatasets():array
    {
        $dataset = [];

        foreach ($this->config['datasets'] as $key => $value) {
            $moduleClass = $value['module'];
            $options = $value['options'];
            $result = $moduleClass::execute($this->context, $options);
            $dataset[$key] = $result;
        }

        return $dataset;
    }

    public function generateReview(int $userId, int $year): array
    {
        $this->config =  config("year_review.{$year}", []);
        $this->context = new YearReviewModuleDTO(
            $userId,
            $year,
        );

        $this->datasets = $this->getDatasets();

        $review = [];
        $review['year'] = $year;
        $review['user_id'] = $userId;
        $review['pages'] = [];

        foreach ($this->config['pages'] as $page) {
            $reviewPage = [];
            $reviewPage['page'] = $page['page'];
            $reviewPage['background'] = $page['background'];

            foreach ($page['blocks'] as $block) {
                $reviewPageBlock = $this->handleBlock($block, $userId, $year);

                $reviewPage['blocks'][] = $reviewPageBlock;
            }

            $review['pages'][] = $reviewPage;
        }
        dd($review);
        return $review;
    }

    private function handleBlock(array $block, int $userId, int $year) : array
    {
        $reviewPageBlock = [];
        $reviewPageBlock['type'] = ($block['type'])->value;
        if(isset($block['dataset'])) {
            $dataset = $this->datasets[$block['dataset']];


            $replacements = isset($dataset['value']) && is_array($dataset['value'])
                ? $dataset['value']
                : $dataset;

            if (isset($block['index'])) {
                $replacements = $replacements[$block['index']] ?? null;
//                dd($replacements);
            }

            $valueType = $block['valueType'] ?? null;
            $replacements = $block['type']->getStructure($replacements, $valueType);

//            if ($block['type'] == YearReviewBlocks::LIST and $valueType == YearReviewBlocks::TRACK) {
//                dd($replacements);
//            }

            if($block['type'] == YearReviewBlocks::ARTIST) {
//                dd($replacements, $block['value']);
            }

            if($block['type'] == YearReviewBlocks::TEXT) {
                $replacements = isset($replacements['value']) && is_array($replacements['value'])
                    ? $replacements['value']
                    : $replacements;
            }




            $reviewPageBlock['value'] = $this->replacePlaceholders(
                $block['value'] ?? '',
                $replacements,
            );
        } elseif (array_key_exists('value', $block)) {
            $reviewPageBlock['value'] = $block['value'];
        } elseif (array_key_exists('values', $block)) {
            $count = count($block['values']);
            $rnd = rand(0, $count - 1);
            $reviewPageBlock['value'] = $block['values'][$rnd];
        }

        return $reviewPageBlock;
    }

    private function replacePlaceholders(mixed $value, array $result): mixed
    {
        if (is_array($value)) {
            return array_map(
                fn (mixed $item): mixed => is_string($item) || is_array($item)
                    ? $this->replacePlaceholders($item, $result)
                    : $item,
                $value,
            );
        }

        if (! is_string($value)) {
            return $value;
        }

        if (
            preg_match('/^\{([a-zA-Z_][a-zA-Z0-9_]*)\}$/', $value, $matches)
            && array_key_exists($matches[1], $result)
        ) {
            return $result[$matches[1]];
        }

        return preg_replace_callback(
            '/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/',
            static function (array $matches) use ($result): string {
                $replacement = $result[$matches[1]] ?? null;

                return is_scalar($replacement)
                    ? (string) $replacement
                    : $matches[0];
            },
            $value,
        );
    }
}
