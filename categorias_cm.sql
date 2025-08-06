-- Eliminar categorías C&M existentes y agregarlas de nuevo con idSec = 88
DELETE FROM categorias WHERE descripcion LIKE '%C&M%';

INSERT INTO categorias (descripcion, idSec) VALUES 
('C&M 1', 88),
('C&M 2', 88),
('C&M 3', 88);

-- Agregar columna para almacenar el curso
ALTER TABLE tbl_adjuntos ADD COLUMN adj_curso INT(11) NULL AFTER adj_can;