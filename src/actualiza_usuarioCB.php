<?php
session_start();
include '../config/sivisae_class.php';
$cedula = filter_input(INPUT_POST, 'cedula_e');
$nombre = strtoupper($_POST['nombre_e']);
$perfil = $_POST['perfil_e'];
$correo = $_POST['correo_e'];
$telefono = $_POST['telefono_e'];
$celular = $_POST['celular_e'];
$skype = $_POST['skype_e'];
$sede= $_POST['sede_e'];
$id_upd= $_POST['id_e'];
$id_per_upd= $_POST['id_e_p'];


$update = new sivisae_consultas();
//se actualiza el usuario
$resUpd = $update->actualizarUsuario($cedula, $nombre, $correo, $telefono, $celular, $skype, $sede, $id_upd, $id_per_upd, $perfil, $_SESSION["usuarioid"]);
//Se retona el resultado
echo $resUpd;

$update->destruir();