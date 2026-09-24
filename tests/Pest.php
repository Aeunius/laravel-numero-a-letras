<?php

// Unit: el conversor puro de src/Support, sin Laravel.
//
// Los datasets viven en JSON para reutilizarlos tal cual en el gemelo de JavaScript.
foreach (['enteros', 'apocope', 'montos'] as $nombre) {
    dataset($nombre, fn () => json_decode(
        (string) file_get_contents(__DIR__."/Datasets/{$nombre}.json"),
        true,
        flags: JSON_THROW_ON_ERROR,
    ));
}
