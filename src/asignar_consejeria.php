<?php
session_start();
include_once '../config/sivisae_class.php';
$consulta = new sivisae_consultas();

if (isset($_POST['auditor']) && isset($_POST['estudiantes']) && isset($_POST['periodo']) && isset($_POST['tipo'])) {
    $consejero = $_POST['auditor'];
    $estudiantes = $_POST['estudiantes'];
    $periodo = $_POST['periodo'];
    $tipoAsg = $_POST['tipo'];
    $res = $consulta->asignarEstudiantesConsejeria($consejero, $estudiantes, $_SESSION['usuarioid'], $periodo, $tipoAsg);
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
