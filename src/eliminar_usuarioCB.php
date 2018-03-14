<?php
session_start();
include '../config/sivisae_class.php';
$id_upd = $_POST['id_e'];
$id_per_upd = $_POST['id_e_p'];
$update = new sivisae_consultas();
$resUpd = $update->eliminarUsuario($id_upd, $id_per_upd, $_SESSION["usuarioid"]);
//Se retona el resultado
echo $resUpd;
$update->destruir();
?>