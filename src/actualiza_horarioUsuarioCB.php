<?php

session_start();
include '../config/sivisae_class.php';

$dia = $_POST['dia_e'];
$hora_ini = $_POST['hora_ini'];
$hora_fin = $_POST['hora_fin'];
$jor_ini = $_POST['jornada_hora_ini'];
$jor_fin = $_POST['jornada_hora_fin'];
$id_user = $_POST['id_e'];


$update = new sivisae_consultas();
//se actualiza el usuario
$resUpd = $update->actualizarHorarioUsuario($dia, $hora_ini, $hora_fin, $jor_ini, $jor_fin, $_SESSION["usuarioid"], $_SESSION["perfilid"]);
//Se retona el resultado
if ($resUpd === "1") {
    echo 'Se actulizaron correctamente el horario.';
} else {
    echo 'No se actulizó el horario';
}

$update->destruir();
