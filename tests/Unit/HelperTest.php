<?php

// Sin Laravel (o sin el ServiceProvider registrado) el helper usa las opciones por defecto.
it('convierte con el helper fuera de Laravel', function () {
    expect(numero_a_letras(1250.50))->toBe('MIL DOSCIENTOS CINCUENTA CON 50/100 SOLES');
});
