<?php

return [
    'primary_domain' => env('APP_URL', 'https://baller.juulex.de'),
    'match_duration_minutes' => (int) env('BALLER_MATCH_DURATION_MINUTES', 7),
    'league_team_size' => 12,
    'default_transfer_listing_hours' => 72,
];
