# Guía de iteraciones — Same App, Two Stacks

Este documento guía los próximos sprints del repo. El objetivo no es definir un ganador entre Symfony y Spring Boot, sino mostrar cómo se resuelve la misma necesidad funcional en dos frameworks distintos, manteniendo el mismo resultado visible.

## Principio del proyecto

Cada sprint debe partir de una misma pregunta:

> ¿Cómo se implementa esta misma feature en Symfony y en Spring Boot, respetando el estilo natural de cada framework?

La comparación debe ser técnica y didáctica. Evitar lenguaje de rivalidad como "vence", "mejor absoluto" o "duelo". Preferir expresiones como:

- side by side
- misma app, dos stacks
- similitudes y diferencias
- decisiones de arquitectura
- tradeoffs

## Regla de paridad

Toda iteración debe mantener un contrato de paridad explícito.

Antes de implementar, definir:

- Qué pantalla o flujo debe verse igual.
- Qué datos deben coincidir.
- Qué cálculos o estados deben dar el mismo resultado.
- Qué diferencias son aceptables por framework.
- Qué tests verifican el contrato.

Si cambia la funcionalidad, se actualizan ambos lados en el mismo sprint. No se deja una feature solo en Symfony o solo en Spring Boot, salvo que el sprint sea explícitamente exploratorio y quede documentado.

## Definición de un sprint listo

Un sprint se considera listo cuando:

- `docker compose up --build` levanta ambos servicios.
- Symfony responde en su puerto documentado.
- Spring Boot responde en su puerto documentado.
- Ambas apps muestran el mismo contrato funcional.
- Los tests de Symfony pasan.
- Los tests de Spring Boot pasan.
- README queda actualizado si cambian comandos, puertos, stack o contrato.
- No se agregan secretos ni configuraciones locales reales.
- El post del blog, si aplica, describe lo implementado y no promete features futuras como si ya existieran.

## Checklist antes de tocar código

Para cada sprint, responder:

- ¿Cuál es la feature concreta?
- ¿Cuál es el comportamiento visible esperado?
- ¿Qué parte corresponde al dominio?
- ¿Qué parte corresponde al framework?
- ¿Qué archivo o capa cambia en Symfony?
- ¿Qué archivo o capa cambia en Spring Boot?
- ¿Qué test falla primero o qué test nuevo valida la feature?
- ¿El cambio requiere base de datos, cola, caché, configuración o dependencia nueva?
- ¿Hay impacto en Docker, README o comandos?

## Checklist de cierre

Antes de cerrar una iteración:

```bash
docker compose up --build
docker compose exec symfony vendor/bin/phpunit
docker run --rm \
    -v "$PWD/springboot":/build -w /build \
    maven:3.9-eclipse-temurin-21 \
    mvn -B -ntp test
```

Validar también en navegador:

- `http://localhost:8094`
- `http://localhost:8095`

Comparar visualmente:

- título
- datos
- estados
- acciones disponibles
- mensajes de error o éxito
- cálculos

## Línea editorial

El repo y los posts asociados deben mantener este enfoque:

- No declarar un ganador absoluto.
- No forzar que ambos frameworks usen el mismo patrón si no es natural.
- Mostrar el código idiomático de cada ecosistema.
- Explicar tradeoffs concretos.
- Separar opinión de evidencia.
- Cerrar cada sprint con una conclusión acotada al caso implementado.

Ejemplo de cierre correcto:

> En esta feature, Symfony concentra la configuración en bundles y servicios, mientras que Spring Boot usa autoconfiguración y beans. Ambos llegan al mismo resultado, pero empujan decisiones distintas sobre convención, explicitud y extensión.

Evitar cierres como:

> Symfony es mejor.
> Spring Boot gana.
> Este framework es siempre más simple.

## Roadmap sugerido

### Sprint 1 — Base side by side

Estado: implementado.

Objetivo:

- Docker con Symfony y Spring Boot en paralelo.
- Pantalla única con datos hardcodeados.
- Contrato de paridad documentado.
- Tests smoke por framework.

### Sprint 2 — Persistencia simple

Objetivo:

- Agregar MySQL compartido o esquemas separados.
- Persistir los sprint items.
- Mantener la misma pantalla y los mismos cálculos.

Symfony:

- Doctrine ORM.
- Entidad.
- Repository.
- Fixtures o migración inicial.

Spring Boot:

- Spring Data JPA.
- Entity.
- Repository.
- Data initializer o migración inicial.

Validaciones:

- Ambos lados leen datos persistidos.
- Reinicio de contenedores mantiene datos si no se borra volumen.
- `docker compose down -v` reconstruye estado inicial.

### Sprint 3 — Puertos y arquitectura hexagonal

Objetivo:

- Desacoplar el caso de uso de la persistencia.
- Mantener el mismo comportamiento visible.

Modelo esperado:

- Dominio sin dependencia directa del framework.
- Caso de uso o servicio de aplicación.
- Puerto de repositorio.
- Adaptador Doctrine en Symfony.
- Adaptador Spring Data/JPA en Spring Boot.

Validaciones:

- Test del caso de uso con repositorio fake/in memory.
- Test de controlador o integración por framework.
- README explica que se compara arquitectura, no solo ORM.

### Sprint 4 — Bundle Symfony vs starter Spring Boot

Objetivo:

- Mostrar cómo empaquetar una capacidad reusable en cada ecosistema.
- La feature visible debe ser la misma.

Symfony:

- Bundle.
- Extensión de configuración.
- Servicios registrados por DI.
- Configuración propia si aplica.

Spring Boot:

- Starter o módulo.
- AutoConfiguration.
- Properties.
- Beans condicionales si aplica.

Ideas de feature:

- Feature flag simple.
- Badge de estado configurable.
- Servicio de auditoría de eventos.
- Calculadora de progreso configurable.

Validaciones:

- La feature puede activarse/configurarse desde cada stack.
- El resultado visual sigue siendo equivalente.
- Tests cubren configuración por defecto y configuración custom.

### Sprint 5 — Validación

Objetivo:

- Agregar entrada de usuario o comando simple.
- Validar reglas iguales en ambos frameworks.

Symfony:

- Symfony Validator.
- Form o DTO validado.
- Mensajes de error.

Spring Boot:

- Bean Validation / Jakarta Validation.
- DTO.
- Binding y mensajes de error.

Validaciones:

- Mismos campos válidos.
- Mismos casos inválidos.
- Mismos mensajes visibles o equivalentes.

### Sprint 6 — Mensajería o tareas async

Objetivo:

- Ejecutar una acción diferida manteniendo feedback visible equivalente.

Symfony:

- Messenger.
- Handler.
- Transporte sync o async según alcance.

Spring Boot:

- `@Async`, eventos o cola según alcance.
- Handler/listener equivalente.

Validaciones:

- La acción se dispara desde el mismo flujo.
- El usuario ve el mismo estado.
- Tests cubren dispatch o ejecución del handler.

## Cómo decidir si una diferencia es aceptable

Una diferencia es aceptable si:

- Es idiomática del framework.
- No cambia el resultado visible.
- Está explicada en README o en el post.
- No rompe el contrato de paridad.

Una diferencia no es aceptable si:

- Hace que una app tenga una feature que la otra no tiene.
- Cambia datos, cálculos o estados visibles.
- Evita tests equivalentes sin justificación.
- Introduce una dependencia externa innecesaria en un solo lado.

## Tests esperados por tipo de cambio

Pantalla o controlador:

- Symfony: `WebTestCase`.
- Spring Boot: `MockMvc`.

Dominio o caso de uso:

- Symfony: PHPUnit unitario.
- Spring Boot: JUnit unitario.

Persistencia:

- Symfony: test de repositorio o integración con kernel.
- Spring Boot: `@DataJpaTest` o test de integración.

Configuración reusable:

- Symfony: test de extensión/bundle o container.
- Spring Boot: test de auto configuration/context.

## Documentación por sprint

Cada sprint debe actualizar:

- README: contrato actual, comandos nuevos, puertos o dependencias.
- Tests: contrato automatizado.
- Post del blog: solo cuando la implementación ya esté validada.

Si el repo ya está publicado, actualizar también el estado del portfolio cuando corresponda.

## Reglas de publicación

- No publicar en GitHub sin confirmación explícita.
- No pushear cambios del blog sin confirmación explícita.
- No incluir `.env` reales, tokens, claves ni datos privados.
- Mantener el repo autocontenido: clone, Docker, run.

## Plantilla para proponer un sprint

```md
## Sprint N — Nombre

Objetivo:
- ...

Contrato de paridad:
- ...

Symfony:
- ...

Spring Boot:
- ...

Tests:
- ...

README/Post:
- ...

Riesgos:
- ...
```
