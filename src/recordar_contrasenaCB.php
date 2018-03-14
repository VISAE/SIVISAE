<?php

include '../config/sivisae_class.php';
include '../src/mail_config.php';
$apunta = new sivisae_consultas();
$enviar = new mail_config();
//Se recibio usuario
$usuario_cambio = $_POST['usuario'];

$res = $apunta->traeUsrId($usuario_cambio);
if (mysql_num_rows($res)<=0) {
    echo '0';
} else {
    while ($row = mysql_fetch_array($res)) {
        $usuarioid = $row[0];
        $nombre = $row[1];
        $correo = $row[2];
        $pass = $apunta->generarPass($usuarioid);
        if (strlen($pass) > 1) {
            $envio = $enviar->enviarPass("Recuperar contrase" . chr(241) . "a para ingreso al SIVISAE", $usuario_cambio, $pass, $correo, $nombre);
            if ($envio === '1') {
                echo "Se generó una nueva contrase" . chr(241) . "a que ha sido enviada al correo";
            } else {
                echo "No se pudo enviar el mensaje";
            }
        } else {
            echo "No se pudo generar la nueva contrase" . chr(241) . "a";
        }
        break;
    }
}
//Se cierra la conexion
$apunta->destruir();
?>
