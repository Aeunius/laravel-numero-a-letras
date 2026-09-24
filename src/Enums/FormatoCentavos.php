<?php

namespace Aeunius\NumeroALetras\Enums;

enum FormatoCentavos: string
{
    /** "CON 50/100 SOLES", el de los comprobantes de la SUNAT. */
    case Fraccion = 'fraccion';

    /** "SOLES CON CINCUENTA CÉNTIMOS" */
    case Texto = 'texto';
}
