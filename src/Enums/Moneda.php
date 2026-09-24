<?php

namespace Aeunius\NumeroALetras\Enums;

use Aeunius\NumeroALetras\Contracts\Moneda as MonedaContract;
use Aeunius\NumeroALetras\Exceptions\OpcionNoValida;

/**
 * Monedas incluidas, por su código ISO 4217 (el mismo del catálogo 02 de la SUNAT).
 */
enum Moneda: string implements MonedaContract
{
    case PEN = 'PEN';
    case USD = 'USD';
    case EUR = 'EUR';

    public static function desdeCodigo(string $codigo): self
    {
        return self::tryFrom(strtoupper(trim($codigo))) ?? throw OpcionNoValida::moneda($codigo);
    }

    public function singular(): string
    {
        return match ($this) {
            self::PEN => 'sol',
            self::USD => 'dólar americano',
            self::EUR => 'euro',
        };
    }

    public function plural(): string
    {
        return match ($this) {
            self::PEN => 'soles',
            self::USD => 'dólares americanos',
            self::EUR => 'euros',
        };
    }

    public function femenina(): bool
    {
        return false;
    }

    public function centavoSingular(): string
    {
        return match ($this) {
            self::PEN, self::EUR => 'céntimo',
            self::USD => 'centavo',
        };
    }

    public function centavoPlural(): string
    {
        return $this->centavoSingular().'s';
    }
}
