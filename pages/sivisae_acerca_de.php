<?php
session_start();
include_once '../config/sivisae_class.php';
$consulta = new sivisae_consultas();
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
            <?php include "../template/sivisae_head_home.php"; ?>
            <!--Encabezado - Fin-->


            <!--Menu - Inicio-->
            <?php include "sivisae_menu.php"; ?>
            <!--Menu - Fin-->
            <main>
                <!--aqui contenido incio-->
                <div >

                    <!--Barra de estado inicio-->
                    <?php include "sivisae_barra_estado.php"; ?>
                    <!--Barra de estado fin-->
                    <div align="center">
                        <h2 id='p_fieldset_autenticacion'>
                            Acerca del SIVISAE
                        </h2>
                    </div>
                    <div class="art-postcontent">
                        <div align="center">
                            <table width="100%">
                                <tr>
                                    <td>
                                        <p align="center" id='p_fieldset_autenticacion'> 
                                            <img src="template/imagenes/generales/banner_acerca_de.png" width="100" height="100"></img> 
                                        </p>
                                    </td>
                                    <td>
                                        <p align="center" id='p_fieldset_autenticacion'><?php echo '<br>Vicerrectoría de Servicios a Aspirantes Estudiantes y Egresados <br> Auditoria de Servicios a los Estudiantes <br><br> <strong>Diseñado Por:</strong> <br> Ing. Andrés Camilo Méndez Aguirre <br> <strong>Soporte Técnico:</strong> <br> correo: andres.mendez@unad.edu.co <br> skype: andrescmendeza <br><br> <strong>UNIVERSIDAD NACIONAL ABIERTA Y A DISTANCIA - UNAD - 2016</strong> <br> Todos los derechos Reservados'; ?></p>
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
            include "../template/sivisae_footer_home.php";
            ?>
        </body>
        <?php
    }
}
$consulta->destruir();
?>
