<?php
session_start();
include '../config/sivisae_class.php';
$id_upd = $_POST['id_el'];
$update = new sivisae_consultas();
$resUpd = $update->eliminarPerfil($id_upd, $_SESSION["usuarioid"]);
//Se retona el resultado
echo $resUpd;
$update->destruir();
?>