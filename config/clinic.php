<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Horario operativo de la clínica
    |--------------------------------------------------------------------------
    |
    | ISO-8601: 1 = lunes ... 7 = domingo. Estos valores representan la
    | capacidad planificada usada por los reportes de ocupación.
    |
    */
    'operating_hours' => [
        'start' => env('CLINIC_OPERATING_START', '07:00'),
        'end' => env('CLINIC_OPERATING_END', '19:00'),
        'days' => array_map(
            'intval',
            explode(',', env('CLINIC_OPERATING_DAYS', '1,2,3,4,5'))
        ),
    ],
];
