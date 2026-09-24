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

### Opciones

El conversor es inmutable: cada opción devuelve uno nuevo y el original no cambia.

```php
$conversor->moneda('USD')->convertir(99.90);
// "NOVENTA Y NUEVE CON 90/100 DÓLARES AMERICANOS"

$conversor->soles()->minusculas()->convertir(5);
// "cinco con 00/100 soles"

$conversor->soloTexto()->convertir(21);
// "VEINTIUNO"

$conversor->formatoCentavos('texto')->convertir(1250.50);
// "MIL DOSCIENTOS CINCUENTA SOLES CON CINCUENTA CÉNTIMOS"

$conversor->conector('y')->convertir(1858.59);
// "MIL OCHOCIENTOS CINCUENTA Y OCHO Y 59/100 SOLES"
```

| Opción | Efecto |
|---|---|
| `moneda(string\|Moneda)` | Código ISO 4217 (`'PEN'`, `'USD'`, `'EUR'`), el enum `Enums\Moneda` o una moneda propia. Por defecto, soles |
| `soles()`, `dolares()`, `euros()` | Atajos de `moneda()` |
| `mayusculas()` / `minusculas()` | Por defecto, mayúsculas |
| `soloTexto()` | Solo la parte entera en palabras, sin moneda ni centavos. Descarta los decimales después de redondear: `21.75` → `VEINTIUNO` |
| `formatoCentavos('fraccion'\|'texto')` | `CON 50/100 SOLES` (por defecto) o `SOLES CON CINCUENTA CÉNTIMOS` |
| `conector('con'\|'y')` | La palabra entre el entero y los centavos. Por defecto, `con` |

### Monedas

| Código | Singular | Plural | Centavos |
|---|---|---|---|
| `PEN` | SOL | SOLES | CÉNTIMOS |
| `USD` | DÓLAR AMERICANO | DÓLARES AMERICANOS | CENTAVOS |
| `EUR` | EURO | EUROS | CÉNTIMOS |

Para otra moneda, implementa `Contracts\Moneda`:

```php
use Aeunius\NumeroALetras\Contracts\Moneda;

final class LibraEsterlina implements Moneda
{
    public function singular(): string { return 'libra esterlina'; }
    public function plural(): string { return 'libras esterlinas'; }
    public function femenina(): bool { return true; }
    public function centavoSingular(): string { return 'penique'; }
    public function centavoPlural(): string { return 'peniques'; }
}

$conversor->moneda(new LibraEsterlina)->formatoCentavos('texto')->convertir(21.01);
// "VEINTIUNA LIBRAS ESTERLINAS CON UN PENIQUE"
```

### Solo las palabras

Si solo necesitas las palabras de un entero, en minúsculas:

```php
use Aeunius\NumeroALetras\Support\Palabras;

Palabras::deEntero(1250);                        // "mil doscientos cincuenta"
Palabras::deEntero(21, apocope: true);           // "veintiún", delante de un sustantivo
Palabras::deEntero(200201, femenino: true);      // "doscientas mil doscientas una"
```

## Reglas

| Caso | Resultado |
|---|---|
| Centavos | Siempre dos dígitos como fracción: `CON 05/100` |
| Cero | `0.50` → `CERO CON 50/100 SOLES` |
| Cien | `CIEN`, pero `CIENTO UNO` |
| Mil | `MIL`, no `UN MIL` |
| "Uno" al final, en fracción | `VEINTIUNO CON 00/100 SOLES`: la moneda va después de la fracción, así que el número no se apocopa |
| "Uno" delante de la moneda, en texto | `UN SOL`, `VEINTIÚN SOLES`, `UN DÓLAR AMERICANO` |
| "Uno" delante de mil, millón o billón | `VEINTIÚN MIL`, `UN MILLÓN`, `TREINTA Y UN MILLONES` |
| Millón o billón exactos, en texto | `UN MILLÓN DE SOLES`, pero `UN MILLÓN CIEN SOLES` |
| Moneda femenina, en texto | `VEINTIUNA LIBRAS`, `DOSCIENTAS MIL LIBRAS`, pero `DOSCIENTOS MILLONES DE LIBRAS` (concuerda con "millón") |
| Escala | Larga, la del español: `MIL MILLONES` (10⁹), `UN BILLÓN` (10¹²) |
| Tildes | Se escriben: `DIECISÉIS`, `VEINTIDÓS`, `MILLÓN` |

### Qué acepta

- `int`, `float` o `string` con dígitos y, si hace falta, un punto decimal:
  `1250`, `1250.5`, `'1250.50'`, `' 1250.50 '`.
- No acepta separadores de miles (`'1,250.50'`), comas decimales, signos ni
  notación científica en texto. Lanza `NumeroNoValido`.
- Los negativos lanzan `NumeroFueraDeRango`. Una opción inválida (moneda,
  formato o conector desconocidos) lanza `OpcionNoValida`.

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

## En el comprobante electrónico

El monto en letras va en la leyenda con código `1000` del catálogo 52
(`<cbc:Note languageLocaleID="1000">`). La SUNAT no fija la redacción: la leyenda
es opcional y sus propias guías usan tanto `CON 59/100 Soles` como `Y 00/100`.
Por eso el conector se puede cambiar.

Lo que sí fija es el largo: `cbc:Note` admite **hasta 100 caracteres**. Hasta
999 999,99 el texto nunca pasa de 86, pero desde las decenas de millones
puede superarlo (`777777777.77` da 113 caracteres, y los dólares suman 13 más
que los soles). El paquete no recorta el texto, porque un monto cortado sería
un monto equivocado: si emites comprobantes por esos montos, comprueba el largo
antes de enviarlo.

## Pruebas

Todo corre en Docker; no hace falta PHP instalado.

```bash
make install
make test
make analyse
```

## Licencia

MIT. Ver [LICENSE.md](LICENSE.md).
