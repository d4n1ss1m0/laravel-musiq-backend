<?php


namespace App\Shared\Enums;

use App\Models\PlaylistType;

enum YearReviewBlocks: string
{
    case TITLE = 'title';
    case TEXT = 'text';
    case LIST = 'list';
    case ARTIST = 'artist';
    case TRACK = 'track';

    public function getStructure(array $array, YearReviewBlocks $valueType = null)
    {
        $return['value'] = $this->artistStructure($array, $valueType);
        return $return;
    }

    private function artistStructure(array $array, YearReviewBlocks $valueType = null)
    {
        $return = [];
        switch ($this) {
            case self::ARTIST:
                $return = [
                    "id" => $array['uuid'],
                    "name" => $array['name'],
                    "image" => $array['image'],
                ];
                break;
            case self::TRACK:
                $return = [
                    "id" => $array['id'],
                    "name" => $array['name'],
                    "image" => $array['image'],
                    "artists" => $array['artists'],
                ];
                break;
            case self::LIST:
                foreach ($array as $value) {
                    $sub = [
                        'type' => $valueType->value,
                        'value' => $valueType->artistStructure($value, null)
                    ];
                    $return[] = $sub;
                }
                break;
            default:
                return $array;
        }

        return $return;
    }

}
