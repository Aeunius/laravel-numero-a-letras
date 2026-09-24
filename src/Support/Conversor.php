<?php

namespace Aeunius\NumeroALetras\Support;

use Aeunius\NumeroALetras\Contracts\Moneda as MonedaContract;
use Aeunius\NumeroALetras\Enums\FormatoCentavos;
use Aeunius\NumeroALetras\Enums\Moneda;
use Aeunius\NumeroALetras\Exceptions\OpcionNoValida;

/**
 * Inmutable: cada opción devuelve un conversor nuevo.
 */
final class Conversor
{
    private MonedaContract $moneda = Moneda::PEN;

    private bool $mayusculas = true;

    private bool $soloTexto = false;

    private FormatoCentavos $formatoCentavos = FormatoCentavos::Fraccion;

    private string $conector = 'con';

    /**
     * 1250.50 → "MIL DOSCIENTOS CINCUENTA CON 50/100 SOLES"
     */
    public function convertir(int|float|string $monto): string
    {
        $monto = Monto::desde($monto);

        $texto = match (true) {
            $this->soloTexto => Palabras::deEntero($monto->entero),
            $this->formatoCentavos === FormatoCentavos::Texto => $this->enTexto($monto),
            default => $this->enFraccion($monto),
        };

        return $this->mayusculas ? mb_strtoupper($texto) : $texto;
    }

    public function moneda(string|MonedaContract $moneda): self
    {
        $copia = clone $this;
        $copia->moneda = is_string($moneda) ? Moneda::desdeCodigo($moneda) : $moneda;

        return $copia;
    }

    public function soles(): self
    {
        return $this->moneda(Moneda::PEN);
    }

    public function dolares(): self
    {
        return $this->moneda(Moneda::USD);
    }

    public function euros(): self
    {
        return $this->moneda(Moneda::EUR);
    }

    public function mayusculas(): self
    {
        $copia = clone $this;
        $copia->mayusculas = true;

        return $copia;
    }

    public function minusculas(): self
    {
        $copia = clone $this;
        $copia->mayusculas = false;

        return $copia;
    }

    /**
     * Solo la parte entera en palabras, sin moneda ni centavos: 21 → "VEINTIUNO".
     * Los decimales se descartan después de redondear a dos: 21.999 → "VEINTIDÓS".
     */
    public function soloTexto(bool $soloTexto = true): self
    {
        $copia = clone $this;
        $copia->soloTexto = $soloTexto;

        return $copia;
    }

    public function formatoCentavos(string|FormatoCentavos $formato): self
    {
        if (is_string($formato)) {
            $formato = FormatoCentavos::tryFrom($formato) ?? throw OpcionNoValida::formatoCentavos($formato);
        }

        $copia = clone $this;
        $copia->formatoCentavos = $formato;

        return $copia;
    }

    /**
     * La palabra que une la parte entera con los centavos: "con" (por defecto) o "y".
     */
    public function conector(string $conector): self
    {
        if (! in_array($conector, ['con', 'y'], true)) {
            throw OpcionNoValida::conector($conector);
        }

        $copia = clone $this;
        $copia->conector = $conector;

        return $copia;
    }

    /**
     * "mil doscientos cincuenta con 50/100 soles"
     */
    private function enFraccion(Monto $monto): string
    {
        return sprintf(
            '%s %s %02d/100 %s',
            Palabras::deEntero($monto->entero),
            $this->conector,
            $monto->centavos,
            $this->moneda->plural(),
        );
    }

    /**
     * "mil doscientos cincuenta soles con cincuenta céntimos"
     */
    private function enTexto(Monto $monto): string
    {
        $texto = implode(' ', [
            Palabras::deEntero($monto->entero, apocope: true, femenino: $this->moneda->femenina()),
            // "un millón de soles", pero "un millón cien soles".
            ...($monto->entero > 0 && $monto->entero % 1_000_000 === 0 ? ['de'] : []),
            $monto->entero === 1 ? $this->moneda->singular() : $this->moneda->plural(),
        ]);

        if ($monto->centavos === 0) {
            return $texto;
        }

        return implode(' ', [
            $texto,
            $this->conector,
            Palabras::deEntero($monto->centavos, apocope: true),
            $monto->centavos === 1 ? $this->moneda->centavoSingular() : $this->moneda->centavoPlural(),
        ]);
    }
}
