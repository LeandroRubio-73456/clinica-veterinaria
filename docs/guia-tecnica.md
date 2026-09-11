# Guía técnica del sistema

## 1. Arquitectura

La aplicación utiliza el patrón MVC de Laravel:

```text
Navegador
   ↓
Rutas web
   ↓
Middleware de autenticación y roles
   ↓
Controllers / Policies / Form Requests
   ↓
Models Eloquent y Services
   ↓
Base de datos MySQL
```

Las vistas Blade se organizan por módulo en `resources/views`. Los componentes reutilizables se
encuentran en `resources/views/components`, especialmente los layouts y componentes de interfaz bajo
`resources/views/components/ui`.

## 2. Flujo de una cirugía

### Programación

`SurgeryController::store` recibe los datos validados por `StoreSurgeryRequest`, inicia una transacción,
bloquea los recursos seleccionados y comprueba solapamientos de horario. Solo después crea la cirugía
con estado `scheduled`.

### Actualización

`SurgeryController::update` repite la validación dentro de una transacción y excluye la cirugía que se
está editando para no detectarla como su propio conflicto.

### Ejecución

- `start`: cambia a `in_progress`, registra `actual_start_time` y ocupa el quirófano.
- `complete`: cambia a `completed`, registra `actual_end_time` y libera el quirófano.
- `cancel`: cambia a `cancelled` y libera el quirófano.
- `noShow`: cambia a `no_show` y libera el quirófano.

Las transiciones también están protegidas por `SurgeryPolicy`.

## 3. Seguridad

- Las rutas internas usan `auth` y `verified`.
- `RoleMiddleware` limita módulos completos por rol.
- Las policies controlan operaciones específicas de una cirugía.
- Los Form Requests validan entradas antes de llegar al controller.
- Blade escapa las variables mostradas por defecto.
- Los formularios usan protección CSRF de Laravel.

## 4. Conflictos de programación

La tabla `scheduling_conflicts` registra únicamente intentos rechazados. Esto es importante porque una
cirugía conflictiva no debe guardarse como una cirugía válida, pero sí conviene conservar evidencia de
que la regla de negocio intervino.

El registro se realiza después de que la transacción de programación se revierte. Así el evento de
conflicto no desaparece junto con la operación fallida.

## 5. Reportes

`ReportService::build` concentra los cálculos de indicadores y recibe un intervalo `from`/`to`.
La vista de reportes no recalcula métricas: únicamente presenta los datos preparados por el servicio.
Esto evita duplicar reglas de negocio en Blade.

### Interpretación de indicadores

| Indicador | Interpretación |
|---|---|
| Completadas por día | Procedimientos con estado `completed`, agrupados por fecha. |
| Inicio a tiempo | Cirugías iniciadas dentro de la tolerancia configurada. |
| Ocupación | Minutos reales de cirugía frente a minutos operativos disponibles. |
| Tiempo de programación | Tiempo desde `created_at` hasta la fecha/hora programada; no es todavía el tiempo clínico total de espera. |
| Conflictos rechazados | Intentos de programación que el sistema impidió guardar por solapamiento. |

## 6. Base de datos

Las migraciones son la fuente de verdad del esquema. El contexto funcional mantiene un inventario
resumido de tablas y relaciones. Cuando se cambie una migración o se agregue una entidad, deben
actualizarse simultáneamente:

1. La migración.
2. El modelo Eloquent.
3. Las relaciones y reglas de autorización.
4. Los Form Requests.
5. Las vistas y reportes afectados.
6. Esta guía.

## 7. Escenario operativo reproducible

`OperationalScenarioSeeder` puede ejecutarse directamente o mediante `DatabaseSeeder`. Se utiliza para
preparar una revisión operativa o evaluación controlada. Genera nombres, horarios y estados plausibles,
pero no representa información histórica real. La marca técnica `source = scenario` permite regenerar
los conflictos sin duplicarlos.

## 8. Criterio de documentación del código

El código debe documentarse en español cuando la explicación se refiera al negocio de la clínica. Los
nombres técnicos permanecen en inglés para mantener las convenciones de Laravel y PHP.

Se documentan especialmente:

- Reglas de negocio.
- Transiciones de estado.
- Cálculos de indicadores.
- Decisiones de seguridad.
- Motivos de transacciones y bloqueos.
- Seeders que generan datos no reales.

No se comentan operaciones obvias como asignaciones simples o consultas autoexplicativas.

## 9. Pendientes técnicos conocidos

- La copia de trabajo debe incluir `artisan`, `bootstrap`, `public`, `storage`, `vendor` y la
  configuración de Docker para ejecutar una verificación completa.
- Algunas migraciones incluyen `deleted_at`, pero no todos los modelos utilizan `SoftDeletes`; debe
  tomarse una decisión uniforme antes de basar nuevas reglas en borrado lógico.
- Si se necesita medir espera desde la solicitud real del propietario, debe añadirse un campo como
  `requested_at` en lugar de utilizar únicamente `created_at`.
