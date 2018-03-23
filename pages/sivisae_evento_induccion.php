<?php
session_start();
include_once '../config/sivisae_class.php';
$consulta = new sivisae_consultas();
$_SESSION["modulo"] = $_GET["sop"];
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
            $modulo = $_GET["sop"];

            if ($modulo != "") {
                $copy = 0;
                $edit = 0;
                $delete = 0;
                $permisos = $consulta->permisos($modulo, $_SESSION["perfilid"]);
                while ($row = mysqli_fetch_array($permisos)) {
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
            // consultar periodos
            $pf = $_SESSION['perfilid'];

            if (isset($pf) && $pf !== '1' && $pf !== '3' && $pf !== '4' && $pf !== '6' && $pf !== '7') {
                $periodos = $consulta->periodos();
            } else {
                $periodos = $consulta->periodos_administrador();
            }
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

        (function ($) {
            $(function () {
                $('#btn_cargar').bind('click', function (e) {
                    e.preventDefault();

                    $('#popup_cargar').bPopup();
                    $("#archivo").replaceWith($("#archivo").val('').clone(true));
                    $('#lee').empty();
                });
            });
        })(jQuery);

        function submitConsultarDocumento() {
            var camposLlenos = {'Periodo':$('#periodo').val(), 'Cédula':$('#cedula').val()};
            if (camposLlenos.Periodo !== '' && camposLlenos.Cédula !== '') {

                var parametros = {
                    "documento": camposLlenos.Cédula,
                    "periodo": camposLlenos.Periodo
                };
                $.ajax({
                    data: parametros,
                    url: 'src/consulta_registroMatriculadosCB.php',
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
                        response = JSON.parse(response);
                        swal({
                            title: response.titleSwal,
                            text: '',
                            type: response.typeSwal,
                            confirmButtonColor: '#004669',
                            confirmButtonText: 'Aceptar'
                        });
                        $("#result").show();
                        $("#result").html(response.response);
                        // $("#cat_atencion, #centro_at, #programa_at").chosen();
                        document.getElementById("cedula").readOnly = true;
                    }
                });
                return false;

            } else {
                var str = '';
                for(key in camposLlenos) {
                    if(camposLlenos[key] == '')
                        str += (str == '')?key:' y '+key;
                }
                swal({
                    title: '¡Debe ingresar '+str+'!',
                    text: '',
                    type: 'error',
                    confirmButtonColor: '#004669',
                    confirmButtonText: 'Aceptar'
                });
                $('#periodo').focus();
                return false;
            }
        }

        function CerrarPopup(popup) {
            if (popup == 1) {
                $('#popup_asignar').bPopup().close();
            }
            if (popup == 2) {
                $('#popup_cargar').bPopup().close();
                $("#archivo").replaceWith($("#archivo").val('').clone(true));
                $('#lee').empty();
                $('.cerrar').hide();
            }
            if (popup == 3) {
                $('#popup_eliminar').bPopup().close();
            }
        }

        function cargarMatriculados() {
            var data = new FormData();
            data.append('archivo', $('#archivo')[0].files[0]);
            //hacemos la petición ajax
            $.ajax({
                url: 'src/leer_archivo_cargue_matriculados.php',
                type: 'POST',
                // Form data
                //datos del formulario
                data: data,
                //necesario para subir archivos via ajax
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $('#spinner').show();
                },
                success: function (response) {
                    $('#spinner').hide();
                    $("#result").html(response);
                    $('.cerrar').show();
                    CerrarPopup(2);
                },
            });
            return false;
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
                            Ingreso a Evento
                        </h2>
                    </div>

                    <div class="art-postcontent">
                        <div align="center">
                            <form id="consultar_documento" name="consultar_documento" method="post" onsubmit="return submitConsultarDocumento()" action="#">
                                <div style="background-color: #ffffff;">
                                    <table style="width: 400px">
                                        <tr>
                                            <td  style="padding: 20px">
                                                <?php
                                                if ($modulo != 12) {
                                                    ?>
                                                    * Periodo:
                                                    <select id="periodo" name="periodo" data-placeholder="Seleccione un periodo" class="chosen-select-deselect" style="width:180px;" tabindex="2">
                                                        <option value=""></option>
                                                        <?php
                                                        while ($row = mysqli_fetch_array($periodos)) {
                                                            echo "<option value='$row[0]'>" .
                                                                utf8_encode(ucwords($row[1])) .
                                                                "</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                <?php } ?>
                                            </td>
                                            <td width="30px"></td>
                                            <td>
                                                <label for="cedula">* Cédula del Matriculado:</label>
                                                <input style="width: 180px;" id="cedula" name="cedula" type="text" maxlength="15" tabindex="1"/></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">
                                                <p><input class="submit_fieldset_autenticacion" type="submit" value="Ingresar"/></p>                                                
                                            </td>
                                        </tr>
                                    </table>
                                    <?php
                                    if ((($copy == $edit) == $delete) == 1) {
                                    ?>
                                    <div align="right">
                                        <button title="Cargar base" id="btn_cargar" class="boton_ex"></button>
                                        <br>
                                    </div>
                                    <?php
                                    }
                                    ?>
                                </div>
                            </form>
                            <div id="popup_cargar">
                                <span class="button_cerrar b-close"></span><br>
                                <div align="center" style="background-color: #004669">
                                    <h2 id='p_fieldset_autenticacion_2'>
                                        Cargar Archivo de Matriculados
                                    </h2>
                                </div>
                                <div class="art-postcontent">

                                    <div align="center">
                                        <div style="background-color: #E8E8E8">
                                        </div>
                                        <form id="cargar_asignacion" name="cargar_asignacion">
                                            <table>
                                                <tr>
                                                    <td colspan="2">
                                                        Para realizar cargue mediante archivo plano descargue el modelo
                                                        <a href="<?php echo RUTA_PPAL . "modelos/modelo_cargue_matriculados.csv" ?>">Aquí</a><br>
                                                        ó seleccione el archivo *.csv, *.xls ó *.xlsx en su
                                                        equipo<br><br>
                                                        <br>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <br>
                                                        <label for="archivo">Archivo:</label>
                                                        <input type="file" name="archivo" id="archivo"/></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" align="center">
                                                        <br/>
                                                        <p><input class="submit_fieldset_autenticacion" type="submit" onclick="return cargarMatriculados()" value="Cargar"/></p>
                                                    </td>
                                                </tr>
                                                <div id="spinner" align="center" style="display:none;">
                                                    <img id="img-spinner" width="50" height="50"
                                                         src="template/imagenes/generales/loading.gif" alt="Loading"/>
                                                </div>
                                                <tr>
                                                    <td colspan="2">
                                                        <div id="lee">

                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" align="center" class="cerrar" style="display: none">
                                                        <br/>
                                                        <p><input class="submit_fieldset_autenticacion"
                                                                  id="cerrar_p_cargar" type="button"
                                                                  onclick="CerrarPopup(2)" value="Cerrar"/></p></td>
                                                </tr>
                                            </table>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="carg" align="center"></div>
                    </div>
                    <div id="dynElement"></div>
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
