<?php

namespace Aeunius\NumeroALetras\Contracts;

/**
 * Nombres de una moneda, en minúsculas y con tildes. El conversor aplica las
 * mayúsculas cuando corresponde.
 *
 * Implementa esta interfaz para usar una moneda que el enum Moneda no trae.
 */
interface Moneda
{
    /** "sol", "dólar americano" */
    public function singular(): string;

    /** "soles", "dólares americanos" */
    public function plural(): string;

    /** Si el nombre es femenino ("una libra", "doscientas libras"). */
    public function femenina(): bool;

    /** "céntimo", "centavo". Se asume masculino. */
    public function centavoSingular(): string;

    /** "céntimos", "centavos" */
    public function centavoPlural(): string;
}
