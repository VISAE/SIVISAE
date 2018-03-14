<?php
session_start();
include '../config/sivisae_class.php';
$titulo = $_POST['titulo_e'];
$descripcion = $_POST['descripcion_e'];
$fecha_noticia = $_POST['fecha_e'];
$link = $_POST['link_e'];
$id_upd = $_POST['id_e'];

$update = new sivisae_consultas();


//Se valida la fecha que no sea menor a la de hoy
$fechaServer = $update->obtenerFechaServer(2);

//unificar con la hora para validar 
$datetime1 = date_create($fechaServer);
$datetime2 = date_create($fecha_noticia . ' 00:00:00');
if ($datetime2 < $datetime1) {
    echo 'La fecha de la noticia debe ser mayor a la fecha actual';
} else {
    //Se envia a guardar
    $update->actualizarNoticia($titulo, $descripcion, $fecha_noticia, $link, $id_upd);
    echo 'La noticia ha sido actualizada con éxito.';
}

$update->destruir();