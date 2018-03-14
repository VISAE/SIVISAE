<?php

session_start();
include '../config/sivisae_class.php';
include '../src/mail_config.php';

$cedula = filter_input(INPUT_POST, 'cedula');
$nombre = strtoupper($_POST['nombre']);
$usuario = $_POST['usuario'];
$perfil = $_POST['perfil'];
$correo = $_POST['correo'];
$telefono = $_POST['telefono'];
$celular = $_POST['celular'];
$skype = $_POST['skype'];
$sede = $_POST['sede'];

$insert = new sivisae_consultas();

$cont_usuario = $insert->validarLoginUsuario($usuario);
//Si es mayor que cero, se reasigna el login de usuario para que no se repita
if ($cont_usuario > 0) {
    $cont_usuario++;
    $usuario = $usuario . '' . $cont_usuario;
}
$usuarioid = $insert->crearUsuario($cedula, $nombre, $usuario, $correo, $telefono, $celular, $skype, $sede);
$enviar = new mail_config();

//echo $usuarioid;
if ($usuarioid != NULL) {
    $usu_per_id = $insert->crearUsuarioPerfil($usuarioid, $perfil);
    if ($perfil === '2' || $perfil === '3' || $perfil === '4') {
        $insert->crearAuditor($usuarioid, $perfil);
    }

    if ($perfil === '5' || $perfil === '6' || $perfil === '7') {
        $insert->crearConsejero($usuarioid, $perfil);
    }

    $pass = $insert->generarPass($usuarioid);
    $envio = $enviar->enviarPass("Credenciales para ingreso al SIVISAE", $usuario, $pass, $correo, $nombre);
    if ($envio === '1') {
        $insert->registrarAccion($usuarioid, "CREAR USUARIO", "EXISTOSO");
        echo "El usuario fue creado exitosamente. Las credenciales serán enviadas al correo del usuario.";
    } else {
        echo "No se pudo enviar el correo al usuario, por favor intente nuevamente creando el usuario.";
        //rollback
        $insert->rollbackUsuario($usuarioid, $usu_per_id, $_SESSION["usuarioid"]);
    }
} else {
    echo "No se pudo crear el usuario";
}
