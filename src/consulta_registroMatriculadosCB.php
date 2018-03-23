<?php
/**
 * Created by PhpStorm.
 * User: omar.bautista
 * Date: 20/03/2018
 * Time: 12:37 PM
 */
session_start();
include_once '../config/sivisae_class.php';
include_once '../pages/sivisae_filtro_induccion_agrega_estudiante.php';
$consulta = new sivisae_consultas();

if (isset($_POST['documento']) && isset($_POST['periodo'])) {
    $documento = $_POST['documento'];
    $periodo = $_POST['periodo'];
    $salida = Array('typeSwal'=>'warning',
                    'titleSwal'=>'Registro Inexistente proceda a crearlo',
                    'response'=>"
<form>
    <div align='center' style='background-color: #004669'>
        <h2 id='p_fieldset_autenticacion_2'>
            Datos personales del Estudiante
        </h2>
    </div>
    <div align='center'>
        <table>"
            .filtroInduccion().
        "</table>    
    </div>
</form>");
    $datosEstudiante = $consulta->consultarMatriculado($documento, $periodo);
    if ($row = mysqli_fetch_array($datosEstudiante)) {

        $salida = Array('typeSwal'=>'info',
                        'titleSwal'=>'Bienvenido(a): '.$row[1],
                        'response'=>$row[1]);
    }
    echo json_encode($salida);
} else {
    echo json_encode(Array('typeSwal'=>'warning',
                           'titleSwal'=>'Error!!',
                           'response'=>'Error'));
}
?>