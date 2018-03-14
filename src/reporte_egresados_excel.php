<?php

session_start();
include_once '../config/sivisae_class.php';
include './paginador.php';
include_once './crear_reporteCB.php';
$consulta = new sivisae_consultas();
$tipo = $_GET['p'];
$cohorte = $_POST['periodo'];
$zona = isset($_POST['zona']) && $_POST['zona'] != '' ? implode(", ", $_POST['zona']) : "T";
$cead = isset($_POST['cead']) && $_POST['cead'] != '' ? implode(", ", $_POST['cead']) : "T";
$escuela = isset($_POST['escuela']) && $_POST['escuela'] != '' ? implode("', '", $_POST['escuela']) : "T";
$programa = isset($_POST['programa']) && $_POST['programa'] != '' ? implode(", ", $_POST['programa']) : "T";
$momentos = $_POST["momentos"];


$tipo_reporte = $_POST['tipo_r'];
$asignar = array();
if (isset($_POST["buscar"]) && $_POST["buscar"] != '') {
    $temp = $consulta->ReporteEgresadosExcel($_POST["buscar"], $cohorte, $zona, $cead, $escuela, $programa, $momentos, $tipo_reporte);
} else {
    $temp = $consulta->ReporteEgresadosExcel('n', $cohorte, $zona, $cead, $escuela, $programa, $momentos, $tipo_reporte);
}

while ($fila = mysql_fetch_array($temp)) {
    $asignar[] = $fila;
}



if ($tipo_reporte === "1") {
    $columnas = array("DOCUMENTO", "NOMBRE", "ÁREA GEOGRÁFICA", "GENERO", "DIRECCIÓN", "EMAIL", "TELÉFONO", "CIUDAD RESIDENCIA", "PROGRAMA", "ESCUELA", "CENTRO",
        "ZONA", "MES", "AÑO", "NIVEL ACADÉMICO", "SITUACION LABORAL", "ACTIVIDAD ECONOMICA", "NOMBRE EMPRESA", "DIRECCION EMPRESA", "TELEFONOEMPRESA",
        "ANTIGUEDAD", "TIEMPO DESEMPLEADO", "RELACION PROGRAMA-TRABAJO", "ESTADO", "MOMENTO");
    $titulo = "Reporte General de Egresados";
    $nombre_arch = "Reporte General de Egresados";
} else if ($tipo_reporte === "2") {
    $columnas = array("DOCUMENTO", "NOMBRE", "EMAIL", "PROGRAMA", "ESCUELA", "CENTRO", "ZONA", "MES", "AÑO", "NIVEL ACADÉMICO", "ESTADO", "MOMENTO", "CODIGO VERIFICACION", "PROTECCION DATOS");
    $titulo = "Reporte para Comunicaciones con Egresados";
    $nombre_arch = "Reporte para Comunicaciones con Egresados";
} else if ($tipo_reporte === "3") {
    $columnas = array("DOCUMENTO", "NOMBRE", "PROGRAMA", "ESCUELA", "CENTRO", "ZONA", "PREGUNTA", "RESPUESTA");
    $titulo = "Reporte Egresados - Momento Cero";
    $nombre_arch = "Reporte Egresados - Momento Cero";
}

$desc = "Reporte que contiene listado de Egresados Unadistas.";
$ruta = generarReporte($titulo, $columnas, $asignar, $nombre_arch, $desc);
    
echo $ruta;
