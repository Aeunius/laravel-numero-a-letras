# Changelog

Todos los cambios importantes de este paquete se registran aquí.

El formato sigue [Keep a Changelog](https://keepachangelog.com/es-ES/1.1.0/) y el
proyecto usa [versionado semántico](https://semver.org/lang/es/).

## [Sin publicar]

## [1.0.0] - 2026-09-23

Primera versión estable: la API pública queda fija hasta la 2.0.

### Agregado

- Integración con Laravel 12 y 13: `NumeroALetrasServiceProvider` (se registra
  solo), facade `NumeroALetras` y configuración publicable con
  `php artisan vendor:publish --tag=numero-a-letras-config`.
- La moneda por defecto de la configuración acepta un código ISO o una clase
  propia que implemente `Contracts\Moneda`.
- Helper global `numero_a_letras()`, que usa la configuración dentro de Laravel
  y las opciones por defecto fuera de él.

## [0.2.0] - 2026-09-23

Primera versión publicada.

### Agregado

- `Support\Conversor`: convierte un monto a letras con el formato de la SUNAT
  (`MIL DOSCIENTOS CINCUENTA CON 50/100 SOLES`).
- `Support\Palabras::deEntero()`: enteros de cero a 999 999 999 999 999 en
  palabras, con apócope opcional (`veintiún`, `un`).
- `Support\Monto`: lee `int`, `float` o `string` y redondea a dos decimales con
  el medio hacia arriba.
- Excepciones `NumeroFueraDeRango` (negativos y desde mil billones) y
  `NumeroNoValido` (texto que no es un número, `INF`, `NAN`).
- Enum `Moneda` con soles (`PEN`), dólares (`USD`) y euros (`EUR`), y la
  interfaz `Contracts\Moneda` para usar cualquier otra.
- Opciones del conversor: `moneda()`, `soles()`, `dolares()`, `euros()`,
  `mayusculas()`, `minusculas()`, `soloTexto()`, `formatoCentavos()` y
  `conector()`.
- Formato de centavos en texto: `UN SOL`, `VEINTIÚN SOLES CON VEINTIÚN
  CÉNTIMOS`, `UN MILLÓN DE SOLES`.
- `Palabras::deEntero()` acepta `femenino: true` para monedas femeninas
  (`VEINTIUNA LIBRAS`, `DOSCIENTAS MIL LIBRAS`).
- Excepción `OpcionNoValida` para monedas, formatos o conectores desconocidos.

[Sin publicar]: https://github.com/Aeunius/laravel-numero-a-letras/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/Aeunius/laravel-numero-a-letras/compare/v0.2.0...v1.0.0
[0.2.0]: https://github.com/Aeunius/laravel-numero-a-letras/releases/tag/v0.2.0
