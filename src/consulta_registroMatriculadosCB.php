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

if (isset($_POST['documento'])) {
    $documento = $_POST['documento'];
    $datosEstudiante = $consulta->consultarMatriculado($documento);
    $salida = 'Registro Inexistente proceda a crearlo';
    while ($row = mysqli_fetch_array($datosEstudiante)) {
        $salida = 'Bienvenido(a):';
    }
    echo $salida;
} else {
    echo "Error";
}
?>