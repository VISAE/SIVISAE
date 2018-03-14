<?php

session_start();
include_once '../config/sivisae_class.php';
include './paginador.php';
include_once './crear_reporteCB.php';
$consulta = new sivisae_consultas();


$periodo = $_POST['periodo'];
$zona = isset($_POST['zona']) && $_POST['zona'] != '' ? implode(", ", $_POST['zona']) : "T";
$cead = isset($_POST['cead']) && $_POST['cead'] != '' ? implode(", ", $_POST['cead']) : "T";
$escuela = isset($_POST['escuela']) && $_POST['escuela'] != '' ? implode("', '", $_POST['escuela']) : "T";
$programa = isset($_POST['programa']) && $_POST['programa'] != '' ? implode(", ", $_POST['programa']) : "T";



$asignar = array();
if (isset($_POST["buscar"]) && $_POST["buscar"] != '') {
    $temp = $consulta->ReporteInduccionesExcel($_POST["buscar"], $periodo, $zona, $cead, $escuela, $programa);
} else {
    $temp = $consulta->ReporteInduccionesExcel('n', $periodo, $zona, $cead, $escuela, $programa);
}

while ($fila = mysql_fetch_array($temp)) {
    $asignar[] = $fila;
}


$titulo = "Reporte de inducción";

$columnas = array("DOCUMENTO ESTUDIANTE", "NOMBRE", "TELÉFONO", "CORREO", "PROGRAMA", "ESCUELA", "CENTRO", "ZONA", "PERIODO", "TIPO", "FECHA INDUCCIÓN", "TIPO INDUCCIÓN", "TIPO PARTICIPACIÓN");
$nombre_arch = "Reporte Inducción";
$desc = "Reporte que contiene un listado inducciones.";
$ruta = generarReporte($titulo, $columnas, $asignar, $nombre_arch, $desc);

echo $ruta;
