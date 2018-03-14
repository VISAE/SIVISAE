<?php
/*
 * 
 *   @author Ing. Andres Mendez
 * 
 */

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


            include "../template/sigra_link_home.php";
            $respuesta = $consulta->perfiles();
            $sedes = $consulta->traerSedes();
            $sintilde = explode(',', SIN_TILDES);
            $tildes = explode(',', TILDES);
            ?>

            <!--contenedor-->
            <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
            <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
            <link rel="stylesheet" type="text/css" href="template/css/estilo_index.css">
            <script src="js/popup/bpopup.js" type="text/javascript"></script>
            <link rel="stylesheet" type="text/css" href="template/popup/estilo_popup.css">
            <link rel="stylesheet" type="text/css" href="template/grilla/estilo_grilla.css">
            <link rel="stylesheet" type="text/css" href="js/Chosen1.4/chosen.css">
            <link rel="stylesheet" type="text/css" href="js/Chosen1.4/chosen.min.css">
            <script src="js/sweetalert2-master/dist/sweetalert2.min.js" type="text/javascript" languaje="javascript"></script>
            <link rel="stylesheet" href="js/sweetalert2-master/dist/sweetalert2.css">
            <script src="js/Chosen1.4/chosen.jquery.js" type="text/javascript" language="javascript"></script>
            <script src="js/Chosen1.4/chosen.jquery.min.js" type="text/javascript" language="javascript"></script>
            <link type="text/css" rel="stylesheet" href="js/introLoader/css/introLoader.min.css" />
            <script src="js/introLoader/jquery.introLoader.js" type="text/javascript" language="javascript"></script>
            <script src="js/introLoader/spin.min.js" type="text/javascript" language="javascript"></script>
            <script src="js/sigra/validaciones.js" type="text/javascript" language="javascript"></script>
            <script src="js/sigra/script-cargar-archivos.js" type="text/javascript" language="javascript"></script>
            <script src="js/sigra/js-asistencia.js" type="text/javascript" language="javascript"></script>
            <script src="js/sigra/tools.js" type="text/javascript" language="javascript"></script>
            <script src="js/jquery-validation/dist/jquery.validate.js" type="text/javascript" language="javascript"></script>
            <link href="js/iCheck/polaris/polaris.css" rel="stylesheet">
            <script src="js/iCheck/icheck.js"></script>
            <!--scripts de funcionalidad - inicio-->
            <script >

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

            $(".chosen-select").chosen({no_results_text: "Uups, No se encontraron registros!", width: "200px"});
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
            $("#documentos, #tel, #cel").keydown(function (event) {
                if (event.shiftKey)
                {
                    event.preventDefault();
                }

                if (event.keyCode === 46 || event.keyCode === 8) {
                } else {
                    if (event.keyCode < 95) {
                        if (event.keyCode < 48 || event.keyCode > 57) {
                            event.preventDefault();
                        }
                    } else {
                        if (event.keyCode < 96 || event.keyCode > 105) {
                            event.preventDefault();
                        }
                    }
                }
            });
        });
        ///validaciones - fin
            </script>
            <style>
                .listo {
                    background-color: #00A600;
                    font-family: tahoma;
                    font-size: 13px;
                    color: #EAEAEA;
                }
            </style>
        </head>

        <body onload="nobackbutton();">
            <!--Encabezado - Inicio-->
        <?php include "../template/sigra_head_home.php"; ?>
            <!--Encabezado - Fin-->

            <main>

                <div >
                    <!--Menu - Inicio-->
        <?php include "sigra_menu.php"; ?>
                    <!--Menu - Fin-->
                    <!--Barra de estado inicio-->
                    <?php include "sigra_barra_estado.php"; ?>
                    <!--Barra de estado fin-->
                    <!--opciones inicio-->


                    <!--opciones fin-->


                    <!--listado de inicio-->

                    <div align="center" style="background-color: #004669">
                        <h2 id='p_fieldset_autenticacion_2'>
                            CONFIRMAR ASISTENCIA
                        </h2>
                    </div>
                    <div style="height: 300px">
                        <div align="center">
                            <table>
                                <tr>
                                    <td><label for="documento">Documento o Código de Evento:</label></td>
                                    <td><input style="width: 180px;" id="documento" name="documento" type="text" maxlength="25" required/></td>
                                    <td><p><a class="botones" id="buscarParticipante" onclick="return buscarParticipante();" >Buscar</a></p></td>
                                </tr>
                            </table>
                        </div>
                        <div align="center" id="data">
                            
                        </div>
                        <div id="carg"></div>
                    </div>
                    <!--listado de fin-->
                </div>

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
