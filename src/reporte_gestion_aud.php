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
$auditor = isset($_POST['auditor']) && $_POST['auditor'] != '' ? $_POST['auditor'] : 'T';

$asignar = array();
//Consulta que alimenta la tabla de estudiantes dependiendo del registro en que debe iniciar y la cantidad de registros por pagina.
if (isset($_POST["buscar"]) && $_POST["buscar"] != '') {
    $temp = $consulta->reporteGestionAud('ex', $_POST["buscar"], $auditor, '', '', $periodo, $cead, $zona, $escuela, $programa);
    while ($fila = mysql_fetch_array($temp)) {
        $asignar[] = $fila;
    }
} else {
    $temp = $consulta->reporteGestionAud('ex', 'n', $auditor, '', '', $periodo, $cead, $zona, $escuela, $programa);
    while ($fila = mysql_fetch_array($temp)) {
        $asignar[] = $fila;
    }
}

$titulo = "Reporte Gestión de Auditores de Servicios a los estudiantes";
$columnas = array("NOMBRE", "CEAD", "ZONA", "ASIGNADOS", "NUEVOS", "HOMOLOGADOS", "ANTIGUOS", "CARACTERIZACIÓN COMPLETA", "CARACTERIZACIÓN INCOMPLETA", "SIN CARACTERIZACIÓN");
$nombre_arch = "Reporte_Gestion_Auditores_Servicios_a_los_estudiantes";
$desc = "Reporte Gestión de Auditores de Servicios a los estudiantes";
$ruta = generarReporte($titulo, $columnas, $asignar, $nombre_arch, $desc);

echo $ruta;
