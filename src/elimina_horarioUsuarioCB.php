<?php

session_start();
include '../config/sivisae_class.php';
$dia = "";
switch ($_POST['id_d']) {
    case 1:
        $dia = "lunes";
        break;
    case 2:
        $dia = "martes";
        break;
    case 3:
        $dia = "miercoles";
        break;
    case 4:
        $dia = "jueves";
        break;
    case 5:
        $dia = "viernes";
        break;
    case 6:
        $dia = "sabado";
        break;
}


$update = new sivisae_consultas();
//se actualiza el usuario
$resUpd = $update->eliminarHorarioUsuario($dia, $_SESSION["usuarioid"], $_SESSION["perfilid"]);
//Se retona el resultado
if ($resUpd === "1") {
    echo 'Se eliminó correctamente el horario.';
} else {
    echo 'No se eliminó el horario';
}

$update->destruir();
