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
        ]
    ]
];
