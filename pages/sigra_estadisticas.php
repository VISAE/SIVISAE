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
                            Estadísticas - SIGRA
                        </h2>
                    </div>
                    <div class="art-postcontent">
                        <div align="center">
                            <table width="100%">
                                <tr>
                                    <td>
                                        <p align="center" id='p_fieldset_autenticacion'> 
                                            <img src="template/imagenes/generales/estadisticas.png" width="100" height="100"></img> 
                                        </p>
                                    </td>
                                    <td>
                                        <p align="left" id='p_fieldset_autenticacion'><?php echo 'Listado General de Graduados'; ?> <a href="../sigra/multimedia/estadisticas/Listado_Graduados.xlsx" >Descargar</a></p>
                                        <p align="left" id='p_fieldset_autenticacion'><?php echo 'Listado de graduados con correos'; ?> <a href="../sigra/multimedia/estadisticas/Listado_Graduados_Correos.xlsx" target="_blank">Descargar</a></p>
                                        <p align="left" id='p_fieldset_autenticacion'><?php echo 'Listado de graduados con direcciones'; ?> <a href="../sigra/multimedia/estadisticas/Listado_Graduados_Direcciones.xlsx" target="_blank">Descargar</a></p>
                                        <p align="left" id='p_fieldset_autenticacion'><?php echo 'Listado de graduados con teléfonos'; ?> <a href="../sigra/multimedia/estadisticas/Listado_Graduados_Telefonos.xlsx" target="_blank">Descargar</a></p>
                                        <p align="left" id='p_fieldset_autenticacion'><?php echo 'Listado de graduados con información laboral'; ?> <a href="../sigra/multimedia/estadisticas/Listado_Graduados_Laboral.xlsx" target="_blank">Descargar</a></p>
                                        <p align="left" id='p_fieldset_autenticacion'><?php echo 'Actividades de Bienestar para Graduados '; ?> <a href="../sigra/multimedia/estadisticas/Actividades_Bienestar_16-1.xlsx" target="_blank">Descargar</a></p>
                                        <p align="left" id='p_fieldset_autenticacion'><?php echo 'Listado de Boletines'; ?> <a href="../sigra/multimedia/estadisticas/Listado_Graduados_Boletines.xlsx" target="_blank">Descargar</a></p>
                                        <p align="left" id='p_fieldset_autenticacion'><?php echo 'Base de Empresas Portal Laboral'; ?> <a href="../sigra/multimedia/estadisticas/Listado_Empresas.xlsx" target="_blank">Descargar</a></p>
                                        <p align="left" id='p_fieldset_autenticacion'><?php echo 'Listado de Graduados Portal Laboral'; ?> <a href="../sigra/multimedia/estadisticas/Listado_Graduados_Portal_Laboral.xlsx" target="_blank">Descargar</a></p>
                                    </td>
                                </tr>
                            </table>                        
                        </div>
                    </div>
                     <div align="center">
                            Última Actualización 30 Junio de 2016
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
