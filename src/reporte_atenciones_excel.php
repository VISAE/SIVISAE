<?php

session_start();
include_once '../config/sivisae_class.php';
include './paginador.php';
include_once './crear_reporteCB.php';
$consulta = new sivisae_consultas();
$sintilde = explode(',', SIN_TILDES);
$tildes = explode(',', TILDES);


//Se obtienen las variables

$zona = isset($_POST['zona']) && $_POST['zona'] != '' ? implode(", ", $_POST['zona']) : "T";
$cead = isset($_POST['cead']) && $_POST['cead'] != '' ? implode(", ", $_POST['cead']) : "T";
$escuela = isset($_POST['escuela']) && $_POST['escuela'] != '' ? implode("', '", $_POST['escuela']) : "T";
$programa = isset($_POST['programa']) && $_POST['programa'] != '' ? implode(", ", $_POST['programa']) : "T";
$auditor = isset($_POST['auditor']) && $_POST['auditor'] != '' ? $_POST['auditor'] : 'T';
$f_inicial = isset($_POST['fecha_inicio']) && $_POST['fecha_inicio'] != '' ? $_POST['fecha_inicio'] : 'T';
$f_final = isset($_POST['fecha_fin']) && $_POST['fecha_fin'] != '' ? $_POST['fecha_fin'] : 'T';

$buscar;
$asignar = array();


//Base general
$atenciones2 = $consulta->ReporteAtenciones($auditor, 2, $_SESSION["perfilid"], $_SESSION["modulo"]);


$cont = 0;
while ($row = mysql_fetch_array($atenciones2)) {
    $id = $row[0];
    //Se muestra reporte de los atendidos
    $datos = $consulta->DatosReporteAtencionesExcel($id, $_POST["buscar"], $zona, $cead, $escuela, $programa, $f_inicial, $f_final);
    while ($rowData = mysql_fetch_array($datos)) {
        $asignar[] = $rowData;
        $cont++;
    }
}


if ($cont == 0) {
    echo 'No se encontraron registros';
} else {

    if ($_SESSION["perfilid"] != 9) {
        $etiqueta = "CONSEJERO";
    } else {
        $etiqueta = "MONITOR";
    }

    $titulo = "Reporte de atenciones";

    $columnas = array("DOCUMENTO ESTUDIANTE", "NOMBRE", "PROGRAMA", "ESCUELA", "CENTRO", "ZONA", "TELÉFONO", "CORREO", "CATEGORIA", "TIPO", "FECHA ATENCIÓN", $etiqueta);
    $nombre_arch = "Reporte de Antenciones " . $etiqueta;
    $desc = "Reporte que contiene el listado de atenciones.";
    $ruta = generarReporte($titulo, $columnas, $asignar, $nombre_arch, $desc);
    echo $ruta;
}
$consulta->destruir();
