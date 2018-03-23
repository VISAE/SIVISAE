<?php
/**
 * Created by PhpStorm.
 * User: omar.bautista
 * Date: 20/03/2018
 * Time: 12:37 PM
 */
session_start();
include_once '../config/sivisae_class.php';
$consulta = new sivisae_consultas();

if (isset($_POST['documento']) && isset($_POST['periodo'])) {
    $documento = $_POST['documento'];
    $periodo = $_POST['periodo'];
    $salida = 'Registro Inexistente proceda a crearlo';
    $datosEstudiante = $consulta->consultarMatriculado($documento, $periodo);
    if ($row = mysqli_fetch_array($datosEstudiante)) {

        $salida = 'Bienvenido(a): '.$row[1];
    }
    echo $salida;
} else {
    echo "Error";
}
?>