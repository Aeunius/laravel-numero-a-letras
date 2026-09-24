<?php

namespace Aeunius\NumeroALetras\Support;

use Aeunius\NumeroALetras\Exceptions\NumeroFueraDeRango;
use Aeunius\NumeroALetras\Exceptions\NumeroNoValido;

/**
 * Un monto no negativo separado en parte entera y centavos, redondeado a dos
 * decimales con el medio hacia arriba (1.005 → 1.01).
 *
 * Los float se leen por su representación más corta (la misma que muestra
 * var_export), así 1.005 se redondea como se escribió y no como se guarda en
 * binario. Para montos con más de 15 cifras conviene pasar un string: un float
 * ya no tiene precisión suficiente para los centavos.
 */
final readonly class Monto
{
    private function __construct(
        public int $entero,
        public int $centavos,
    ) {}

    public static function desde(int|float|string $valor): self
    {
        if (is_int($valor)) {
            return self::desdeTexto((string) $valor, $valor);
        }

        if (is_float($valor)) {
            if (! is_finite($valor)) {
                throw NumeroNoValido::desde($valor);
            }

            if ($valor < 0) {
                throw NumeroFueraDeRango::negativo($valor);
            }

            $texto = var_export(abs($valor), true);

            // Notación científica: 1.0E+20 o 5.0E-5.
            if (str_contains($texto, 'E')) {
                $texto = sprintf('%.3F', $valor);
            }

            return self::desdeTexto($texto, $valor);
        }

        return self::desdeTexto(trim($valor), $valor);
    }

    private static function desdeTexto(string $texto, int|float|string $original): self
    {
        if (str_starts_with($texto, '-')) {
            throw NumeroFueraDeRango::negativo($original);
        }

        if (preg_match('/^(\d+)(?:\.(\d+))?$/', $texto, $partes) !== 1) {
            throw NumeroNoValido::desde($original);
        }

        $decimales = str_pad($partes[2] ?? '', 3, '0');
        $centavos = (int) substr($decimales, 0, 2) + ($decimales[2] >= '5' ? 1 : 0);

        $digitos = ltrim($partes[1], '0');

        if (strlen($digitos) > strlen((string) Palabras::MAXIMO)) {
            throw NumeroFueraDeRango::demasiadoGrande($original);
        }

        $entero = (int) $digitos;

        if ($centavos === 100) {
            $entero++;
            $centavos = 0;
        }

        if ($entero > Palabras::MAXIMO) {
            throw NumeroFueraDeRango::demasiadoGrande($original);
        }

        return new self($entero, $centavos);
    }
}
