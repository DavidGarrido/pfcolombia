ALTER TABLE usuario
ADD usua_muni int(11);
ALTER TABLE usuario
ADD usua_pai varchar(50);
ALTER TABLE usuario ADD FOREIGN KEY(usua_muni)
REFERENCES dane_municipios (id_municipio);

INSERT INTO categorias (idSec, descripcion, detalle) VALUES
(37,'Regional Cundinamarca','Regional Cundinamarca'),
(37,'Regional Magdalena','Regional Magdalena'),
(37,'Regional Norte De Santander','Regional Norte De Santander'),
(37,'Regional Nariño','Regional Nariño'),
(37,'Regional Quindio','Regional Quindio'),
(37,'Regional Guajira','Regional Guajira'),
(37,'Regional Risaralda','Regional Risaralda'),
(37,'Regional Santander','Regional Santander'),
(37,'Regional Sucre','Regional Sucre'),
(37,'Regional Tolima','Regional Tolima'),
(37,'Regional Valle del Cauca','Regional Valle del Cauca'),
(37,'Regional Bolivar','Regional Bolivar'),
(37,'Regional Huila','Regional Huila'),
(37,'Regional Cauca','Regional Cauca');

CREATE TABLE tbl_regional_ubicacion(
	reub_id int(5) AUTO_INCREMENT PRIMARY KEY,
	reub_nom varchar(150) NOT NULL,
	reub_des varchar(150),
	reub_dir varchar(50),
	reub_reg_fk int(11) NOT NULL,
	reub_mun_fk int(11) NOT NULL,
	FOREIGN KEY (reub_reg_fk) REFERENCES categorias (id),
	FOREIGN KEY (reub_mun_fk) REFERENCES dane_municipios(id_municipio)
)ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO tbl_regional_ubicacion (reub_nom, reub_dir,reub_reg_fk,reub_mun_fk) VALUES
('Ere La Modelo','Carrera 82',289,88),
('Ere Penitenciaria Del Bosque','Carrera. 8 #76',289,88),
('Distrital Del Bosque','Carrera. 8 #76',289,88),
('Crf El Buen Pastor','Carrera. 26 #44-39',289,769),
('Ere Sabanalarga','Carrera 28 #13-42',289,925),
('Estaciones De Policía Hipodromo y La Victoria','Carrera. 30 #26-04',289,925),
('Epmsc El Banco ','Kilómetro 5 Vía Guamal',288,287),
('Epmsc Aguachica ','Calle 10 #8-90',288,8),
('Cárcel Municipal De Chiriguana ','Carrera 6 #5-31',288,201),
('Estacion De Policía Aguachica ','Calle 5 #31-51',288,8),
('Estacion De Policía Mompox ','Carrera 3 No. 18A-89',288,565),
('Estacion De Policía San Fernando ','Calle 15 #80B-2',288,171),
('Estacion De Policía Guamal ','Carrera 11 No. 4-67 Barrio 10 De Marzo',288,393),
('Estacion De Policía Curumani ','Calle 7 15-104',288,266),
('Establecimiento Penitenciario y Cárcelario La Picota','Kilómetro 5 Vía Usme',287,107),
('Cárcel Nacional Modelo','Carrera. 57 #1560, Bogotá',287,107),
('Reclusion De Mujeres De Bogota El Buen Pastor','Carrera 58 #80-95',287,107),
('Cárcel Distrital','Calle 1C Sur, Carrera. 8 ##1C -50',287,107),
('Distrital De Paz De Ariporo','Calle 10 #6-69',286,651),
('Distrital De Yopal','Calle 7 Con Carrea 19',286,1085),
'Cárcel De Hombres De Sogamoso','Carrera 9ª 1ª -16 Sur',286,
'Cárcel De Mujeres De Sogamoso','Carrera 9ª 1ª -16 Sur',286,
'Distrital De Duitama','Calle 7 No 15 -08',286,
'Distrital De Santa Rosa De Viterbo','Carrera 4 #7-13',286,
'Máxima Seguridad De Cómbita','Kilometro 17 Via Tunja',286,
'Mediana Seguridad De Cómbita','Kilometro 17 Via Tunja',286,
'Distrital De Tunja','Calle 31 No.2 -15 Este',286,
'Distrital De Ramiriquí','Calle 8 #4A-2 A 4A-14',286,
'Distrital De Moniquirá','Kilometro 01 Vía  Barbosa',286,
'Distrital De San Gil','Carrera 12 #20 A 67',286,
'Centro De Formacion Juvenil(Cromi)','Carrera. 19E #5B-2'
'Uri','Calle 16 #19-51'
'Permanente Central','Carrera 3 #16-97'
'Batallon','Calle 16 #31136'
'Centro Cárcelario De Mediana Seguridad La Judicial','Carrera. 19 #18-60'
'Centro Penitenciario De Maxima Seguridad','Kilometro 3.5 Vía La Mesa'
'Pacora Caldas','Kilometro 2 Via A Salamina '
'Riosucio Caldas','Calle 6 ##8-14'
'La Blanca Manizales','Vía Panamericana Barrio Estambul'
'Villa Josefina Manizales','Calle 58 #24A-50'
'Estacion De Policía Risaralda','Calle 63A #42 A 82'
'Estacion De Policía Ancerma','Calle 18 #32'
'Correccional Fei','Carrera 4 H 4190'
'Ana Yancy','Calle 28 #8-2 A 8-66,'
'Distrital De Istmina','Vía 40 # 54-332'
'Epmsc Monteria','Calle 39 A # 18-29'
'Epmsc Tierralta','Kilometro 23 Via Urra'
'Epmsc Granada ','Carrera 14 #14-31'
'Cpmsacs Acacias - Meta ','Kilometro 3 Via Acacias'
'Camis Ere. Acacias - Meta ','Kilometro 3 Via Acacias'
'Epmsc Caqueza ','Calle 2 ## 4-32'
'Epmsc Choconta ','Carrera 5 N· 5-31'
'Epmsc Ubate ','Carrera 7 #6-41'
'Epmsc Zipaquira ','Calle 7 #7-48'
'Epc La Esperanza De Guaduas ','Kilometro 3.5 Vía A Cambao, Vereda La Unión'
'Epmsc Villeta ','Carrera 6 #3-160'
'Epmsc La Mesa ','Carrera 21 #8-46'
'Epmsc Guateque','Calle 9 #6-1 #6-85 A'
'Epmsc Villavicencio 	Transversal 26 # 21-34'
'Cárcel De La Policía ','Calle 8 No 2 - 29'
'Cárcel De Funza	Via Siberia Mosquera #Kilometro 3'
'Epmsc Fusagasuga ','Carrera 8 N. 7-51 '
'Empsc - Rodrigo De Bastidas','Calle 24 No. 17A-36'
'Empsc - Rodrigo De Bastidas - Ii','Calle 24 No. 17A-36'
'Complejo Penitenciario De Cúcuta	Caserío El Cerrito'
'Establecimiento Penitenciario De Mediana Seguridad Y Cárcelario De Pasto','Calle 24 No. 31-23 '
'Establecimiento Penitenciario De Mediana Seguridad Y Cárcelario De Ipiales','Carrera 8 #1-49'
'Establecimiento Penitenciario De Mediana Seguridad Y Cárcelario De La Union','Carrera. 3 No. 13 – 48'
'Cárcel Peñas Blancas Calarcá, Quindío	Kilometro 1 Via Al Valle del Cauca '
'Cárcel San Bernardo','Calle 50 21-97 '
'Cárcel Villa Cristina','Calle 50 N°23-29 '
'Cárcel De Menores La Primavera	','Carrera. 4 #7-2'
'Penitenciaria De Sevilla','Carrera 50 Con','Calle 62ª'
'Cárcel De Riohacha','Carrera 9 #17-11'
'Instituto Nacional Penitenciario Y Cárcelario La 40','Carrera 11 # 50-57'
'Recluciòn De Mujeres Impecable La Badea','Kilómetro 4 Vía Turín La Popa'
'Unidad Permanente De Proteccion A La Vida	','Calle 14 #5 - 20'
'Instituto Nacional Penitenciario Y Cárcelario Santa Rosa De Cabal ','Carrera 16 No. 14-27'
'Prisión Municipal Anserma','Calle 3 #3-27 #3-69 A'
'Correccional De Menores Marceliano Ossa Pereira','Calle 18 #19A-18'
'Reclucion De Mujeres El Buen Pastor	','Calle 45 Via Chimita'
'Epam Giron(Palogordo)	Carretera A Zapatoca Kilometro 14'
'Inspeccion De Policía Del Norte','Calle 35 # 10-43'
'Cárcel Modelo De Bucaramanga','Calle 45, 533'
'Establecimiento Penitenciario De Mediana Seguridad Y Cárcelario De Sincelejo','Calle 10 N° 13 – 72'
'Coiba Picaleña Ibague','Carrera 45 Sur No. 134 - 95'
'Cpms Melgar','Carrera. 26 #571'
'Epmsc Espinal','Carrera 12','Calle 6'
'Epmsc Guamo','Calle 11 N. 8-04'
'Epms Purificación','Barrio El Plan'
'Epmsc Chaparral','Carrera 6ªe N° 8ª- 44'
'Epmsc Honda','Carrera 12 # 5-71, 132'
'Epmsc Fresno','Calle 4 #3-2'
'Epmsc Libano','Carrera. 12 #5-41'
'Epmsc Armero Guayabal','Carrera 6 #6-23'
'Cárcel De Villa Hermosa','Calle 31B #73B-38'
'Cárcel De Jamundí 	Kilometro 2.7 Vía Bocas Del Palo'
'Cárcel De Buenaventura','Calle 6 No. 51B-61'
'Cárcel De Cartago ','Calle 10 Nº 13 - 72'
'Cárcel De Roldanillo','Calle 7 N° 6 -51'
'Cárcel De Caicedonia','Calle 18 No. 19-203'
'Cárcel De San Sebastian De Ternera','Calle 31 #85-180'
'Cárcel Distrital De Mujeres','Diagonal 31 # 85-180'
'Cárcel Camilo Torres-Magangué','Barrio Camilo Torres'
'Hogares Claret','Calle 11 #10-71'
'Estación De Policía Los Caracoles','Kra 58A No. 21-21'
'Estación De Policía Bellavista','Dg. 28 ##57-35'
'Cárcel De Pitalito','Calle 19 #67-80 '
'Cárcel De Garzon','Calle 3 Sur No. 18 A 19'
'Cárcel De La Plata ','Carrera 3 Este No. 13-287'
'Cárcel De Neiva ','Vereda Río Frío'
'Cpamspy - Cárcel Y Penitenciaria Con Alta Y Media Seguridad De Popayán	','Kilómetro 3 Vía Vereda Las Guacas'
'Cpmsmpy - Cárcel Y Penitenciaria De Media Seguridad Para Mujeres De Popayán','Calle 3 N. 16-11'
'Epmsc Santander De Quilichao','Calle 4 Via Timbo'
'Epmsc Silvia','Carrera 3 N° 4 – 52'
'Cpmsebo - Cárcel Y Penitenciaria De Media Seguridad De El Bordo','Calle 2 7-25 '
'Epmsc Bolívar Cauca','Calle 3 No. 3-32'
'Centro De Armonizacion','Resguardo Indigena Canoas	Resguardo Canoas'
