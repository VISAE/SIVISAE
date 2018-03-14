<?php

session_start();
include_once '../config/sivisae_class.php';
$consulta = new sivisae_consultas();


if (isset($_POST['opcion'])) {
    $opcion = $_POST['opcion'];
    $cedula_at = $_POST['cedula_at'];
    $cat_atencion = isset($_POST['cat_atencion']) && $_POST['cat_atencion'] != '' ? implode(",", $_POST['cat_atencion']) : "T";
    $atencion_b = $_POST['atencion_b'];
    $observacion=$_POST['observacion'];
    if ($opcion == "1") {
        //Registra aspirante
        $nombre = $_POST['nombre'];
        $correo = $_POST['correo'];
        $programa_at = $_POST['programa_at'];
        $centro_at = $_POST['centro_at'];
        $telefono = $_POST['telefono'];
        $direccion = $_POST['direccion'];
        $consulta->registraAspirante($nombre, $correo, $programa_at, $centro_at, $telefono, $direccion, $cedula_at, $_SESSION['usuarioid']);
    }
    $consulta->registraAtencion($cedula_at, $cat_atencion, $atencion_b, $_SESSION['usuarioid'], $_SESSION["perfilid"], $observacion);

    echo '<script type="text/javascript" language="javascript">
        $(document).ready(function () {
                    swal({
                title: "La Atención ha sido registrada",
                text: "",
                type: "success",
                confirmButtonColor: "#004669",
                confirmButtonText: "Aceptar"
            },
            function () {
                window.location.href = "pages/sivisae_registro_atenciones.php?op=4";
            });
            });
        </script>';
}
?>

