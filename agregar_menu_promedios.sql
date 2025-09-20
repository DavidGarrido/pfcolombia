-- Script para agregar opciones de promedios al menú
-- Ejecutar en producción

-- Crear backup antes de ejecutar
-- mysqldump -u root -p pfcoiied_db > backup_menu_$(date +%Y%m%d_%H%M%S).sql

-- Agregar opción "Promedios por Regionales" para coordinadores (mismo grupo que los otros)
INSERT IGNORE INTO menu (nombre, php, orden, opc, extra, imagen, principal, paracliente, estado, directo) 
VALUES ('Promedios por Regionales', 'promedios_por_regionales', 81.00, 0, '', 'fas fa-chart-area', 1, 1, 1, 0);

-- Habilitar automáticamente "Promedios por Regionales" para coordinadores (tipo 2)
-- Usar el ID que se generó automáticamente
INSERT INTO usuarios_menu (idUsuario, idMenu) 
SELECT u.id, m.id FROM usuario u, menu m 
WHERE u.tipo = 2 AND m.php = 'promedios_por_regionales';

-- Habilitar automáticamente "Promedios de Facilitadores" para coordinadores (tipo 2)
INSERT INTO usuarios_menu (idUsuario, idMenu) 
SELECT id, 79 FROM usuario WHERE tipo = 2 
AND id NOT IN (SELECT idUsuario FROM usuarios_menu WHERE idMenu = 79);

-- Habilitar automáticamente "Mis Promedios" para facilitadores (tipo 163)
INSERT INTO usuarios_menu (idUsuario, idMenu) 
SELECT id, 78 FROM usuario WHERE tipo = 163 
AND id NOT IN (SELECT idUsuario FROM usuarios_menu WHERE idMenu = 78);