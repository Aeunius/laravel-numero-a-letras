<?php

namespace Aeunius\NumeroALetras\Support;

use Aeunius\NumeroALetras\Exceptions\NumeroFueraDeRango;

/**
 * Enteros a palabras en español, en minúsculas y con escala larga
 * (millón = 10^6, billón = 10^12).
 */
final class Palabras
{
    /** Justo debajo de mil billones. */
    public const MAXIMO = 999_999_999_999_999;

    private const BASICOS = [
        'cero', 'uno', 'dos', 'tres', 'cuatro', 'cinco', 'seis', 'siete', 'ocho', 'nueve',
        'diez', 'once', 'doce', 'trece', 'catorce', 'quince', 'dieciséis', 'diecisiete', 'dieciocho', 'diecinueve',
        'veinte', 'veintiuno', 'veintidós', 'veintitrés', 'veinticuatro', 'veinticinco', 'veintiséis', 'veintisiete', 'veintiocho', 'veintinueve',
    ];

    private const DECENAS = [
        3 => 'treinta', 4 => 'cuarenta', 5 => 'cincuenta', 6 => 'sesenta',
        7 => 'setenta', 8 => 'ochenta', 9 => 'noventa',
    ];

    private const CENTENAS = [
        1 => 'ciento', 2 => 'doscientos', 3 => 'trescientos', 4 => 'cuatrocientos', 5 => 'quinientos',
        6 => 'seiscientos', 7 => 'setecientos', 8 => 'ochocientos', 9 => 'novecientos',
    ];

    /**
     * Con $apocope, el número termina en "un" o "veintiún" en lugar de "uno" o
     * "veintiuno", como corresponde delante de un sustantivo ("veintiún soles").
     * Delante de "mil", "millones" y "billones" se aplica siempre.
     */
    public static function deEntero(int $numero, bool $apocope = false): string
    {
        if ($numero < 0) {
            throw NumeroFueraDeRango::negativo($numero);
        }

        if ($numero > self::MAXIMO) {
            throw NumeroFueraDeRango::demasiadoGrande($numero);
        }

        if ($numero === 0) {
            return 'cero';
        }

        $billones = intdiv($numero, 10 ** 12);
        $millones = intdiv($numero % 10 ** 12, 10 ** 6);
        $resto = $numero % 10 ** 6;

        return self::unir(
            self::escala($billones, 'un billón', 'billones'),
            self::escala($millones, 'un millón', 'millones'),
            $resto > 0 ? self::menorQueUnMillon($resto, $apocope) : '',
        );
    }

    private static function escala(int $cantidad, string $singular, string $plural): string
    {
        return match (true) {
            $cantidad === 0 => '',
            $cantidad === 1 => $singular,
            default => self::menorQueUnMillon($cantidad, true).' '.$plural,
        };
    }

    private static function menorQueUnMillon(int $numero, bool $apocope): string
    {
        $miles = intdiv($numero, 1000);
        $resto = $numero % 1000;

        return self::unir(
            match (true) {
                $miles === 0 => '',
                $miles === 1 => 'mil',
                default => self::menorQueMil($miles, true).' mil',
            },
            $resto > 0 ? self::menorQueMil($resto, $apocope) : '',
        );
    }

    private static function menorQueMil(int $numero, bool $apocope): string
    {
        if ($numero === 100) {
            return 'cien';
        }

        $centenas = intdiv($numero, 100);
        $resto = $numero % 100;

        return self::unir(
            $centenas > 0 ? self::CENTENAS[$centenas] : '',
            $resto > 0 ? self::menorQueCien($resto, $apocope) : '',
        );
    }

    private static function menorQueCien(int $numero, bool $apocope): string
    {
        if ($numero < 30) {
            return match (true) {
                $apocope && $numero === 1 => 'un',
                $apocope && $numero === 21 => 'veintiún',
                default => self::BASICOS[$numero],
            };
        }

        $decena = self::DECENAS[intdiv($numero, 10)];
        $unidad = $numero % 10;

        return match (true) {
            $unidad === 0 => $decena,
            $apocope && $unidad === 1 => $decena.' y un',
            default => $decena.' y '.self::BASICOS[$unidad],
        };
    }

    private static function unir(string ...$partes): string
    {
        return implode(' ', array_filter($partes, fn (string $parte) => $parte !== ''));
    }
}
