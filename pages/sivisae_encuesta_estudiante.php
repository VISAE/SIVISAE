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

            // Se verifican los parametros de entrada

            $cedula_enc = base64_decode($_GET["ie"]);
            $peraca_enc = base64_decode($_GET["pa"]);
            ?>

            <!--contenedor-->

            <link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
            <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
            <script src="http://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>

            <link rel="stylesheet" type="text/css" href="template/css/estilo_index.css">
            <script src="js/popup/bpopup.js" type="text/javascript"></script>
            <link rel="stylesheet" type="text/css" href="template/popup/estilo_popup.css">
            <link rel="stylesheet" type="text/css" href="template/grilla/estilo_grilla.css">
            <link rel="stylesheet" type="text/css" href="js/Chosen1.4/chosen.css">
            <link rel="stylesheet" type="text/css" href="js/Chosen1.4/chosen.min.css">
            <script src="js/sweetalert-master/dist/sweetalert-dev.js" type="text/javascript" languaje="javascript"></script>
            <link rel="stylesheet" href="js/sweetalert-master/dist/sweetalert.css">
            <script src="js/Chosen1.4/chosen.jquery.js" type="text/javascript" language="javascript"></script>
            <script src="js/Chosen1.4/chosen.jquery.min.js" type="text/javascript" language="javascript"></script>

            <!--scripts de funcionalidad - inicio-->
            <script>

                ///Seguridad - Inicio
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

                    $(".chosen-select").chosen({no_results_text: "Uups, No se encontraron registros!"});
                });

                function nobackbutton() {
                    window.location.hash = "no-back-button";
                    window.location.hash = "Again-No-back-button" //chrome
                    window.onhashchange = function () {
                        window.location.hash = "no-back-button";
                    }
                }
                ///Seguridad - Fin

                ///validaciones - inicio

                $(document).ready(function () {
                    $("#hora").keydown(function (event) {
                        if (event.shiftKey)
                        {
                            event.preventDefault();
                        }

                        if (event.keyCode === 46 || event.keyCode === 8) {
                        }
                        else {
                            if (event.keyCode < 95) {
                                if (event.keyCode < 48 || event.keyCode > 57) {
                                    event.preventDefault();
                                }
                            }
                            else {
                                if (event.keyCode < 96 || event.keyCode > 105) {
                                    event.preventDefault();
                                }
                            }
                        }
                    });
                });

                ///validaciones - fin


                ///logica - inicio
                function listaGrilla() {
                    var data = new FormData();
                    data.append('est_id', <?php echo $cedula_enc ?>);
                    data.append('pa_id', <?php echo $peraca_enc ?>);

                    $.ajax({
                        type: 'POST',
                        url: 'src/encuestaEstudianteCB.php',
                        data: data,
                        cache: false,
                        contentType: false,
                        processData: false,
                        dataType: "HTML",
                        beforeSend: function () {

                        },
                        success: function (data) {
                            $('#list_grilla').html(data);
                            $('#tb_grilla').show();

                            $("#list_grilla").on("click", ".pagination a", function (e) {
                                e.preventDefault();
                                var page = $(this).attr("data-page"); //get page number from link
                                $("#list_grilla").load("src/encuestaEstudianteCB.php", {
                                    "page": page

                                },
                                function () { //get content from PHP page

                                });
                            });
                        }

                    });
                    return false;
                }

            </script>
            <!--scripts de funcionalidad - fin-->


            <!--inicio calendario firefox-->
            <?php
            $navegador = getenv("HTTP_USER_AGENT");
            if (preg_match("/Firefox/i", "$navegador")) {
                ?>


                <script>
                    $(function () {
                        $.datepicker.setDefaults($.datepicker.regional["es"]);
                        $("#fecha").datepicker({
                            firstDay: 1,
                            changeYear: true,
                            changeMonth: true,
                            dateFormat: 'yy/mm/dd',
                            yearRange: '-0:+0'
                        });
                    });
                </script>

                <style>
                    /*div principal del datepicker*/
                    .ui-datepicker
                    {
                        width: auto;
                        background: #004669;
                    }

                    /*Tabla con los días del mes*/
                    .ui-datepicker table
                    {
                        font-size: 9px;
                    }

                    /*La cabecera*/
                    .ui-datepicker .ui-datepicker-header
                    {
                        font-size: 10px;
                        background: #FFFFFF;
                    }

                    /*Para los días de la semana: Sa Mo ... */
                    .ui-datepicker th
                    {
                        color: #FFFFFF;
                    }

                    /*Para items con los días del mes por defecto */
                    .ui-datepicker .ui-state-default
                    {
                        background: #FFFFFF;
                    }

                    /*Para el item del día del mes seleccionado */
                    .ui-datepicker .ui-state-active
                    {
                        background: orange;
                        color: #FFFFFF;
                    }
                </style>

                <?php
            }
            ?>
            <!--fin calendario firefox-->

        </head>

        <body onload="nobackbutton();
                        listaGrilla();">
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
                    <!--opciones fin-->

                    <!--listado de inicio-->

                    <div align="center" style="background-color: #004669">
                        <h2 id='p_fieldset_autenticacion_2'>
                            DETALLE ENCUESTA ESTUDIANTE A EVALUACIÓN DE LA INDUCCIÓN 
                        </h2>
                    </div>

                    <div align="center" id="list_grilla"></div>

                    <!--listado de fin-->

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
