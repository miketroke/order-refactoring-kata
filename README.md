# Order Refactoring Kata

Código funcional pero deliberadamente mal diseñado. Tu objetivo es refactorizarlo sin cambiar su comportamiento.

## Objetivos
Practica:
- SRP
- nombres
- métodos pequeños
- reducción de condicionales
- acoplamiento
- extracción de clases
- dependencias
- testabilidad

## Regla principal
Los tests ya están verdes. No cambies inicialmente los tests ni añadas funcionalidad.

Haz un refactor pequeño -> ejecuta PHPUnit -> repite.

## Inicio
```bash
composer install
vendor/bin/phpunit
```

Primero lee `src/OrderService.php` y detecta problemas antes de modificarlo.

No hay una solución única. La intención es hacer code review de tus decisiones.
