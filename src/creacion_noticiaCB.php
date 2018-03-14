<?php

session_start();
include '../config/sivisae_class.php';
$titulo = $_POST['titulo'];
$descripcion = $_POST['descripcion'];
$fecha = $_POST['fecha'];
$link = $_POST['link'];
$perfil = $_POST['perfil'];
$insert = new sivisae_consultas();
//Se valida la fecha que no sea menor a la de hoy
$fechaServer = $insert->obtenerFechaServer(2);
//unificar con la hora para validar 
$datetime1 = date_create($fechaServer);
$datetime2 = date_create($fecha . ' 23:59:59');

//echo $fechaServer." | ".$fecha;
if ($datetime2 >= $datetime1) {
    // si la imagen no tiene errores se guarda la noticia
    //Se envia a guardar la noticia
    $idNot = $insert->guardarNoticia($titulo, $descripcion, $fecha, $link, $perfil);
    //Se activa id para cargar imagen y opcion de carga imagen
    echo $idNot;
} else {
    echo '-1';
}

$insert->destruir();
?>