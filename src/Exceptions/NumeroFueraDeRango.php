<?php

namespace Aeunius\NumeroALetras\Exceptions;

use Aeunius\NumeroALetras\Support\Palabras;
use InvalidArgumentException;

final class NumeroFueraDeRango extends InvalidArgumentException
{
    public static function negativo(int|float|string $numero): self
    {
        return new self("No se pueden convertir números negativos: {$numero}.");
    }

    public static function demasiadoGrande(int|float|string $numero): self
    {
        $maximo = number_format(Palabras::MAXIMO, 0, '', ' ');

        return new self("El número {$numero} supera el máximo que se puede convertir ({$maximo},99).");
    }
}
