# Same App, Two Stacks — Symfony + Spring Boot

Repo comparativo: la **misma aplicación** implementada en **Symfony 7** (PHP 8.3) y **Spring Boot 3** (Java 21), levantadas a la vez con un solo `docker compose up`.

La idea es ir agregando, en sprints sucesivos, features típicas de cada framework (workflow, validación, persistencia, mensajería, etc.) manteniendo siempre la **misma pantalla** como resultado. Cada lado renderiza con su engine nativo (Twig / Thymeleaf) — el contraste está en el código, no en el output.

El enfoque editorial es **comparar decisiones, no declarar ganadores**: cada lado resuelve el mismo problema, las diferencias muestran las decisiones idiomáticas de cada ecosistema.

## Levantar

Requisitos: Docker + Docker Compose. **No** hace falta PHP, Composer, Java ni Maven en el host.

```bash
git clone https://github.com/walteru/symfony-springboot-side-by-side.git
cd symfony-springboot-side-by-side
docker compose up --build
```

El primer arranque tarda varios minutos (Composer descarga `vendor/` la primera vez; Maven descarga el árbol de dependencias y compila el JAR). A partir del segundo `up` queda en segundos.

Una vez levantado:

| Servicio    | URL                       | Puerto host |
|-------------|---------------------------|-------------|
| Symfony     | http://localhost:8094     | 8094        |
| Spring Boot | http://localhost:8095     | 8095        |

Para bajar todo:

```bash
docker compose down        # conserva volúmenes (cachés)
docker compose down -v     # limpia también volúmenes
```

## Contrato de paridad — Sprint 1

Ambas apps **deben** renderizar exactamente lo mismo (texto y estructura). Esto es el "smoke test de paridad" del sprint:

- Título: `Same App, Two Stacks — Sprint 1`
- Subtítulo: `Powered by <Framework> <versión>`
- Tabla con **5 sprint items** (datos idénticos en ambos lados):

  | id | title                    | category | status      | weight |
  |----|--------------------------|----------|-------------|--------|
  | 1  | Bootstrap repo           | infra    | done        | 3      |
  | 2  | Add Symfony service      | backend  | done        | 5      |
  | 3  | Add Spring Boot service  | backend  | done        | 5      |
  | 4  | Write README             | docs     | in_progress | 2      |
  | 5  | Publish blog post        | docs     | planned     | 3      |

- Resumen calculado en runtime (no hardcodeado):
  - `Total items: 5`
  - `Total weight: 18`
  - `Completion: 72%` &nbsp; (= done_weight / total_weight = 13 / 18, redondeado hacia abajo)

Los tests de cada lado verifican estos valores como contrato. Si en un sprint futuro cambia la data o la fórmula, se actualiza en **ambos** lados a la vez.

## Stack

### Symfony (`symfony/`)

- PHP 8.3 (imagen `php:8.3-apache`)
- Symfony 7.x (`composer.json` declara `^7.2`; `composer.lock` actual fija componentes 7.4.x)
- Apache 2.4 con `mod_rewrite` + `FallbackResource` apuntando a `public/index.php`
- Twig 3 para vistas
- PHPUnit 11 para tests
- Entrypoint propio: si falta `vendor/autoload.php` en el primer arranque, corre `composer install` automáticamente

### Spring Boot (`springboot/`)

- Java 21 (Eclipse Temurin)
- Spring Boot 3.3.4 (`spring-boot-starter-web` + `spring-boot-starter-thymeleaf`)
- Maven 3.9 (solo dentro del contenedor, en la etapa de build)
- Dockerfile multi-stage: build con `maven:3.9-eclipse-temurin-21` → runtime con `eclipse-temurin:21-jre` (imagen final más liviana, sin JDK)
- JUnit 5 + `MockMvc` para tests

## Correr los tests

Cada lado tiene su test mínimo de smoke + paridad de contrato:

```bash
# Symfony — corre dentro del contenedor levantado
docker compose exec symfony vendor/bin/phpunit

# Spring Boot — la imagen runtime es solo JRE (sin Maven),
# así que se ejecuta en un contenedor Maven temporal con el código montado
docker run --rm \
    -v "$PWD/springboot":/build -w /build \
    maven:3.9-eclipse-temurin-21 \
    mvn -B -ntp test
```

> Por qué los comandos son distintos: el `Dockerfile` de `springboot/` es multi-stage — Maven solo vive en la etapa de build, y la imagen final que `docker compose` levanta es `eclipse-temurin:21-jre` ejecutando el JAR. Para correr `mvn test` necesitamos un entorno con Maven, por eso el `docker run` aparte. Si tenés Maven en el host, también podés hacer `cd springboot && mvn test`.

## Estructura

```
symfony-springboot-side-by-side/
├── docker-compose.yml
├── symfony/
│   ├── Dockerfile
│   ├── docker/                  config Apache + entrypoint
│   ├── composer.json
│   ├── public/                  index.php + assets
│   ├── src/                     Kernel + Controller + Domain
│   ├── templates/               Twig
│   ├── tests/                   PHPUnit
│   ├── config/                  bundles, routes, services, framework
│   └── .env                     valores dummy (APP_SECRET=demo-not-secret)
└── springboot/
    ├── Dockerfile               multi-stage build
    ├── pom.xml
    └── src/
        ├── main/
        │   ├── java/com/sincrodev/sidebyside/
        │   └── resources/       application.properties, static/, templates/
        └── test/java/com/sincrodev/sidebyside/
```

## Roadmap (sprints siguientes — orientativo)

- **Sprint 2** — Persistencia: misma entidad en Doctrine ORM (Symfony) y Spring Data JPA (Spring Boot) contra MySQL compartido. CRUD básico de sprint items.
- **Sprint 3** — Validación: Symfony Validator vs Bean Validation (Jakarta).
- **Sprint 4** — Workflow / máquinas de estado: Symfony Workflow vs Spring State Machine.
- **Sprint 5** — Mensajería async: Symfony Messenger vs Spring `@Async` / RabbitMQ.

Cada sprint mantiene el contrato de paridad de pantalla actualizado.

Para criterios de paridad, checklists de iteración, línea editorial y plantilla para proponer un sprint, ver [`ITERACIONES.md`](ITERACIONES.md).

## Licencia

[MIT](LICENSE)
