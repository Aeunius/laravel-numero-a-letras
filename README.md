# Laravel Número a Letras

Convierte montos a letras con el formato de los comprobantes peruanos de la
SUNAT: `1250.50` → `MIL DOSCIENTOS CINCUENTA CON 50/100 SOLES`.

El núcleo es PHP puro y funciona sin Laravel; la integración con Laravel
(facade, helper y configuración) es opcional.

[![tests](https://github.com/Aeunius/laravel-numero-a-letras/actions/workflows/tests.yml/badge.svg)](https://github.com/Aeunius/laravel-numero-a-letras/actions/workflows/tests.yml)
[![Versión en Packagist](https://img.shields.io/packagist/v/aeunius/laravel-numero-a-letras.svg)](https://packagist.org/packages/aeunius/laravel-numero-a-letras)
[![Licencia](https://img.shields.io/packagist/l/aeunius/laravel-numero-a-letras.svg)](LICENSE.md)

## Requisitos

- PHP 8.2 o superior, con la extensión `mbstring`
- Laravel 12 o 13 (opcional)

## Instalación

```bash
composer require aeunius/laravel-numero-a-letras
```

## Uso

```php
use Aeunius\NumeroALetras\Support\Conversor;

$conversor = new Conversor;

$conversor->convertir(1250.50);      // "MIL DOSCIENTOS CINCUENTA CON 50/100 SOLES"
$conversor->convertir('1250.50');    // lo mismo
$conversor->convertir(0.5);          // "CERO CON 50/100 SOLES"
$conversor->convertir(21);           // "VEINTIUNO CON 00/100 SOLES"
$conversor->convertir(21000);        // "VEINTIÚN MIL CON 00/100 SOLES"
$conversor->convertir(1000000);      // "UN MILLÓN CON 00/100 SOLES"
```

Si solo necesitas las palabras de un entero, en minúsculas:

```php
use Aeunius\NumeroALetras\Support\Palabras;

Palabras::deEntero(1250);                 // "mil doscientos cincuenta"
Palabras::deEntero(21);                   // "veintiuno"
Palabras::deEntero(21, apocope: true);    // "veintiún", para usarlo delante de un sustantivo
```

## Reglas

| Caso | Resultado |
|---|---|
| Centavos | Siempre dos dígitos como fracción: `CON 05/100` |
| Cero | `0.50` → `CERO CON 50/100 SOLES` |
| Cien | `CIEN`, pero `CIENTO UNO` |
| Mil | `MIL`, no `UN MIL` |
| "Uno" al final | `VEINTIUNO CON 00/100 SOLES`: en este formato la moneda va después de la fracción, así que el número no se apocopa |
| "Uno" delante de mil, millón o billón | `VEINTIÚN MIL`, `UN MILLÓN`, `TREINTA Y UN MILLONES` |
| Escala | Larga, la del español: `MIL MILLONES` (10⁹), `UN BILLÓN` (10¹²) |
| Tildes | Se escriben: `DIECISÉIS`, `VEINTIDÓS`, `MILLÓN` |

### Qué acepta

- `int`, `float` o `string` con dígitos y, si hace falta, un punto decimal:
  `1250`, `1250.5`, `'1250.50'`, `' 1250.50 '`.
- No acepta separadores de miles (`'1,250.50'`), comas decimales, signos ni
  notación científica en texto. Lanza `NumeroNoValido`.
- Los negativos lanzan `NumeroFueraDeRango`.

### Redondeo

A dos decimales, con el medio hacia arriba: `1.005` → `UNO CON 01/100`,
`0.995` → `UNO CON 00/100`.

Un `float` se redondea tal como se escribió, no como lo guarda la máquina: para
PHP `1.005` es en realidad `1.00499999…`, pero aquí se toma como `1.005`.

### Máximo

`999 999 999 999 999,99` (justo debajo de mil billones). Por encima se lanza
`NumeroFueraDeRango`.

Un `float` pierde precisión en los centavos a partir de unos 15 dígitos en
total. Para montos tan grandes, pasa el monto como `string`.

## Pruebas

Todo corre en Docker; no hace falta PHP instalado.

```bash
make install
make test
make analyse
```

## Licencia

MIT. Ver [LICENSE.md](LICENSE.md).
