<?php

namespace App\Shared\Fields;

final class Fields
{
    // Global
    public const ID = 'id';
    public const IDS = 'ids';
    public const NAME = 'name';
    public const TITLE = 'title';
    public const FILE = 'file';
    public const COVER = 'cover';
    public const COVER_NAME = 'coverName';
    public const LINK = 'link';
    public const SERVICE = 'service';
    public const QUERY = 'query';
    public const USER_ID = 'userId';
    public const TYPE = 'type';
    public const ORDER = 'order';

    // Artist
    public const ARTIST = 'artist';
    public const ARTISTS = 'artists';

    // Song
    public const SONG = 'song';

    // Pagination
    public const PAGE = 'page';
    public const PER_PAGE = 'limit';
    public const ITEMS = 'items';
    public const ITEMS_IDS = 'itemsIds';

    //History
    public const SOURCE = 'source';
    public const SOURCE_ID = 'source_id';

    private function __construct()
    {
    }
}
