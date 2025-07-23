<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuración de la Geocerca de la Compañía
    |--------------------------------------------------------------------------
    |
    | Aquí se define la ubicación central de la oficina y el radio
    | máximo permitido en metros para que un marcaje sea considerado válido.
    |
    */

    'location' => [
        // Coordenadas del centro de la geocerca (Ej: Plaza de Armas, Valdivia)
        'latitude' => env('COMPANY_LATITUDE', -39.547544), 
        'longitude' => env('COMPANY_LONGITUDE', -72.956661),
    ],

    // Radio en metros. Un empleado debe estar dentro de este radio para marcar.
    'radius_meters' => env('COMPANY_RADIUS_METERS', 100), // 500 metros por defecto

];
