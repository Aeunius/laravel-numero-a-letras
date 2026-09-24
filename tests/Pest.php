<?php

// Unit: el conversor puro de src/Support, sin Laravel.
//
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
