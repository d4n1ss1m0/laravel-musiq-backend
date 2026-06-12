<?php

namespace App\Enum\Fields;

enum Fields: string
{
    //Global
    case Name = 'name';
    case Title = 'title';
    case File = 'file';

    //Artist
    case Artist = 'artist';

    //Song
    case Song = 'song';

    //Pagination
    case Page = 'page';
    case PerPage = 'limit';
    case Items = 'items';
    case ItemsIds = 'itemsIds';

}
