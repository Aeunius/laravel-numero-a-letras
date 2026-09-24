<?php

use Aeunius\NumeroALetras\Contracts\Moneda;
use Aeunius\NumeroALetras\Exceptions\OpcionNoValida;
use Aeunius\NumeroALetras\Facades\NumeroALetras;
use Aeunius\NumeroALetras\NumeroALetrasServiceProvider;
use Aeunius\NumeroALetras\Support\Conversor;

final class SolDeOro implements Moneda
{
    public function singular(): string
    {
        return 'sol de oro';
    }

    public function plural(): string
    {
        return 'soles de oro';
    }

    public function femenina(): bool
    {
        return false;
    }

    public function centavoSingular(): string
    {
        return 'centavo';
    }

    public function centavoPlural(): string
    {
        return 'centavos';
    }
}

it('convierte con el facade', function () {
    expect(NumeroALetras::convertir(1250.50))->toBe('MIL DOSCIENTOS CINCUENTA CON 50/100 SOLES');
});

it('encadena opciones desde el facade', function () {
    expect(NumeroALetras::moneda('USD')->convertir(99.90))->toBe('NOVENTA Y NUEVE CON 90/100 DÓLARES AMERICANOS')
        ->and(NumeroALetras::soles()->minusculas()->convertir(5))->toBe('cinco con 00/100 soles')
        ->and(NumeroALetras::soloTexto()->convertir(21))->toBe('VEINTIUNO');
});

it('no arrastra opciones de una llamada a otra', function () {
    NumeroALetras::dolares()->minusculas();

    expect(NumeroALetras::convertir(1))->toBe('UNO CON 00/100 SOLES');
});

it('registra el alias NumeroALetras', function () {
    expect(\NumeroALetras::convertir(1))->toBe('UNO CON 00/100 SOLES');
});

it('convierte con el helper', function () {
    expect(numero_a_letras(1250.50))->toBe('MIL DOSCIENTOS CINCUENTA CON 50/100 SOLES');
});

it('toma las opciones de la configuración', function () {
    config([
        'numero-a-letras.moneda' => 'usd',
        'numero-a-letras.mayusculas' => false,
        'numero-a-letras.formato_centavos' => 'texto',
        'numero-a-letras.conector' => 'y',
    ]);

    expect(NumeroALetras::convertir(21.5))->toBe('veintiún dólares americanos y cincuenta centavos')
        ->and(numero_a_letras(1))->toBe('un dólar americano')
        ->and(app(Conversor::class)->convertir(2))->toBe('dos dólares americanos');
});

it('acepta una clase propia como moneda en la configuración', function () {
    config(['numero-a-letras.moneda' => SolDeOro::class]);

    expect(NumeroALetras::convertir(10))->toBe('DIEZ CON 00/100 SOLES DE ORO')
        ->and(NumeroALetras::formatoCentavos('texto')->convertir(1))->toBe('UN SOL DE ORO');
});

it('avisa si la configuración tiene una opción inválida', function (string $clave, string $valor, string $mensaje) {
    config(["numero-a-letras.{$clave}" => $valor]);

    expect(fn () => NumeroALetras::convertir(1))->toThrow(OpcionNoValida::class, $mensaje);
})->with([
    ['moneda', 'GBP', 'La moneda "GBP" no está incluida'],
    ['moneda', stdClass::class, 'La moneda "stdClass" no está incluida'],
    ['formato_centavos', 'palabras', 'El formato de centavos "palabras" no existe'],
    ['conector', 'e', 'El conector "e" no es válido'],
]);

it('publica la configuración', function () {
    $publicables = NumeroALetrasServiceProvider::pathsToPublish(NumeroALetrasServiceProvider::class, 'numero-a-letras-config');

    expect($publicables)->toHaveCount(1)
        ->and(array_key_first($publicables))->toEndWith('config/numero-a-letras.php')
        ->and(array_values($publicables)[0])->toBe(config_path('numero-a-letras.php'));
});
