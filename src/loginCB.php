<?php

session_start();

include $_SERVER['DOCUMENT_ROOT'] . "/sivisae/config/sivisae_class.php";
$cod_Tx = '';
$redireccionar = 'Location: ' . RUTA_PPAL . 'pages/sivisae_notifica.php?e=';

if (isset($_POST['usuario']) && isset($_POST['password'])) {
    //Se recibio usuario
    $usuario_recibido = $_POST['usuario'];
    $contrasena_recibida = $_POST['password'];
    //Se instancia clase de transacciones

    $apunta = new sivisae_consultas();
    $resMS = $apunta->inicioSesion($usuario_recibido, $contrasena_recibida);
    if ($resMS === FALSE) {
        die(mysql_error()); // TODO: better error handling
        echo 'NO PASA';
    } else {
        $codigo = null;
        while ($fila = mysql_fetch_array($resMS)) {
            $codigo = $fila[0];
            if ($codigo == 1) {
                $_SESSION["usuarioid"] = $fila[1];
                $_SESSION["ced"] = $fila[2];
                $_SESSION["nom"] = $fila[3];
                $_SESSION["login"] = $fila[4];
                $_SESSION["cambio_pass"] = $fila[5];
                $_SESSION["sede"] = $fila[6];
                $_SESSION["perfil"] = $fila[7];
                $_SESSION["perfilid"] = $fila[8];
                $_SESSION["fecha_nac"] = $fila[9];
                $_SESSION["fecha_server"] = $fila[10];
                $_SESSION["programa_usuario"] = $fila[11];
                $_SESSION["opc_ed"] = "";
                $_SESSION["opc_el"] = "";
                $_SESSION["notificaciones"] = "";
                $_SESSION["modulo"] = "0";
                $_SESSION["correo"] = $fila[12];
                $_SESSION["telefono"] = $fila[13];
                $_SESSION["celular"] = $fila[14];
                $_SESSION["skype"] = $fila[15];
                $_SESSION["fecha_nac_compl"] = $fila[16];
                $_SESSION["actualiza_datos"] = $fila[17];
            }
        }
        switch ($codigo) {
            case '0':
                $cod_Tx = 'X02';
                break;
            case '1':
                if ($_SESSION["cambio_pass"] == 0) {
                    if ($_SESSION["actualiza_datos"] == 0) {
                        $redireccionar = 'Location: ' . RUTA_PPAL . 'pages/sivisae_home.php/';
                    } else {
                        $redireccionar = "Location: " . RUTA_PPAL . "pages/sivisae_perfil_usuario.php";
                    }
                } else {
                    $redireccionar = "Location: " . RUTA_PPAL . "pages/sivisae_cambiar_contrasena.php?p=1";
                }
                break;
            case '2':
                $redireccionar.='X02';
                break;
            case '3':
                $redireccionar.='X03';
                break;
            case '4':
                $redireccionar.='X04';
                break;
            case '5':
                $redireccionar.='X05';
                break;
        }
    }
    //Se cierra la conexion
    $apunta->destruir();
} else {
    //No se recibio usuario
    $redireccionar.='X02';
}
//Se redirecciona según código

header($redireccionar);
?>
