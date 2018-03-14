<?php
session_start();
include_once '../config/sigra_class.php';
$consulta = new sigra_consultas();
?>
<head>
    <?php
    $browser = getenv("HTTP_USER_AGENT");
    if (preg_match("/MSIE/i", "$browser")) {
        //Navegadores no compatibles
        ?>
        <script language="JavaScript" type="text/JavaScript">
            window.location = "sigra_notifica.php?e=X01";
        </script>

        <?php
    } else {
        //Navegadores compatibles
        // Se valida inicio de sesion

        if (!isset($_SESSION['usuarioid'])) {
            //Debe iniciar sesion
            header("Location: " . RUTA_PPAL . "pages/sigra_notifica.php?e=X02");
        } else {
            include "../template/sigra_link_home.php";
            ?>

            <!--contenedor-->
            <link rel="stylesheet" type="text/css" href="template/css/estilo_index.css">

            <!--bloqueo hacia atrás-->
            <script>
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
            <?php include "../template/sigra_head_home.php"; ?>
            <!--Encabezado - Fin-->



            <main>
                <!--aqui contenido incio-->
                <div >
                    <!--Menu - Inicio-->
                    <?php include "sigra_menu.php"; ?>
                    <!--Menu - Fin-->
                    <!--Barra de estado inicio-->
                    <?php include "sigra_barra_estado.php"; ?>
                    <!--Barra de estado fin-->
                    <div align="center">
                        <h2 id='p_fieldset_autenticacion'>
                            Material de Ayuda
                        </h2>
                    </div>
                    <div class="art-postcontent">
                        <div align="center">
                            <table width="100%">
                                <tr>
                                    <td>
                                        <p align="center" id='p_fieldset_autenticacion'> 
                                            <img src="template/imagenes/generales/ayuda_banner.png" width="100" height="100"></img> 
                                        </p>
                                    </td>
                                    <td>
                                        <p align="center" id='p_fieldset_autenticacion'><?php echo 'Manual de Usuario'; ?> <a href="../sigra/multimedia/documentos/SIGRA_Manual_usuario_final.pdf" target="_blank">Descargar</a></p>
                                        <p align="center" id='p_fieldset_autenticacion'><?php echo 'Manual de Administrador'; ?> <a href="../sigra/multimedia/documentos/SIGRA_Manual_usuario_Administrador.pdf" target="_blank">Descargar</a></p>
                                        <p align="center" id='p_fieldset_autenticacion'><?php echo 'Acceso Observatorio Laboral'; ?> <a href="http://www.graduadoscolombia.edu.co/encuesta/maestro.php" target="_blank">Abrir</a></p>
                                        <p align="center" id='p_fieldset_autenticacion'><?php echo 'Encuesta Momentos'; ?> <a href="http://www.graduadoscolombia.edu.co/encuesta/" target="_blank">Abrir</a></p>
                                        <p align="center" id='p_fieldset_autenticacion'><?php echo 'Plan de Trabajo 16-2'; ?> <a href="../sigra/multimedia/documentos/PLAN_DE_TRABAJO_GRADUADOS_2016_II.xlsx" target="_blank">Descargar</a></p>
                                        <p align="center" id='p_fieldset_autenticacion'><?php echo 'Plan de Mejoramiento 16-2'; ?> <a href="../sigra/multimedia/documentos/Plan_de_mejora_Graduados.xlsx" target="_blank">Descargar</a></p>
                                    </td>
                                </tr>
                            </table>                        
                        </div>
                    </div>
                </div>
                <!--aqui contenido fin-->
            </main>

            <?php
            //Pie de pagina
            include "../template/sigra_footer_home.php";
            ?>
        </body>
        <?php
    }
}
$consulta->destruir();
?>
