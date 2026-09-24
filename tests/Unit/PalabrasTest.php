<?php

use Aeunius\NumeroALetras\Exceptions\NumeroFueraDeRango;
use Aeunius\NumeroALetras\Support\Palabras;

it('convierte enteros a palabras', function (int $numero, string $esperado) {
    expect(Palabras::deEntero($numero))->toBe($esperado);
})->with('enteros');

it('apocopa el final delante de un sustantivo', function (int $numero, string $esperado) {
    expect(Palabras::deEntero($numero, apocope: true))->toBe($esperado);
})->with('apocope');

it('no deja espacios dobles ni sobrantes en ningún número hasta 100 000', function () {
    for ($numero = 0; $numero <= 100_000; $numero++) {
        $texto = Palabras::deEntero($numero);

        if ($texto !== trim($texto) || str_contains($texto, '  ')) {
            throw new RuntimeException("Espacios mal puestos en {$numero}: \"{$texto}\"");
        }
    }

    expect(true)->toBeTrue();
});

it('usa "cero" solo para el cero', function () {
    expect(Palabras::deEntero(1_000_000))->not->toContain('cero')
        ->and(Palabras::deEntero(1_000_000_000_000))->not->toContain('cero');
});

it('rechaza negativos', function () {
    Palabras::deEntero(-1);
})->throws(NumeroFueraDeRango::class, 'No se pueden convertir números negativos: -1.');

it('rechaza desde mil billones', function () {
    Palabras::deEntero(Palabras::MAXIMO + 1);
})->throws(NumeroFueraDeRango::class, 'supera el máximo que se puede convertir (999 999 999 999 999,99)');

it('concuerda en femenino', function (int $numero, string $esperado) {
    expect(Palabras::deEntero($numero, femenino: true))->toBe($esperado);
})->with('femenino');
