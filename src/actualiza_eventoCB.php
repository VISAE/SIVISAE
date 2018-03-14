<?php
session_start();
include '../config/sivisae_class.php';
$titulo = $_POST['titulo_e'];
$descripcion = $_POST['descripcion_e'];
$fecha_evento = $_POST['fecha_e'];
$hora_evento = $_POST['hora_e'];
$id_upd = $_POST['id_e'];

$update = new sivisae_consultas();
//Se valida la fecha que no sea menor a la de hoy
$fechaServer = $update->obtenerFechaServer(2);
//unificar con la hora para validar 
$datetime1 = date_create($fechaServer);
$datetime2 = date_create($fecha_evento . ' ' . $hora_evento . ':00:00');

if (!$datetime2 < $datetime1) {
//se actualiza el evento
    $resUpd = $update->actualizarEvento($titulo, $descripcion, $fecha_evento . ' ' . $hora_evento . ':00:00', $id_upd, $_SESSION["usuarioid"]);
//Se retorna el resultado
    echo $resUpd;
} else {
    echo 'La fecha del evento debe ser mayor a la fecha actual';
}



$update->destruir();