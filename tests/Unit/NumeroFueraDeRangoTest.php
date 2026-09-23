<?php

use Aeunius\NumeroALetras\Exceptions\NumeroFueraDeRango;

it('es una excepción de argumento inválido', function () {
    expect(NumeroFueraDeRango::negativo(-5))
        ->toBeInstanceOf(InvalidArgumentException::class)
        ->getMessage()->toBe('No se pueden convertir números negativos: -5.');
});
