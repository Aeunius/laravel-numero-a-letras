<?php

use Aeunius\NumeroALetras\Exceptions\NumeroFueraDeRango;
use Aeunius\NumeroALetras\Exceptions\NumeroNoValido;
use Aeunius\NumeroALetras\Support\Conversor;

it('convierte montos con el formato de la SUNAT', function (int|float|string $monto, string $esperado) {
    expect((new Conversor)->convertir($monto))->toBe($esperado);
})->with('montos');

it('rechaza montos negativos', function (int|float|string $monto) {
    (new Conversor)->convertir($monto);
})->with([-1, -0.01, '-5', ' -5 ', PHP_INT_MIN])->throws(NumeroFueraDeRango::class, 'negativos');

it('rechaza montos desde mil billones', function (int|float|string $monto) {
    (new Conversor)->convertir($monto);
})->with([
    1_000_000_000_000_000,
    1e15,
    1e20,
    '1000000000000000',
    '999999999999999.995',
    '99999999999999999999999999',
    PHP_INT_MAX,
])->throws(NumeroFueraDeRango::class, 'supera el máximo');

it('rechaza lo que no es un número', function (float|string $monto) {
    (new Conversor)->convertir($monto);
})->with([
    '',
    'abc',
    '1,250.50',
    '1 250',
    '1250.',
    '.50',
    '1e3',
    '+5',
    INF,
    NAN,
])->throws(NumeroNoValido::class, 'No es un número válido');
