<?php
session_start();
include '../config/sivisae_class.php';

$periodo = $_POST['periodo_e'];
$semanas = $_POST['semanas_e'];
$fecha_inicio = $_POST['fecha_inicio_e'];
$fecha_fin = $_POST['fecha_fin_e'];
$seguimientos = $_POST['seguimientos_e'];
$id_upd = $_POST['id_e'];

$insert = new sivisae_consultas();


//Se valida la fecha que no sea menor a la de hoy
$fechaServer = $insert->obtenerFechaServer(2);

//validacion de fechas
$dateFechaServer = date_create($fechaServer);
$dateFechaInicio = date_create($fecha_inicio . '00:00:00');
$dateFechaFin = date_create($fecha_fin . '00:00:00');

$msj = "";

if ($dateFechaInicio <= $dateFechaServer) {
    $msj = "La fecha inicial debe ser mayor que la fecha actual";
}

if ($dateFechaInicio <= $dateFechaServer) {
    $msj = "La fecha final debe ser mayor que la fecha actual";
}

if ($dateFechaInicio > $dateFechaFin) {
    $msj = "La fecha inical debe ser menor que la fecha final";
}

if ($msj == "") {
    // Se guarda la informacion
    $insert->actualizaCorteSeguimiento($periodo, $semanas, $fecha_inicio. ' 00:00:00', $fecha_fin. ' 23:59:59', $seguimientos, $id_upd);
    $msj = "El corte de seguimiento se ha actualizado con éxito";
}

echo $msj;

$insert->destruir();