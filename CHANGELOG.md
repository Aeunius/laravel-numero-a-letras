# Changelog

Todos los cambios importantes de este paquete se registran aquí.

El formato sigue [Keep a Changelog](https://keepachangelog.com/es-ES/1.1.0/) y el
proyecto usa [versionado semántico](https://semver.org/lang/es/).

## [Sin publicar]

### Agregado

- `Support\Conversor`: convierte un monto a letras con el formato de la SUNAT
  (`MIL DOSCIENTOS CINCUENTA CON 50/100 SOLES`).
- `Support\Palabras::deEntero()`: enteros de cero a 999 999 999 999 999 en
  palabras, con apócope opcional (`veintiún`, `un`).
- `Support\Monto`: lee `int`, `float` o `string` y redondea a dos decimales con
  el medio hacia arriba.
- Excepciones `NumeroFueraDeRango` (negativos y desde mil billones) y
  `NumeroNoValido` (texto que no es un número, `INF`, `NAN`).
