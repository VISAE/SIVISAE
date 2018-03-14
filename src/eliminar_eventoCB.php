<?php
session_start();
include '../config/sivisae_class.php';
$id_upd = $_POST['id_e'];
$update = new sivisae_consultas();
$resUpd = $update->eliminarEvento($id_upd, $_SESSION["usuarioid"]);
//Se retona el resultado
echo $resUpd;
$update->destruir();
?>