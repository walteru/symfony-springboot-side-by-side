# Sprint 2 — Persistencia simple

Plan y **contrato de paridad** del Sprint 2. Se escribe ANTES de implementar (pedido
de la revisión): deja fijadas las decisiones de negocio que afectan Docker, fixtures,
tests y la narrativa del post. Para principios, reglas de paridad y checklists, ver
[`ITERACIONES.md`](ITERACIONES.md).

## Objetivo

Mover los **mismos** sprint items de hoy (hardcodeados en `SprintItemRepository`) a
persistencia en MySQL, idiomática en cada stack, **sin cambiar el resultado visible**:

- Symfony: Doctrine ORM (entidad + repository + migración/seed inicial).
- Spring Boot: Spring Data JPA (entity + repository + data initializer/seed inicial).

El caso de uso (cálculo de completion en runtime) y la pantalla no cambian: solo cambia
de dónde salen los datos.

## Decisiones definidas (antes de codear)

Las tres se declaran como supuesto técnico **razonable y reversible**. Si el coordinador
quiere otra cosa, se ajusta antes de la primera línea de código.

1. **Topología MySQL: una sola instancia (servicio `mysql` compartido), dos bases/esquemas
   separados** — `app_symfony` y `app_springboot`.
   - Por qué: mantiene clone & run con un único contenedor de base (coherente con "MySQL
     compartido" del README), pero cada framework gestiona su propio esquema y sus
     migraciones de forma idiomática, sin que la herramienta de migración de un stack sea
     dueña de las tablas del otro. Eso preserva la narrativa "misma app, dos stacks".
   - Reversible: pasar a una base única compartida es un cambio de config, no de dominio.

2. **Datos iniciales: exactamente los 5 items actuales** (mismos id, title, category,
   status, weight). El seed de ambos lados carga esta tabla idéntica:

   | id | title | category | status | weight |
   |---|---|---|---|---|
   | 1 | Bootstrap repo | infra | done | 3 |
   | 2 | Add Symfony service | backend | done | 5 |
   | 3 | Add Spring Boot service | backend | done | 5 |
   | 4 | Write README | docs | in_progress | 2 |
   | 5 | Publish blog post | docs | planned | 3 |

3. **Fórmula de completitud: sin cambios.** Sigue siendo
   `completion = done_weight / total_weight`, intdiv (redondeo hacia abajo).
   Con el seed de arriba: `done_weight = 13`, `total_weight = 18` → **`Completion: 72%`**.
   El contrato visible es idéntico al de Sprint 1; lo único nuevo es que los datos están
   persistidos.

## Contrato de paridad — Sprint 2

Ambas apps deben renderizar exactamente lo mismo, ahora leyendo desde MySQL:

- Tabla con **5 sprint items**, datos idénticos a la tabla de arriba, en el mismo orden.
- Resumen calculado en runtime: `Total items: 5`, `Total weight: 18`, `Completion: 72%`.
- Tras `docker compose down -v` y nuevo `up`, el estado vuelve al seed inicial (mismos
  5 items, 72%).
- Reinicio de contenedores **sin** `-v` conserva los datos persistidos.

Diferencias aceptables (idiomáticas, no cambian lo visible): mecanismo de migración/seed
(Doctrine migrations/fixtures vs data initializer/script JPA), nombres internos de tabla,
estrategia de IDs.

## Validación esperada

```bash
docker compose up --build
docker compose exec symfony vendor/bin/phpunit
docker run --rm -v "$PWD/springboot":/build -w /build \
  maven:3.9-eclipse-temurin-21 mvn -B -ntp test
```

- Symfony: test de repositorio/integración (kernel) que lee los items persistidos +
  `WebTestCase` que verifica los 5 items y `Completion: 72%`.
- Spring Boot: `@DataJpaTest` (o integración) que lee los items persistidos + `MockMvc`
  que verifica los 5 items y `Completion: 72%`.
- Smoke de paridad: comparar visualmente `http://localhost:8094` y `http://localhost:8095`
  (título, datos, total, completion).
- Reconstrucción: `docker compose down -v && docker compose up --build` → 72% de nuevo.

## Riesgos y mitigación

- **Clone & run con MySQL:** definir healthcheck del servicio `mysql` y que ambas apps
  esperen a `mysql` healthy antes de migrar/seedear. Sin esto, el primer arranque puede
  fallar por base no lista.
- **Paridad:** si cambia la data o la fórmula, se actualiza en **ambos** lados en el mismo
  sprint (regla de `ITERACIONES.md`). Este sprint NO cambia data ni fórmula.
- **Narrativa del post:** describir solo lo implementado (persistencia). No adelantar
  Sprint 3 (hexagonal) como si ya existiera.

## Documentación al cerrar

- README: actualizar el contrato a "Sprint 2", comandos nuevos (volumen, reset) y el
  servicio `mysql`.
- Post nuevo de continuidad en el blog, enlazado **en ambos sentidos** con el post de
  Sprint 1 (`misma-app-dos-stacks-symfony-springboot-sprint-1`) y manteniendo el hilo de
  serie.
- Actualizar `PORTFOLIO_STATUS.md` del portfolio cuando el sprint quede publicado.
