-- Sprint 2: una sola instancia MySQL con DOS bases separadas, una por stack.
-- Cada framework gestiona su propio esquema (Doctrine / Spring Data JPA).

CREATE DATABASE IF NOT EXISTS app_symfony    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE IF NOT EXISTS app_springboot CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Usuario de aplicación compartido (credenciales dummy, solo para el demo).
CREATE USER IF NOT EXISTS 'app'@'%' IDENTIFIED BY 'app_demo_not_for_production';
GRANT ALL PRIVILEGES ON app_symfony.*    TO 'app'@'%';
GRANT ALL PRIVILEGES ON app_springboot.* TO 'app'@'%';
FLUSH PRIVILEGES;
