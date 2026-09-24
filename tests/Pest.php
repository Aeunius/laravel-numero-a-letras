<?php

use Aeunius\NumeroALetras\Tests\TestCase;

// Feature: dentro de una aplicación Laravel simulada con Testbench.
// Unit: el conversor puro de src/Support, sin Laravel.
// Sin Laravel instalado (una de las filas del CI) solo corren los de Unit.
if (class_exists(Orchestra\Testbench\TestCase::class)) {
    uses(TestCase::class)->in('Feature');
}

// Los datasets viven en JSON para reutilizarlos tal cual en el gemelo de JavaScript.
foreach (['enteros', 'apocope', 'femenino', 'montos'] as $nombre) {
    dataset($nombre, fn () => json_decode(
        (string) file_get_contents(__DIR__."/Datasets/{$nombre}.json"),
        true,
        flags: JSON_THROW_ON_ERROR,
    ));
}

// Cada caso: {"monto", "opciones": {moneda, caja, centavos, conector, soloTexto}, "esperado"}.
dataset('opciones', fn () => array_map(
    fn (array $caso) => [$caso['monto'], $caso['opciones'], $caso['esperado']],
    json_decode((string) file_get_contents(__DIR__.'/Datasets/opciones.json'), true, flags: JSON_THROW_ON_ERROR),
));
