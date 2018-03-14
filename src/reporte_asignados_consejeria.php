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
$tipo_asignacion = $_POST['tipo_asignacion'];
if ($tipo_asignacion == 3) {
    $tipo_asignacion = "1,2";
}
$asignar = array();
//Consulta que alimenta la tabla de estudiantes dependiendo del registro en que debe iniciar y la cantidad de registros por pagina.
if (isset($_POST["buscar"]) && $_POST["buscar"] != '') {
    $temp = $consulta->filtrarEstudiantesAsignadosExcelConsejeria($auditor, $_POST["buscar"], $periodo, $escuela, $programa, $tipo_asignacion);

    while ($fila = mysql_fetch_array($temp)) {
        $asignar[] = $fila;
    }
} else {
    $temp = $consulta->estudiantesAsignadosExcelConsejeria($auditor, $periodo, $escuela, $programa, $tipo_asignacion);
    while ($fila = mysql_fetch_array($temp)) {
        $asignar[] = $fila;
    }
}

$titulo = "Reporte de estudiantes asignados";
$columnas = array("Cédula", "Nombre", "Centro", "Programa", "Escuela", "Periodo", "Caracterización", "Inducción", "Tipo Estudiante", "Zona","Estado", "Tipo Asignación", "Consejero", "Correo","Género","Teléfono", "Institucional");
$nombre_arch = "Estudiantes Asignados";
$desc = "Reporte que contiene un listado de estudiantes asignados.";
$ruta = generarReporte($titulo, $columnas, $asignar, $nombre_arch, $desc);

echo $ruta;
