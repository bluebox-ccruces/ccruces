-- Reparación de textos dañados (mojibake) en producción.
-- Ejecutar en phpMyAdmin, una sola vez.
SET NAMES utf8mb4;

-- Asegura charset correcto en tablas
ALTER TABLE users CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE services CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE posts CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Paso 1: reinterpreta textos que contienen patrones típicos (Ã, Â)
UPDATE posts
SET
  title = CONVERT(BINARY(CONVERT(title USING latin1)) USING utf8mb4),
  excerpt = CONVERT(BINARY(CONVERT(excerpt USING latin1)) USING utf8mb4),
  content = CONVERT(BINARY(CONVERT(content USING latin1)) USING utf8mb4),
  author = CONVERT(BINARY(CONVERT(author USING latin1)) USING utf8mb4)
WHERE
  title LIKE '%Ã%' OR title LIKE '%Â%' OR
  excerpt LIKE '%Ã%' OR excerpt LIKE '%Â%' OR
  content LIKE '%Ã%' OR content LIKE '%Â%' OR
  author LIKE '%Ã%' OR author LIKE '%Â%';

UPDATE services
SET
  name = CONVERT(BINARY(CONVERT(name USING latin1)) USING utf8mb4),
  tagline = CONVERT(BINARY(CONVERT(tagline USING latin1)) USING utf8mb4),
  description = CONVERT(BINARY(CONVERT(description USING latin1)) USING utf8mb4),
  status = CONVERT(BINARY(CONVERT(status USING latin1)) USING utf8mb4)
WHERE
  name LIKE '%Ã%' OR name LIKE '%Â%' OR
  tagline LIKE '%Ã%' OR tagline LIKE '%Â%' OR
  description LIKE '%Ã%' OR description LIKE '%Â%' OR
  status LIKE '%Ã%' OR status LIKE '%Â%';

-- Paso 2: segunda pasada para casos de doble codificación
UPDATE posts
SET
  title = CONVERT(BINARY(CONVERT(title USING latin1)) USING utf8mb4),
  excerpt = CONVERT(BINARY(CONVERT(excerpt USING latin1)) USING utf8mb4),
  content = CONVERT(BINARY(CONVERT(content USING latin1)) USING utf8mb4),
  author = CONVERT(BINARY(CONVERT(author USING latin1)) USING utf8mb4)
WHERE
  title LIKE '%Ã%' OR title LIKE '%Â%' OR
  excerpt LIKE '%Ã%' OR excerpt LIKE '%Â%' OR
  content LIKE '%Ã%' OR content LIKE '%Â%' OR
  author LIKE '%Ã%' OR author LIKE '%Â%';

UPDATE services
SET
  name = CONVERT(BINARY(CONVERT(name USING latin1)) USING utf8mb4),
  tagline = CONVERT(BINARY(CONVERT(tagline USING latin1)) USING utf8mb4),
  description = CONVERT(BINARY(CONVERT(description USING latin1)) USING utf8mb4),
  status = CONVERT(BINARY(CONVERT(status USING latin1)) USING utf8mb4)
WHERE
  name LIKE '%Ã%' OR name LIKE '%Â%' OR
  tagline LIKE '%Ã%' OR tagline LIKE '%Â%' OR
  description LIKE '%Ã%' OR description LIKE '%Â%' OR
  status LIKE '%Ã%' OR status LIKE '%Â%';

-- Después de esto, vuelve a importar database/seed.sql para dejar contenidos consistentes.