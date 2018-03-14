<?php
session_start();
include '../config/sivisae_class.php';
$id_eliminar = $_POST['id_e'];
$elimina = new sivisae_consultas();
$resElm = $elimina->eliminarSeguimiento($id_eliminar, $_SESSION["usuarioid"]);
//Se retona el resultado
echo $resElm;
$elimina->destruir();
?>