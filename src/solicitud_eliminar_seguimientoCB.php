<?php
session_start();
include_once '../config/sivisae_class.php';
$consulta = new sivisae_consultas();

$observacion_solicitud = $_POST['observacion_eliminacion'];
$id_seguimiento = $_POST['seg_id'];

$solicitud = $consulta->generarSolicitudEliminacion($observacion_solicitud, $id_seguimiento);

echo $solicitud;

$consulta->destruir();
?>