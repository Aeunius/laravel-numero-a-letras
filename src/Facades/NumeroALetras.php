<?php

namespace Aeunius\NumeroALetras\Facades;

use Aeunius\NumeroALetras\Contracts\Moneda;
use Aeunius\NumeroALetras\Enums\FormatoCentavos;
use Aeunius\NumeroALetras\Support\Conversor;
use Illuminate\Support\Facades\Facade;

/**
 * @method static string convertir(int|float|string $monto)
 * @method static Conversor moneda(string|Moneda $moneda)
 * @method static Conversor soles()
 * @method static Conversor dolares()
 * @method static Conversor euros()
 * @method static Conversor mayusculas()
 * @method static Conversor minusculas()
 * @method static Conversor soloTexto(bool $soloTexto = true)
 * @method static Conversor formatoCentavos(string|FormatoCentavos $formato)
 * @method static Conversor conector(string $conector)
 *
 * @see Conversor
 */
class NumeroALetras extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return Conversor::class;
    }
}
