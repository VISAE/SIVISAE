<?php
session_start();
include '../config/sivisae_class.php';

$titulo = $_POST['titulo'];
$descripcion = $_POST['descripcion'];
$fecha = $_POST['fecha'];
$hora = $_POST['hora'];

$insert = new sivisae_consultas();


//Se valida la fecha que no sea menor a la de hoy
$fechaServer = $insert->obtenerFechaServer(2);



//unificar con la hora para validar 
$datetime1 = date_create($fechaServer);
$datetime2 = date_create($fecha . ' ' . $hora . ':00:00');
if (!$datetime2 < $datetime1) {
//Se envia a guardar
    $insert->guardarEvento($titulo, $descripcion, $fecha . ' ' . $hora . ':00:00');
    echo 'El evento ha sido registrado con éxito.';
} else {
    echo 'La fecha del evento debe ser mayor a la fecha actual';
}




$insert->destruir();