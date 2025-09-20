INSERT INTO menu (nombre, php, orden, opc, extra, imagen, principal, paracliente, estado, directo) 
VALUES ('Promedios por Regionales', 'promedios_por_regionales', 3.00, 0, '', 'fas fa-chart-area', 7, 0, 1, 0);

INSERT INTO usuarios_menu (idUsuario, idMenu) 
SELECT u.id, m.id FROM usuario u, menu m 
WHERE u.tipo = 2 AND m.php = 'promedios_por_regionales';