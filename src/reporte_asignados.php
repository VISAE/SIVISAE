<?php
session_start();
include_once '../config/sivisae_class.php';
include './paginador.php';
include_once './crear_reporteCB.php';
$consulta = new sivisae_consultas();

$periodo = $_POST['periodo'];
$escuela = isset($_POST['escuela']) && $_POST['escuela'] != '' ? implode("', '", $_POST['escuela']) : 'T';
$programa = isset($_POST['programa']) && $_POST['programa'] != '' ? implode(", ", $_POST['programa']) : 'T';
//$pagina = $_POST["page"];
$auditor = isset($_POST['auditor']) && $_POST['auditor'] != '' ? $_POST['auditor'] : 'T';

$asignar = array();
//Consulta que alimenta la tabla de estudiantes dependiendo del registro en que debe iniciar y la cantidad de registros por pagina.
if (isset($_POST["buscar"]) && $_POST["buscar"] != '') {
    $temp = $consulta->filtrarEstudiantesAsignadosExcel($auditor, $_POST["buscar"], $periodo, $escuela, $programa);
    while ($fila = mysql_fetch_array($temp)) {
        $asignar[] = $fila;
    }
} else {
    $temp = $consulta->estudiantesAsignadosExcel($auditor, $periodo, $escuela, $programa);
    while ($fila = mysql_fetch_array($temp)) {
        $asignar[] = $fila;
    }
}

$titulo = "Reporte de estudiantes asignados";
$columnas = array("Nombre estudiante", "Cédula", "Programa", "CEAD", "Zona", "Escuela", "Tipo", "Teléfono", "Correo", "Auditor asignado", "Periodo académico");
$nombre_arch = "Estudiantes Asignados";
$desc = "Reporte que contiene un listado de estudiantes asignados.";
$ruta = generarReporte($titulo, $columnas, $asignar, $nombre_arch, $desc);

echo $ruta;
