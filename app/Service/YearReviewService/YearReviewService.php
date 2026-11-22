<?php

namespace App\Service\YearReviewService;

use App\DTO\YearReview\YearReviewModuleDTO;
use App\Shared\Enums\YearReviewBlocks;

class YearReviewService implements YearReviewServiceInterface
{
    private array $config = [];

    private array $datasets = [];

    private YearReviewModuleDTO $context;

    private function getDatasets(): array
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
        $this->config = config("year_review.{$year}", []);
        if (! isset($this->config['datasets'], $this->config['pages'])) {
            throw new \InvalidArgumentException("Year review configuration not found: {$year}");
        }
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
            $reviewPage['blocks'] = [];

            foreach ($page['blocks'] as $block) {
                $reviewPageBlock = $this->handleBlock($block);

                if ($reviewPageBlock === null) {
                    continue 2;
                }

                $reviewPage['blocks'][] = $reviewPageBlock;
            }

            $review['pages'][] = $reviewPage;
        }

        return $review;
    }

    private function handleBlock(array $block): ?array
    {
        $reviewPageBlock = [];
        $reviewPageBlock['type'] = ($block['type'])->value;
        if (isset($block['dataset'])) {
            $dataset = $this->datasets[$block['dataset']];

            $replacements = isset($dataset['value']) && is_array($dataset['value'])
                ? $dataset['value']
                : $dataset;

            if (isset($block['index'])) {
                $replacements = $replacements[$block['index']] ?? null;
            }

            if ($replacements === null || $replacements === []) {
                return null;
            }

            $valueType = $block['valueType'] ?? null;
            $replacements = $block['type']->getStructure($replacements, $valueType);

            if ($block['type'] == YearReviewBlocks::TEXT) {
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

        $value = preg_replace_callback(
            '/\{([a-zA-Z_][a-zA-Z0-9_]*)\}(\s+)\[([^\[\]|]+)\|([^\[\]|]+)\|([^\[\]|]+)\]/u',
            static function (array $matches) use ($result): string {
                $number = $result[$matches[1]] ?? null;

                if (! is_scalar($number) || ! preg_match('/^-?\d+$/', (string) $number)) {
                    return $matches[0];
                }

                $lastTwoDigits = abs((int) $number % 100);
                $lastDigit = $lastTwoDigits % 10;
                $form = match (true) {
                    $lastTwoDigits >= 11 && $lastTwoDigits <= 14 => $matches[5],
                    $lastDigit === 1 => $matches[3],
                    $lastDigit >= 2 && $lastDigit <= 4 => $matches[4],
                    default => $matches[5],
                };

                return '{'.$matches[1].'}'.$matches[2].$form;
            },
            $value,
        );

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
