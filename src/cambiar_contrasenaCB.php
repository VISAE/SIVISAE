<?php
session_start();
include '../config/sivisae_class.php';
include '../src/mail_config.php';
$apunta = new sivisae_consultas();
if (isset($_POST['password_old'])) {
    $contrasena_old = $_POST['password_old'];
    $contrasena_new = $_POST['password_new'];
    $usuarioid = $_SESSION['usuarioid'];
    //Se instancia clase de transacciones
    $resMS = $apunta->cambioPass($usuarioid, $contrasena_new, $contrasena_old);

    //Se obtiene el resultado de la transaccion
    while ($row = mysql_fetch_array($resMS)) {
        $tx_rta = $row[0];
    }

    switch ($tx_rta) {
        case 0:
            echo 'No se realizó la transacción, intente nuevamente.';
            break;
        case 1:
            echo 'Se cambió la contraseña correctamente.';
            break;
        case 2:
            echo 'El usuario ingresado no existe.';
            break;
        case 3:
            echo 'La contraseña actual es incorrecta.';
            break;
        case 4:
            echo 'El usuario esta inactivo, contacte al admministrador.';
            break;
    }
} else {

    echo "No se pudo recuperar el usuario";
}
//Se cierra la conexion
$apunta->destruir();
//Se destruyen posibles variables de session
session_destroy();
?>
