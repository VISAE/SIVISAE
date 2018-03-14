<?php
session_start();
include_once '../config/sivisae_class.php';
$consulta = new sivisae_consultas();
$usr_id = $_SESSION['usuarioid'];
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



            // opcion directorio
            $class_directory = "boton_d";
            $disabled_directory = "";


            include "../template/sivisae_link_home.php";
            $respuesta = $consulta->perfiles();
            $sedes = $consulta->traerSedes();
            ?>

            <!--contenedor-->
            <link rel="stylesheet" type="text/css" href="template/css/estilo_index.css">
            <link rel="stylesheet" type="text/css" href="js/Chosen1.4/chosen.css">
            <link rel="stylesheet" type="text/css" href="js/Chosen1.4/chosen.min.css">
            <link type="text/css" rel="stylesheet" href="js/qtip/jquery.qtip.css" />
            <link type="text/css" rel="stylesheet" href="js/introLoader/css/introLoader.min.css" />
            <link rel="stylesheet" href="template/popup/style.min.css">
            <script src="js/popup/bpopup.js" type="text/javascript"></script>
            <link rel="stylesheet" type="text/css" href="template/popup/estilo_popup.css">
            <link rel="stylesheet" type="text/css" href="template/grilla/estilo_grilla.css">
            <script src="js/Chosen1.4/chosen.jquery.js" type="text/javascript" language="javascript"></script>
            <script src="js/Chosen1.4/chosen.jquery.min.js" type="text/javascript" language="javascript"></script>
            <script src="js/sweetalert-master/dist/sweetalert-dev.js" type="text/javascript" languaje="javascript"></script>
            <link rel="stylesheet" href="js/sweetalert-master/dist/sweetalert.css">
            <script type="text/javascript" src="js/qtip/jquery.qtip.js"></script>
            <script src="js/introLoader/jquery.introLoader.js" type="text/javascript" language="javascript"></script>
            <script src="js/introLoader/spin.min.js" type="text/javascript" language="javascript"></script>
            <script src="js/popup/bpopup.js" type="text/javascript"></script>
            <link rel="stylesheet" type="text/css" href="template/popup/estilo_popup.css">

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
                    traerNotif();
                });

                function nobackbutton() {
                    window.location.hash = "no-back-button";
                    window.location.hash = "Again-No-back-button"; //chrome
                    window.onhashchange = function () {
                        window.location.hash = "no-back-button";
                    }
                }

                function startLoad() {
                    $('#list_estudiantes').hide();
                    $("#dynElement").introLoader({
                        animation: {
                            name: 'simpleLoader',
                            options: {
                                stop: false,
                                fixed: false,
                                exitFx: 'fadeOut',
                                ease: "linear",
                                style: 'light'
                            }
                        },
                        spinJs: {
                            lines: 13, // The number of lines to draw 
                            length: 20, // The length of each line 
                            width: 10, // The line thickness 
                            radius: 30, // The radius of the inner circle 
                            corners: 1, // Corner roundness (0..1) 
                            color: '#004669', // #rgb or #rrggbb or array of colors 
                        }
                    });
                }
                function stopLoad() {
                    $('#list_estudiantes').show();
                    var loader = $('#dynElement').data('introLoader');
                    loader.stop();
                }

                function traerNotif() {
                    $.ajax({
                        async: true,
                        type: "POST",
                        url: "src/notificacionesCB.php",
                        data: "accion=lista",
                        dataType: "html",
                        success: function (data) {
                            $('#list_notificaciones').html(data);
                            $("#list_notificaciones").on("click", ".pagination a", function (e) {
                                //                                    $("#spinner").show();
                                e.preventDefault();
                                var page = $(this).attr("data-page"); //get page number from link
                                $("#list_notificaciones").load("src/notificacionesCB.php", {
                                    "page": page,
                                    "accion": "lista"
                                },
                                function () { //get content from PHP page 
                                    //                                        $("#spinner").hide();
                                });
                            });
                        }
                    });
                }
            </script>
            <!--scripts de funcionalidad - fin-->

        </head>
        <body onload="nobackbutton();
              ">
            <!--Encabezado - Inicio-->
            <?php include "../template/sivisae_head_home.php"; ?>
            <!--Encabezado - Fin-->
            <!--Menu - Inicio-->
            <?php include "sivisae_menu.php"; ?>
            <!--Menu - Fin-->
            <main>

                <div >

                    <!--Barra de estado inicio-->
                    <?php include "sivisae_barra_estado.php"; ?>
                    <!--Barra de estado fin-->
                    <!--opciones inicio-->
                    <!--opciones fin-->
                    <!--listado de usuarios inicio-->

                    <div align="center" style="background-color: #004669">
                        <h2 id='p_fieldset_autenticacion_2'>
                            NOTIFICACIONES RECIBIDAS 
                        </h2>
                    </div>
                    <div class="art-postcontent">
                        <div align="center">
                            <form id="frm_notificaciones" name="frm_notificaciones">
                                <br>

                                <div id="dynElement" >
                                </div>
                                <div id="list_notificaciones" align='center'>

                                </div>
                            </form>
                        </div>
                    </div>
                </div>

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
