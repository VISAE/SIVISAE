<?php

/*
  scripts para actualizar los usuarios registrados en los nodos
 * //Autor: Ing. Andres Camilo Mendez Aguirre
  //Fecha: 21/02/2017
 */

echo "entramos";

// Conectando, seleccionando la base de datos
$link = mysql_connect('192.168.4.23', 'root', 'V1s43_S3rv!d0r_$Thr33')
        or die('No se pudo conectar: ' . mysql_error());
echo 'Connected successfully';
mysql_select_db('SIVISAE') or die('No se pudo seleccionar la base de datos');

$sql = "UPDATE SIGRA.`tmp_titulos` SET `MES`=UPPER(`MES`)";
$result = mysql_query($sql) or die('Consulta fallida: ' . mysql_error());
$sql = "UPDATE SIGRA.`tmp_titulos` SET `MOMENTO`=''";
$result = mysql_query($sql) or die('Consulta fallida: ' . mysql_error());
$sql = "UPDATE SIGRA.`tmp_titulos` SET `MOMENTO`='M1' WHERE DATEDIFF(NOW(),CONCAT(`ANIO`,'-',REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(MES, 'ENERO', '01'),
        'FEBRERO', '02'),'MARZO', '03'),'ABRIL','04'),'MAYO','05'),'JUNIO','06'),'JULIO','07'),'AGOSTO','08'),'SEPTIEMBRE','09'),'OCTUBRE','10'),'NOVIEMBRE','11'),'DICIEMBRE','12'),'-',`DIA`)) >=360 AND 
        DATEDIFF(NOW(),CONCAT(`ANIO`,'-',REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(MES, 'ENERO', '01'),
        'FEBRERO', '02'),'MARZO', '03'),'ABRIL','04'),'MAYO','05'),'JUNIO','06'),'JULIO','07'),'AGOSTO','08'),'SEPTIEMBRE','09'),'OCTUBRE','10'),'NOVIEMBRE','11'),'DICIEMBRE','12'),'-',`DIA`)) <= 540";
$result = mysql_query($sql) or die('Consulta fallida: ' . mysql_error());
$sql = "UPDATE SIGRA.`tmp_titulos` SET `MOMENTO`='M3' WHERE DATEDIFF(NOW(),CONCAT(`ANIO`,'-',REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(MES, 'ENERO', '01'),
        'FEBRERO', '02'),'MARZO', '03'),'ABRIL','04'),'MAYO','05'),'JUNIO','06'),'JULIO','07'),'AGOSTO','08'),'SEPTIEMBRE','09'),'OCTUBRE','10'),'NOVIEMBRE','11'),'DICIEMBRE','12'),'-',`DIA`)) >=1080 AND 
        DATEDIFF(NOW(),CONCAT(`ANIO`,'-',REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(MES, 'ENERO', '01'),
        'FEBRERO', '02'),'MARZO', '03'),'ABRIL','04'),'MAYO','05'),'JUNIO','06'),'JULIO','07'),'AGOSTO','08'),'SEPTIEMBRE','09'),'OCTUBRE','10'),'NOVIEMBRE','11'),'DICIEMBRE','12'),'-',`DIA`)) <= 1440";
$result = mysql_query($sql) or die('Consulta fallida: ' . mysql_error());
$sql = "UPDATE SIGRA.`tmp_titulos` SET `MOMENTO`='M5' WHERE DATEDIFF(NOW(),CONCAT(`ANIO`,'-',REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(MES, 'ENERO', '01'),
        'FEBRERO', '02'),'MARZO', '03'),'ABRIL','04'),'MAYO','05'),'JUNIO','06'),'JULIO','07'),'AGOSTO','08'),'SEPTIEMBRE','09'),'OCTUBRE','10'),'NOVIEMBRE','11'),'DICIEMBRE','12'),'-',`DIA`)) >=1800 AND 
        DATEDIFF(NOW(),CONCAT(`ANIO`,'-',REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(MES, 'ENERO', '01'),
        'FEBRERO', '02'),'MARZO', '03'),'ABRIL','04'),'MAYO','05'),'JUNIO','06'),'JULIO','07'),'AGOSTO','08'),'SEPTIEMBRE','09'),'OCTUBRE','10'),'NOVIEMBRE','11'),'DICIEMBRE','12'),'-',`DIA`)) <= 1980";
$result = mysql_query($sql) or die('Consulta fallida: ' . mysql_error());
$sql = " UPDATE SIGRA.`tmp_titulos` SET `MOMENTO`='NO APLICA' WHERE `MOMENTO`=''";
$result = mysql_query($sql) or die('Consulta fallida: ' . mysql_error());
?>