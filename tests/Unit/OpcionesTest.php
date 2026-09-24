<?php

use Aeunius\NumeroALetras\Contracts\Moneda as MonedaContract;
use Aeunius\NumeroALetras\Enums\FormatoCentavos;
use Aeunius\NumeroALetras\Enums\Moneda;
use Aeunius\NumeroALetras\Exceptions\OpcionNoValida;
use Aeunius\NumeroALetras\Support\Conversor;

/**
 * @param  array{moneda?: string, caja?: string, centavos?: string, conector?: string, soloTexto?: bool}  $opciones
 */
function conversorCon(array $opciones): Conversor
{
    $conversor = new Conversor;

    foreach ($opciones as $opcion => $valor) {
        $conversor = match ($opcion) {
            'moneda' => $conversor->moneda($valor),
            'caja' => $valor === 'minusculas' ? $conversor->minusculas() : $conversor->mayusculas(),
            'centavos' => $conversor->formatoCentavos($valor),
            'conector' => $conversor->conector($valor),
            'soloTexto' => $conversor->soloTexto($valor),
        };
    }

    return $conversor;
}

it('aplica las opciones', function (int|float|string $monto, array $opciones, string $esperado) {
    expect(conversorCon($opciones)->convertir($monto))->toBe($esperado);
})->with('opciones');

it('tiene atajos para las monedas incluidas', function () {
    $conversor = new Conversor;

    expect($conversor->dolares()->convertir(1))->toBe('UNO CON 00/100 DÓLARES AMERICANOS')
        ->and($conversor->euros()->convertir(1))->toBe('UNO CON 00/100 EUROS')
        ->and($conversor->dolares()->soles()->convertir(1))->toBe('UNO CON 00/100 SOLES');
});

it('acepta el enum en moneda() y formatoCentavos()', function () {
    expect((new Conversor)->moneda(Moneda::USD)->formatoCentavos(FormatoCentavos::Texto)->convertir(2))
        ->toBe('DOS DÓLARES AMERICANOS');
});

it('es inmutable', function () {
    $base = new Conversor;
    $base->dolares()->minusculas()->soloTexto()->formatoCentavos('texto')->conector('y');

    expect($base->convertir(1.5))->toBe('UNO CON 50/100 SOLES');
});

it('acepta monedas propias', function () {
    $libra = new class implements MonedaContract
    {
        public function singular(): string
        {
            return 'libra esterlina';
        }

        public function plural(): string
        {
            return 'libras esterlinas';
        }

        public function femenina(): bool
        {
            return true;
        }

        public function centavoSingular(): string
        {
            return 'penique';
        }

        public function centavoPlural(): string
        {
            return 'peniques';
        }
    };

    $conversor = (new Conversor)->moneda($libra);
    $texto = $conversor->formatoCentavos('texto');

    expect($conversor->convertir(21))->toBe('VEINTIUNO CON 00/100 LIBRAS ESTERLINAS')
        ->and($texto->convertir(1))->toBe('UNA LIBRA ESTERLINA')
        ->and($texto->convertir(21.01))->toBe('VEINTIUNA LIBRAS ESTERLINAS CON UN PENIQUE')
        ->and($texto->convertir(200000))->toBe('DOSCIENTAS MIL LIBRAS ESTERLINAS')
        ->and($texto->convertir(200000000))->toBe('DOSCIENTOS MILLONES DE LIBRAS ESTERLINAS')
        ->and($texto->convertir(1_000_001))->toBe('UN MILLÓN UNA LIBRAS ESTERLINAS');
});

it('rechaza monedas que no conoce', function () {
    (new Conversor)->moneda('GBP');
})->throws(OpcionNoValida::class, 'La moneda "GBP" no está incluida (PEN, USD, EUR)');

it('rechaza formatos de centavos que no existen', function () {
    (new Conversor)->formatoCentavos('palabras');
})->throws(OpcionNoValida::class, 'Usa uno de: fraccion, texto');

it('rechaza conectores que no sean "con" o "y"', function (string $conector) {
    (new Conversor)->conector($conector);
})->with(['e', 'CON', ''])->throws(OpcionNoValida::class, 'Usa "con" o "y"');
