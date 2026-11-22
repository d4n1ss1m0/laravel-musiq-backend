<?php

use App\Service\YearReviewService\modules\TopArtistsModule;
use App\Service\YearReviewService\modules\TopFavouriteTracksModule;
use App\Service\YearReviewService\modules\TopTracksModule;
use App\Shared\Enums\YearReviewBlocks;
use App\Service\YearReviewService\modules\TotalMinutesModule;

return [
    'year' => 2026,
    'available' => '2026-12-25',
    'datasets' => [
        'total_minutes' => [
            'module' => TotalMinutesModule::class,
            'options' => []
        ],
        'top_artists' => [
            'module' => TopArtistsModule::class,
            'options' => [
                'limit' => 5
            ]
        ],
        'top_tracks' => [
            'module' => TopTracksModule::class,
            'options' => [
                'limit' => 5
            ]
        ],
        'top_favourite_tracks' => [
            'module' => TopFavouriteTracksModule::class,
            'options' => [
                'limit' => 5
            ]
        ]
    ],
    'pages' => [
        [
            "page" => 1,
            "background" => 'year_review/background/2026/background1.webp',
            "blocks" => [
                [
                    "type" => YearReviewBlocks::TITLE,
                    "value" => "Твои итоги года"
                ],
                [
                    "type" => YearReviewBlocks::TEXT,
                    "values" => [
                        "Мы собрали для тебя несколько итогов. Готов узнать, каким был твой музыкальный год?",
                        "Самое время оглянуться назад. Давай посмотрим, чем тебе запомнился этот год в музыке.",
                        "Мы собрали самые интересные моменты твоего года. Посмотрим, что получилось?"
                    ]
                ]
            ]
        ],
        [
            "page" => 2,
            "background" => 'year_review/background/2026/background1.webp',
            "blocks" => [
                [
                    "type" => YearReviewBlocks::TITLE,
                    "value" => "Прослушано минут"
                ],
                [
                    "type" => YearReviewBlocks::TEXT,
                    "dataset" => "total_minutes",
                    'value' => "{min}",
                    "calculatable" => true,
                ],
                [
                    "type" => YearReviewBlocks::TEXT,
                    "dataset" => "total_minutes",
                    'value' => "это {day} [день|дня|дней]",
                    "calculatable" => true,
                ],
                [
                    "type" => YearReviewBlocks::TEXT,
                    "values" => [
                        "Впечатляющий результат! А теперь узнаем, кто из исполнителей звучал у тебя чаще остальных.",
                        "Неплохо! Кажется, один исполнитель особенно часто оказывался у тебя в наушниках.",
                        "Цифры говорят сами за себя. Теперь посмотрим, кто стал главным исполнителем твоего года."
                    ]
                ]
            ]
        ],
        [
            "page" => 3,
            "background" => 'year_review/background/2026/background2.webp',
            "blocks" => [
                [
                    "type" => YearReviewBlocks::TITLE,
                    "value" => "Твой любимый исполнитель"
                ],
                [
                    "type" => YearReviewBlocks::ARTIST,
                    "dataset" => "top_artists",
                    "value" => "{value}",
                    "index" => 0,
                    "calculatable" => true
                ],
                [
                    "type" => YearReviewBlocks::TEXT,
                    "value" => "Прослушано минут"
                ],
                [
                    "type" => YearReviewBlocks::TEXT,
                    "dataset" => "top_artists",
                    "value" => "{total_min}",
                    "index" => 0,
                    "calculatable" => true
                ],
                [
                    "type" => YearReviewBlocks::TEXT,
                    "values" => [
                        "Похоже, с этим исполнителем у тебя особый музыкальный коннект.",
                        "Кажется, вы с этим исполнителем действительно нашли друг друга.",
                        "Этот исполнитель явно занял особое место в твоём музыкальном году."
                    ]
                ]
            ]
        ],
        [
            "page" => 4,
            "background" => 'year_review/background/2026/background2.webp',
            "blocks" => [
                [
                    "type" => YearReviewBlocks::TITLE,
                    "value" => "Твои любимые исполнители"
                ],
                [
                    "type" => YearReviewBlocks::LIST,
                    "valueType" => YearReviewBlocks::ARTIST,
                    "dataset" => "top_artists",
                    "count" => 5,
                    "value" => "{value}",
                    "calculatable" => true
                ],
                [
                    "type" => YearReviewBlocks::TEXT,
                    "values" => [
                        "Отличная подборка! А теперь узнаем, какой трек ты слушал чаще всего.",
                        "Весьма интересный набор. А что же по трекам? Как и везде, там есть один абсолютный фаворит.",
                        "Хороший список! Теперь посмотрим, какой трек стал главным саундтреком твоего года."
                    ]
                ]
            ]
        ],
        [
            "page" => 5,
            "background" => 'year_review/background/2026/background3.webp',
            "blocks" => [
                [
                    "type" => YearReviewBlocks::TITLE,
                    "value" => "Твой любимый трек"
                ],
                [
                    "type" => YearReviewBlocks::TRACK,
                    "dataset" => "top_tracks",
                    "value" => "{value}",
                    "index" => 0,
                    "calculatable" => true
                ],
                [
                    "type" => YearReviewBlocks::TEXT,
                    "values" => [
                        "Вот он — твой главный трек года. Ты включал его целых",
                        "Похоже, этот трек тебе совсем не надоел: за год ты прослушал его",
                        "Главный хит твоего года найден! Ты прослушал его"
                    ]
                ],
                [
                    "type" => YearReviewBlocks::TEXT,
                    "dataset" => "top_tracks",
                    "value" => "{play_count} [раз!|раза!|раз!]",
                    "index" => 0,
                    "calculatable" => true
                ],
                [
                    "type" => YearReviewBlocks::TEXT,
                    "values" => [
                        "Один фаворит уже найден. А теперь посмотрим, какие ещё треки вошли в твою пятёрку лучших.",
                        "С главным треком всё понятно. Теперь соберём всю пятёрку треков, которые чаще всего звучали у тебя в этом году.",
                        "Этот трек стал главным саундтреком года. Но он был не один — пора взглянуть на весь твой топ-5."
                    ]
                ]
            ]
        ],
        [
            "page" => 6,
            "background" => 'year_review/background/2026/background3.webp',
            "blocks" => [
                [
                    "type" => YearReviewBlocks::TITLE,
                    "value" => "Твои любимые треки"
                ],
                [
                    "type" => YearReviewBlocks::LIST,
                    "valueType" => YearReviewBlocks::TRACK,
                    "dataset" => "top_tracks",
                    "count" => 5,
                    "value" => "{value}",
                    "calculatable" => true
                ],
                [
                    "type" => YearReviewBlocks::TEXT,
                    "values" => [
                        "Интересный список! Теперь посмотрим, какой новый трек попал в твои любимые в этом году.",
                        "А теперь немного о новых находках. Посмотрим, какой трек ты добавил в любимые в этом году.",
                        "Среди любимых появились и новички. Давай посмотрим на одного из них."
                    ]
                ]
            ]
        ],
        [
            "page" => 7,
            "background" => 'year_review/background/2026/background3.webp',
            "blocks" => [
                [
                    "type" => YearReviewBlocks::TITLE,
                    "value" => "В этом году твой новый любимый трек"
                ],
                [
                    "type" => YearReviewBlocks::TRACK,
                    "dataset" => "top_favourite_tracks",
                    "value" => "{value}",
                    "index" => 0,
                    "calculatable" => true
                ],
                [
                    "type" => YearReviewBlocks::TEXT,
                    "values" => [
                        "Этот трек попал в «Любимые треки» только в этом году, но быстро стал особенным.",
                        "Ты добавил этот трек в любимые в этом году — похоже, он сразу попал точно в цель.",
                        "Новинка этого года, которая быстро добралась до твоего сердца и «Любимых треков»."
                    ]
                ]
            ]
        ],
    ]
];
