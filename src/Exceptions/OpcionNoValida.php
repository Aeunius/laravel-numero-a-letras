<?php

namespace Aeunius\NumeroALetras\Exceptions;

use Aeunius\NumeroALetras\Enums\FormatoCentavos;
use Aeunius\NumeroALetras\Enums\Moneda;
use InvalidArgumentException;

final class OpcionNoValida extends InvalidArgumentException
{
    public static function moneda(string $codigo): self
    {
        $codigos = implode(', ', array_column(Moneda::cases(), 'value'));

        return new self("La moneda \"{$codigo}\" no está incluida ({$codigos}). Para otra, pasa una clase que implemente Contracts\\Moneda.");
    }

    public static function formatoCentavos(string $formato): self
    {
        $formatos = implode(', ', array_column(FormatoCentavos::cases(), 'value'));

        return new self("El formato de centavos \"{$formato}\" no existe. Usa uno de: {$formatos}.");
    }

    public static function conector(string $conector): self
    {
        return new self("El conector \"{$conector}\" no es válido. Usa \"con\" o \"y\".");
    }
}
