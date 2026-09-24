<?php

namespace Aeunius\NumeroALetras\Support;

final class Conversor
{
    /**
     * 1250.50 → "MIL DOSCIENTOS CINCUENTA CON 50/100 SOLES"
     */
    public function convertir(int|float|string $monto): string
    {
        $monto = Monto::desde($monto);

        return mb_strtoupper(sprintf(
            '%s con %02d/100 soles',
            Palabras::deEntero($monto->entero),
            $monto->centavos,
        ));
    }
}
