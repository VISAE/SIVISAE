<?php

session_start();
include '../config/sivisae_class.php';

$correo = $_POST['correo'];
$telefono = $_POST['telefono'];
$celular = $_POST['celular'];
$skype = $_POST['skype'];
$f_nac = $_POST['f_nac'];


$update = new sivisae_consultas();
//se actualiza el usuario
$resUpd = $update->actualizarPerfilUsuario($correo, $telefono, $celular, $skype, $f_nac, $_SESSION["usuarioid"]);
//Se retona el resultado
if ($resUpd === "1") {
    $_SESSION["correo"] = $correo;
    $_SESSION["telefono"] = $telefono;
    $_SESSION["celular"] = $celular;
    $_SESSION["skype"] = $skype;
    $_SESSION["fecha_nac_compl"] = $f_nac;
    $_SESSION["actualiza_datos"] = 0;
    echo 'Se actulizaron correctamente los datos de usuario.';
} else {
    echo 'No se actulizaron los datos de usuario, por favor verifique e intente nuevamente.';
}

$update->destruir();
