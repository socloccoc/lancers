<?php
/**
 * Created by PhpStorm.
 * User: daidv
 * Date: 5/23/21
 * Time: 2:45 PM
 */
return [
    'accounts' => [
        'lancers' => [
            'email' => env('LANCERS_EMAIL'),
            'pass' => env('LANCERS_PASSWORD'),
        ],
        'sokudan' => [
            'email' => env('SOKUDAN_EMAIL'),
            'pass' => env('SOKUDAN_PASSWORD'),
        ],
        'crowdworks' => [
            'email' => env('CROWDWORKS_EMAIL'),
            'pass' => env('CROWDWORKS_PASSWORD'),
            'job_category' => [
                'https://crowdworks.jp/public/jobs/group/development',
                'https://crowdworks.jp/public/jobs/group/software_development',
                'https://crowdworks.jp/public/jobs/group/web_products',
                'https://crowdworks.jp/public/jobs/group/ec',
            ]
        ]
    ],
    'spreadsheetId' => env('SHEET_ID')
];
