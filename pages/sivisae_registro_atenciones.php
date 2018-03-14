<?php
session_start();
include_once '../config/sivisae_class.php';
$consulta = new sivisae_consultas();
$_SESSION["modulo"] = $_GET["op"];
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

            //Se configuran los permisos (crear, editar, eliminar)
            $modulo = $_GET["op"];

            if ($modulo != "") {
                $copy = 0;
                $edit = 0;
                $delete = 0;
                $permisos = $consulta->permisos($modulo, $_SESSION["perfilid"]);
                while ($row = mysql_fetch_array($permisos)) {
                    $copy = $row[0];
                    $edit = $row[1];
                    $delete = $row[2];
                }
            } else {
                $copy = 0;
                $edit = 0;
                $delete = 0;
            }

            //Se configuran imagenes y acceso de los iconos
            if ($copy == 0) {
                $class_copy = "boton_c_bloq";
                $disabled_copy = "disabled";
            } else {
                $class_copy = "boton_c";
                $disabled_copy = "";
            }

            if ($edit == 0) {
                $class_edit = "boton_e_bloq";
                $disabled_edit = "disabled";
            } else {
                $class_edit = "boton_e";
                $disabled_edit = "";
            }
            if ($delete == 0) {
                $class_delete = "boton_el_bloq";
                $disabled_delete = "disabled";
            } else {
                $class_delete = "boton_el";
                $disabled_delete = "";
            }

            $_SESSION['opc_ed'] = "class='$class_edit' $disabled_edit";
            $_SESSION['opc_el'] = "class='$class_delete' $disabled_delete";


            include "../template/sivisae_link_home.php";
            $respuesta = $consulta->perfiles();
            $sedes = $consulta->traerSedes();
            ?>

            <!--contenedor-->
            <link rel="stylesheet" type="text/css" href="template/css/estilo_index.css">
            <link rel="stylesheet" type="text/css" href="js/Chosen1.4/chosen.css">
            <link rel="stylesheet" type="text/css" href="js/Chosen1.4/chosen.min.css">
            <link href="js/iCheck/polaris/polaris.css" rel="stylesheet">
            <link type="text/css" rel="stylesheet" href="js/introLoader/css/introLoader.min.css" />
            <link rel="stylesheet" href="template/popup/style.min.css">
            <script src="js/popup/bpopup.js" type="text/javascript"></script>
            <link rel="stylesheet" type="text/css" href="template/popup/estilo_popup.css">
            <link rel="stylesheet" type="text/css" href="template/grilla/estilo_grilla.css">
            <script src="js/sweetalert-master/dist/sweetalert-dev.js" type="text/javascript" languaje="javascript"></script>
            <link rel="stylesheet" href="js/sweetalert-master/dist/sweetalert.css">
            <link rel="stylesheet" href="js/Contador_TextArea/style.css">
            <link rel="stylesheet" href="js/knob/css/style.css">
            <script src="js/iCheck/icheck.js"></script>
            <script src="js/introLoader/jquery.introLoader.js" type="text/javascript" language="javascript"></script>
            <script src="js/introLoader/spin.min.js" type="text/javascript" language="javascript"></script>
            <script src="js/Chosen1.4/chosen.jquery.js" type="text/javascript" language="javascript"></script>
            <script src="js/Chosen1.4/chosen.jquery.min.js" type="text/javascript" language="javascript"></script>
            <script src="js/flipclock/compiled/flipclock.min.js" type="text/javascript" language="javascript"></script>
            <script type="text/javascript" src="js/qtip/jquery.qtip.js"></script>
            <script src='js/varios/script-registrar-atencion.js' type='text/javascript' language='javascript'></script>
            <style type="text/css" class="init">

            </style>





            <!--scripts de funcionalidad - inicio-->
            <script>
        $(document).ready(function () {
            $("#cat_atencion, #centro_at, #programa_at").chosen();

            $(".chosen-select-deselect").chosen({allow_single_deselect: true});
            $(".chosen-select").chosen({no_results_text: "No se encontraron registros!"});
            $('#cat_atencion').chosen().change(function () {
                filtros($(this).prop("id").toLowerCase());
            });


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

        function submitConsultarDocumento() {
            if ($('#cedula').val() !== '') {

                var cedula = $('#cedula').val();
                var parametros = {
                    "documento": cedula
                };
                $.ajax({
                    data: parametros,
                    url: 'src/consulta_registroAtencionesCB.php',
                    type: 'POST',
                    beforeSend: function () {
                        $("#carg").introLoader({
                            animation: {
                                name: 'simpleLoader',
                                options: {
                                    stop: false,
                                    fixed: false,
                                    exitFx: 'fadeOut',
                                    ease: "linear",
                                    style: 'light',
                                    delayBefore: 250,
                                }
                            }
                        });
                    },
                    success: function (response) {
                        var loader = $('#carg').data('introLoader');
                        loader.stop();
                        $("#result").show();
                        $("#result").html(response);
                        $("#cat_atencion, #centro_at, #programa_at").chosen();
                        document.getElementById("cedula").readOnly = true;
                    }
                });
                return false;

            } else {
                swal({
                    title: '¡Debe ingresar el número de documento!',
                    text: '',
                    type: 'error',
                    confirmButtonColor: '#004669',
                    confirmButtonText: 'Aceptar'
                });
                $('#cedula').focus();
                return false;
            }
        }

        function startLoad() {
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
            var loader = $('#dynElement').data('introLoader');
            loader.stop();
        }

        ///validaciones - fin

            </script>
            <!--scripts de funcionalidad - fin-->

        </head>

        <body onload="nobackbutton();">
            <!--Encabezado - Inicio-->
            <?php include "../template/sivisae_head_home.php"; ?>
            <!--Encabezado - Fin-->
            <!--Menu - Inicio-->
            <?php include "sivisae_menu.php"; ?>
            <!--Menu - Fin-->
            <main>
                <div>

                    <!--Barra de estado inicio-->
                    <?php include "sivisae_barra_estado.php"; ?>
                    <!--Barra de estado fin-->
                    <!--opciones inicio-->
                    <!--opciones fin-->


                    <!--listado de usuarios inicio-->

                    <div align="center" style="background-color: #004669">
                        <h2 id='p_fieldset_autenticacion_2'>
                            Registro de Atenciones
                        </h2>
                    </div>

                    <div class="art-postcontent">
                        <div align="center">
                            <form id="consultar_documento" name="consultar_documento" method="post" onsubmit="return submitConsultarDocumento()" action="src/consulta_registroAtencionesCB.php">
                                <div style="background-color: #ffffff">
                                    <table style="width: 400px">
                                        <tr>
                                            <td><label for="cedula">Cédula (Aspirante, Estudiante o Graduado/Egresado) (*):</label></td>
                                            <td><input style="width: 180px;" id="cedula" name="cedula" type="text" maxlength="15"/></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <p><input class="submit_fieldset_autenticacion" type="submit" value="Consultar"/></p>
                                                <div align="center" id="result"></div>
                                                <div id="spinner" align="center" style="display:none;">
                                                    <img id="img-spinner" width="50" height="50" src="template/imagenes/generales/loading.gif" alt="Loading"/>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </form>                  
                        </div>
                    </div>
                    <div id="dynElement"></div>
                    <div id="carg" align="center"></div>
                    <div align="center" id="result"></div>
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
