<?php

namespace Aeunius\NumeroALetras\Exceptions;

use InvalidArgumentException;

final class NumeroFueraDeRango extends InvalidArgumentException
{
    public static function negativo(int|float|string $numero): self
    {
        return new self("No se pueden convertir números negativos: {$numero}.");
    }
}
