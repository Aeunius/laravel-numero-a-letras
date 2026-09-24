<?php

// Opciones por defecto del facade NumeroALetras y del helper numero_a_letras().
// Se publican con:
//   php artisan vendor:publish --tag=numero-a-letras-config

return [

    // Código ISO 4217 de una moneda incluida ('PEN', 'USD', 'EUR') o el nombre
    // de una clase que implemente Aeunius\NumeroALetras\Contracts\Moneda.
    'moneda' => 'PEN',

    // true: "MIL CON 00/100 SOLES". false: "mil con 00/100 soles".
    'mayusculas' => true,

    // 'fraccion': "CON 50/100 SOLES", el de los comprobantes de la SUNAT.
    // 'texto': "SOLES CON CINCUENTA CÉNTIMOS".
    'formato_centavos' => 'fraccion',

    // La palabra entre el entero y los centavos: 'con' o 'y'.
    'conector' => 'con',

];
