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
            <script src="js/sigra/js-inscripcion.js" type="text/javascript" language="javascript"></script>
            <script src="js/jquery-validation/dist/jquery.validate.js" type="text/javascript" language="javascript"></script>

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
                    $("#documento, #tel, #cel").keydown(function (event) {
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
            </script>
            <style>
                #errorContainer {
                    display: none;
                    overflow: auto;
                    background-color: #FFDDDD;
                    border: 1px solid #FF2323;
                    padding-top: 1;
                }

                #errorContainer label {
                    float: none;
                    width: auto;
                }

                input.error {
                    border: 2px solid #FF2323;
                }
                .error {
                    color: #FF0000;
                    font-size: 14px;
                }
                #frm_ins {
                    display: none;
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
                            FORMULARIO DE INSCRIPCION
                        </h2>
                    </div>
                    <div>
                        <form id="form_inscripcion" name="form_inscripcion" method="post">
                            <div align="center">
                                <table>
                                    <tr>
                                        <td><label for="documento">Documento:</label></td>
                                        <td><input style="width: 180px;" id="documento" name="documento" type="text" maxlength="25" required/></td>
                                        <td><p><a class="botones" id="buscarParticipante" onclick="return buscarParticipante();" >Buscar</a></p></td>
                                    </tr>
                                </table>
                            </div>
                            <div align="center" id="frm_ins">
                                <table>                                
                                    <tr class="comp">
                                        <td><label for="nombre">Nombre:</label></td>
                                        <td><input style="width: 180px;" id="nombre" name="nombre" type="text" maxlength="25" required/></td>
                                        <td rowspan="6"><div id="otro-evento" style="display: none;"></div></td>
                                    </tr>
                                    <tr class="comp">
                                        <td><label for="estamento">Estamento:</label></td>
                                        <td><select data-placeholder="Seleccione..." id="estamento" name="estamento" required class="chosen-select">
                                                <option value=""></option>
                                                <option value="ESTUDIANTES">Estudiantes</option>
                                                <option value="FUNCIONARIOS">Funcionarios</option>
                                                <option value="GRADUADOS">Graduados</option>
                                                <option value="DOCENTE">Docente</option>
                                                <option value="PARTICULAR">Particular</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr class="comp">
                                        <td><label for="cel">Celular:</label></td>
                                        <td><input style="width: 180px;" id="cel" name="cel" type="text" maxlength="25" required/></td>
                                    </tr>
                                    <tr class="comp">
                                        <td><label for="tel">Teléfono fijo:</label></td>
                                        <td><input style="width: 180px;" id="tel" name="tel" type="text" maxlength="25" /></td>
                                    </tr>
                                    <tr class="comp">
                                        <td><label for="mail">Correo Electrónico:</label></td>
                                        <td><input style="width: 180px;" id="mail" name="mail" type="text" maxlength="25" required/></td>
                                    </tr>
                                    <tr class="comp">
                                        <td><label for="evento">Evento:</label></td>
                                        <td>
                                            <select data-placeholder="Seleccione..." id="evento" required name="evento" onchange="infAd()" class="chosen-select">
                                                <option value=""></option>
                                                <?php $eventos = $consulta->eventosDisp(); 
                                                while ($evento = mysqli_fetch_array($eventos)) {
                                                    echo '<option value="'.$evento[0].'">'.ucwords(preg_replace($sintilde, $tildes, utf8_decode($evento[1]))).'</option>';
                                                } ?>
                                                <!--<option value="o">Otro (Crear)</option>-->
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="center" colspan="2" style="height: 70px"><p><a class="botones" id="cancelar" >Cancelar</a>&nbsp;&nbsp;&nbsp;&nbsp;
                                        <a class="botones" id="guardar" >Guardar</a></p>
                                        <div id="errorContainer" align="center">
                                            <p>(*) Campos requeridos.</p>
                                        </div>
                                    <div id="carga" style="display: none;height: 60px;"></div>
                                    </td>
                                    </tr>
                                </table>
                                <div id="qr"></div>
                            </div>
                        </form>
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
