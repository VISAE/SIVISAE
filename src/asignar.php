<?php
session_start();
include_once '../config/sivisae_class.php';
$consulta = new sivisae_consultas();

if (isset($_POST['auditor']) && isset($_POST['estudiantes']) && isset($_POST['periodo'])) {
    $auditor = $_POST['auditor'];
    $estudiantes = $_POST['estudiantes'];
    $periodo = $_POST['periodo'];
    $res = $consulta->asignarEstudiantes($auditor, $estudiantes, $_SESSION['usuarioid'], $periodo);
    if($res==1){
        if(count(split(",",$estudiantes))==1){
            echo "Estudiante asignado con exito.";
        }else {
            echo "Estudiantes asignados con exito.";
        }
    }else {
        echo "No fue posible asignar los estudiantes.";
    }
} else {
    echo "Error!!!";
}

$consulta->destruir();
