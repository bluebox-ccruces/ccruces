-- Ejecutar en phpMyAdmin para asegurar soporte completo de caracteres especiales.
-- Reemplaza `TU_BASE_DE_DATOS` por el nombre real si quieres convertir toda la base.
-- ALTER DATABASE `TU_BASE_DE_DATOS` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE users CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE services CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE posts CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
