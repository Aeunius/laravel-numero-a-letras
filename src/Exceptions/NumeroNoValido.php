<?php

namespace Aeunius\NumeroALetras\Exceptions;

use InvalidArgumentException;

final class NumeroNoValido extends InvalidArgumentException
{
    public static function desde(float|string $valor): self
    {
        $texto = is_string($valor) ? "\"{$valor}\"" : var_export($valor, true);

        return new self("No es un número válido: {$texto}. Usa dígitos y, si hace falta, un punto decimal (1250.50).");
    }
}
