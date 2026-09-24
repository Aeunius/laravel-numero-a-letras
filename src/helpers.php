<?php

use Aeunius\NumeroALetras\Support\Conversor;
use Illuminate\Container\Container;

if (! function_exists('numero_a_letras')) {
    /**
     * numero_a_letras(1250.50) → "MIL DOSCIENTOS CINCUENTA CON 50/100 SOLES"
     *
     * Dentro de Laravel usa las opciones de config/numero-a-letras.php; fuera de
     * Laravel, las opciones por defecto.
     */
    function numero_a_letras(int|float|string $monto): string
    {
        $conversor = class_exists(Container::class) && Container::getInstance()->bound(Conversor::class)
            ? Container::getInstance()->make(Conversor::class)
            : new Conversor;

        return $conversor->convertir($monto);
    }
}
