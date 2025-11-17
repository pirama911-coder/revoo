-- Agrega columna de stock al catálogo de artículos
-- Ejecuta esto en tu BD (phpMyAdmin)

ALTER TABLE articulos
  ADD COLUMN stock INT NOT NULL DEFAULT 0 AFTER imagen;

-- Opcional: establece un stock inicial para artículos existentes
-- UPDATE articulos SET stock = 10;
