<?php

$variables = [
    'FLIP_SECRET' => 'FLIP_API_KEY',
];

foreach ($variables as $key => $value) {
    putenv("$key=$value");
}
