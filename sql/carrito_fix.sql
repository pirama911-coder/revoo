-- Corre esto en tu MySQL (phpMyAdmin) para permitir múltiples ítems por carrito y evitar el problema descrito.
-- 1) Ver índices actuales de la tabla carrito
--    (Usa esto para identificar si hay un índice UNIQUE sólo en usuario_id)
--    SHOW INDEX FROM carrito;

-- 2) Si existe un índice UNIQUE sobre `usuario_id`, elimínalo.
--    Cambia NOMBRE_DEL_INDICE por el nombre real que obtengas del paso 1.
--    Ejemplo típico: `usuario_id` o `usuario_id_unique`.
-- ALTER TABLE carrito DROP INDEX NOMBRE_DEL_INDICE;

-- 3) Asegura un índice único compuesto correcto (usuario_id, articulo_id)
ALTER TABLE carrito
  ADD UNIQUE KEY usuario_articulo (usuario_id, articulo_id);

-- 4) (Opcional pero recomendado) Asegura claves foráneas y tipos
-- Ajusta nombres de tabla/campos si difieren en tu BD.
-- ALTER TABLE carrito
--   ADD CONSTRAINT fk_carrito_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
--   ADD CONSTRAINT fk_carrito_articulo FOREIGN KEY (articulo_id) REFERENCES articulos(id) ON DELETE CASCADE;

-- 5) (Opcional) Default para cantidad
-- ALTER TABLE carrito MODIFY cantidad INT NOT NULL DEFAULT 1;
