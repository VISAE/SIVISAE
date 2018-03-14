<!DOCTYPE html>
<?php
session_start();
include_once '../config/sivisae_class.php';
$consulta = new sivisaeconsultas();
?>
<head>
    <?php
    $browser = getenv("HTTP_USER_AGENT");
    if (preg_match("/MSIE/i", "$browser")) {
        //Navegadores no compatibles
        ?>
        <script language="JavaScript" type="text/JavaScript">
            window.location = "sivisae_notifica.php?e=X01";
        </script>

        <?php
    } else {
        //Navegadores compatibles
        // Se valida inicio de sesion

        if (!isset($_SESSION['usuarioid'])) {
            //Debe iniciar sesion
            header("Location: " . RUTA_PPAL . "pages/sivisae_notifica.php?e=X02");
        } else {
            include "../template/sivisae_link_home.php";
            ?>

            <!--contenedor-->
            <link rel="stylesheet" type="text/css" href="template/css/estilo_index.css">
            <link rel="stylesheet" type="text/css" href="js/Chosen1.4/chosen.css">
            <link rel="stylesheet" type="text/css" href="js/Chosen1.4/chosen.min.css">
            <link href="js/iCheck/polaris/polaris.css" rel="stylesheet">

            <link rel="stylesheet" href="template/popup/style.min.css">
            <script src="js/popup/bpopup.js" type="text/javascript"></script>
            <link rel="stylesheet" type="text/css" href="template/popup/estilo_popup.css">
            <link rel="stylesheet" type="text/css" href="template/grilla/estilo_grilla.css">
            <script src="js/sweetalert-master/dist/sweetalert-dev.js" type="text/javascript" languaje="javascript"></script>
            <link rel="stylesheet" href="js/sweetalert-master/dist/sweetalert.css">

            <script src="js/iCheck/icheck.js"></script>
            <script src="js/introLoader/jquery.introLoader.js" type="text/javascript" language="javascript"></script>
            <script src="js/introLoader/spin.min.js" type="text/javascript" language="javascript"></script>
            <script src="js/Chosen1.4/chosen.jquery.js" type="text/javascript" language="javascript"></script>
            <script src="js/Chosen1.4/chosen.jquery.min.js" type="text/javascript" language="javascript"></script>

            <script type="text/javascript" language="javascript">
                $(document).ready(function () {
                    $(document).on('contextmenu', function (e) {
                        swal({
                            title: '¡Cuidado!',
                            text: 'El clic derecho esta deshabilitado en esta página',
                            type: 'error',
                            confirmButtonColor: '#004669',
                            confirmButtonText: 'Aceptar'
                        });
                        return false;
                    });
                    $('.sel_zona').hide();
                });
                function nobackbutton() {
                    window.location.hash = "no-back-button";
                    window.location.hash = "Again-No-back-button" //chrome
                    window.onhashchange = function () {
                        window.location.hash = "no-back-button";
                    }
                }
            </script>
        </head>
        <body onload="nobackbutton();">
            <!--Encabezado - Inicio-->
            <?php include "../template/sivisae_head_home.php"; ?>
            <!--Encabezado - Fin-->
            <!--Menu - Inicio-->
            <?php include "sivisae_menu.php"; ?>
            <!--Menu - Fin-->
            <main>
                <!--aqui contenido incio-->
                <div >

                    <!--Barra de estado inicio-->
                    <?php
                    include "sivisae_barra_estado.php";
                    // echo "Tu dirección IP es: {$_SERVER['REMOTE_ADDR']}";
                    ?>
                    <!--Barra de estado fin-->
                    <div align="center" style="background-color: #004669" >
                        <h2 id='p_fieldset_autenticacion_2'>
                            Estudiantes asignados
                        </h2>
                    </div>
                    <div class="art-postcontent">
                        <div align="center">
                            <form id="estudiantes_asignados" name="estudiantes_asignados">
                                <br>
                                <?php
                                include "sivisae_filtro.php";
                                // echo "Tu dirección IP es: {$_SERVER['REMOTE_ADDR']}";
                                ?>
                                <div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
        </body>

        <?php
    }
}
$consulta->destruir();
?>
