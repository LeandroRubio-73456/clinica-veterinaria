<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Tolerancia para inasistencias
    |--------------------------------------------------------------------------
    |
    | Una cirugía se marca como no presentada únicamente cuando continúa en
    | estado "scheduled" después de su hora de inicio más esta tolerancia.
    |
    */
    // Nunca debe ser cero por accidente: la tolerancia mínima del negocio es 15 minutos.
    'no_show_grace_minutes' => max(15, (int) env('SURGERY_NO_SHOW_GRACE_MINUTES', 15)),
];
