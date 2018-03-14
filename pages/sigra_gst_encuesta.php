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
            <script src="js/sigra/js-encuestas.js" type="text/javascript" language="javascript"></script>
            <script src="js/jquery-validation/dist/jquery.validate.js" type="text/javascript" language="javascript"></script>
            <link rel="stylesheet" href="template/css/accordion.css"/>
            <script src="js/css-toggle-switch-source/js/html5shiv.min.js" type="text/javascript" languaje="javascipt"></script>
            <link rel="stylesheet" href="js/css-toggle-switch-source/css/style.css"/>
            
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
                    $(".chosen-select_mod").chosen({no_results_text: "Uups, No se encontraron registros!", width: "60px"});
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
                    $(".nume, .nume1, .nume2, .nume3").keydown(function (event) {
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
                #errorContainer, #errorContainer_mod, #errorContainer_preg, #errorContainer_resp {
                    display: none;
                    overflow: auto;
                    background-color: #FFDDDD;
                    border: 1px solid #FF2323;
                    padding-top: 1;
                }

                #errorContainer, #errorContainer_mod, #errorContainer_preg, #errorContainer_resp label {
                    float: none;
                    width: auto;
                }

                input.error, textArea.error {
                    border: 2px solid #FF2323;
                }
                .error {
                    color: #FF0000;
                    font-size: 14px;
                }
                label.error {
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
                    
                    <!--listado de inicio-->

                    <div align="center" style="background-color: #004669">
                        <h2 id='p_fieldset_autenticacion_2'>
                            ENCUESTAS
                        </h2>
                    </div>
                    <br/>
                    <div class="accordion">
                        <ul>
                            <li>
                                <input type="radio" name="select" class="accordion-select" checked/>
                                <!--<input type="radio" name="select" class="accordion-select" />-->
                                <div class="accordion-title">
                                    <span>Nombre y Descripción</span>
                                </div>
                                <div align="center" class="accordion-content">
                                    <form id="frm_enc" name="frm_enc" method="post">
                                        <table style="width: 600px">
                                            <tr>
                                                <td><label for="nombre">Nombre:</label></td>
                                                <td><input style="width: 180px;" id="nombre" name="nombre" type="text" maxlength="25" required/></td>
                                            </tr>
                                            <tr>
                                                <td><label for="desc_enc">Descripción:</label></td>
                                                <td><input style="width: 180px;" id="desc_enc" name="desc_enc" type="text"  /></td>
                                            </tr>
                                            <tr>
                                                <td align="center" colspan="2" style="height: 70px">
                                                    <a class="botones" id="cancelar" >Cancelar</a>&nbsp;&nbsp;&nbsp;&nbsp;
                                                        <a class="botones" id="guardar" >Guardar</a>
                                                    <div id="errorContainer" align="center">
                                                        <p>(*) Campos requeridos.</p>
                                                    </div>
                                                    <div id="carga" style="display: none;height: 60px;"></div>
                                                    <label class="lbl_nombre" style="color: #004669;font-family: tahoma; font-size: 15px"></label>
                                                </td>
                                            </tr>
                                        </table>
                                    </form>
                                </div>
                                <div class="accordion-separator"></div>
                            </li>
                            <input type="hidden" name="enc_id" id="enc_id" value=""/>
                            <li>
                                <input type="radio" name="select" class="accordion-select" id="act-mod" disabled="disabled"/>
                                <!--<input type="radio" name="select" class="accordion-select" id="act-mod" />-->
                                <div class="accordion-title">
                                    <span>Módulos</span>
                                </div>
                                <div class="accordion-content">
                                    <form id="frm_mod" name="frm_mod" method="post">
                                        <label class="lbl_nombre" style="color: #004669;font-family: tahoma; font-size: 20px; font-weight: bold"></label>
                                        <div align="center">
                                            <table id="modulos" style="width: 600px">
                                                <tr style="background-color: #004669">
                                                    <th  style="color: #FFFFFF">Nombre</th>
                                                    <th  style="color: #FFFFFF">Descripción</th>
                                                    <th  style="color: #FFFFFF">Orden</th>
                                                </tr>
                                                <tr>
                                                    <td style="width: 40%;">
                                                        <input style="width: 100%;" id="nombre_mod1" name="nombre_mod1" type="text" maxlength="25" required/></td>
                                                    <td style="width: 50%;">
                                                        <input style="width: 100%;" id="desc_mod1" name="desc_mod1" type="text" /></td>
                                                <td style="width: 10%;">
                                                    <input style="width: 100%;" class="nume" id="orden_mod1" name="orden_mod1" type="number" required /></td>
                                                </tr>
                                            </table>
                                            <div align="center" colspan="2" style="height: 70px" id="btns">
                                                <a class="botones" id="add_mod" >Añadir</a>&nbsp;&nbsp;&nbsp;&nbsp;
                                                <a class="botones" id="rem_mod" >Borrar</a>&nbsp;&nbsp;&nbsp;&nbsp;
                                                    <a class="botones" id="guardar_mod" >Guardar</a>
                                                <div id="errorContainer_mod" align="center">
                                                    <p>(*) Campos requeridos.</p>
                                                </div>
                                                <div id="carga_mod" style="display: none;height: 60px;"></div>
                                            </div>
                                        </div>
                                    </form>
                                    <br/>
                                    <div align='center'>
                                        <div align='center' id="carg_lst_mods"></div>
                                        <div align='center' id="lst_mods"></div>
                                    </div>
                                </div>
                                <div class="accordion-separator"></div>
                            </li>
                            <li>
                                <input type="radio" name="select" class="accordion-select"  id="act-preg" disabled="disabled"/>
                                <!--<input type="radio" name="select" class="accordion-select"  id="act-preg" />-->
                                <div class="accordion-title">
                                    <span>Preguntas</span>
                                </div>
                                <div class="accordion-content" id="sec_preg">
                                    <form id="frm_preg" name="frm_preg" method="post"> 
                                        <label class="lbl_nombre" id="nom_enc" style="color: #004669;font-family: tahoma; font-size: 20px; font-weight: bold"></label><br/>
                                        <div><label style="color: #004669;font-family: tahoma; font-size: 20px; font-weight: bold">Módulo</label>
                                            <select data-placeholder="Seleccione..." name="modulo" id="modulo" class="chosen-select">
                                                </select>
                                        </div>
                                        <br/>
                                        <div id="f_pregs" style="display: none">
                                            <table id="pregs" style="width: 90%">
                                                <tr>
                                                    <td style="vertical-align: top;width: 30%">
                                                        <label for="enunciado" style="color: #004669;font-family: tahoma; font-size: 15px;">Enunciado</label>
                                                        <input style="width: 100%;" id="enunciado" name="enunciado" type="text" /></td>
                                                    <td style="vertical-align: top;width: 30%">
                                                        <label for="tp_preg" style="color: #004669;font-family: tahoma; font-size: 15px;">Tipo</label>
                                                        <select data-placeholder="Seleccione..." name="tp_preg" id="tp_preg" class="chosen-select">
                                                            <option value=""></option>
                                                            <option value="UNICA">Respuesta Unica</option>
                                                            <option value="MULTIPLE">Respuesta Multiple</option>
                                                            <option value="ABIERTA">Respuesta Abierta</option>
                                                        </select>
                                                    </td>
                                                    <td style="vertical-align: top;width: 30%">
                                                        <label for="desc_preg" style="color: #004669;font-family: tahoma; font-size: 15px;">Descripción</label>
                                                        <textarea style="width: 100%;" id="desc_preg" name="desc_preg" type="text" cols="15" rows="5" ></textarea>
                                                    </td>
                                                    <td style="vertical-align: top;width: 10%">
                                                        <label for="orden_preg" style="color: #004669;font-family: tahoma; font-size: 15px;">Orden</label>
                                                        <input style="width: 100%;" class="nume1" id="orden_preg" name="orden_preg" type="number" required /></td>
                                                </tr>
                                                <tr>
                                                    <td style="vertical-align: top">
                                                        <label for="referencia" style="color: #004669;font-family: tahoma; font-size: 15px;">Referencia</label>
                                                        <input style="width: 100%;" id="referencia" name="referencia" type="text" /></td>
                                                    <td style="vertical-align: top">
                                                        <label for="imagen_preg" style="color: #004669;font-family: tahoma; font-size: 15px;">Imagen</label>
                                                        <input type="file" id="imagen_preg" name="imagen_preg" />
                                                        <!--<input type="hidden" id="url_imagen" name="url_imagen" /><br/>-->
                                                        <div id='carg-img_preg' name='carg-img_preg' style="display: none"></div>
                                                    </td>
                                                    <td style="vertical-align: top">
                                                        <label for="hipervinculo" style="color: #004669;font-family: tahoma; font-size: 15px;">Hipervinculo</label>
                                                        <input style="width: 100%;" id="hipervinculo" name="hipervinculo" type="text" />
                                                    </td>
                                                    <td>
                                                        <div class="switch" id="est_preg" style="width: 100%;display:none">
                                                            <input name="estado_preg" type="checkbox" id="estado_preg" class="cmn-toggle cmn-toggle-yes-no">
                                                            <label for="estado_preg" data-on="Activo" data-off="Inactivo"/>
                                                            <input type="hidden" name="hid_preg_id" id="hid_preg_id" value=""/>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                            <br/>
                                            <div align="center" colspan="2" style="height: 70px" id="btns">
                                                <!--<a class="botones" id="add_mod" >Añadir</a>&nbsp;&nbsp;&nbsp;&nbsp;-->
                                                <a class="botones" id="limpiar" >Limpiar</a>&nbsp;&nbsp;&nbsp;&nbsp;
                                                    <a class="botones" id="guardar_preg" >Guardar</a>
                                                    <a class="botones" id="editar_preg" style="display:none">Guardar</a>
                                                    <div id="errorContainer_preg" align="center" style="width: 80%">
                                                    <p>(*) Campos requeridos.</p>
                                                </div>
                                                <div id="carga_preg" style="display: none;height: 60px;"></div>
                                            </div>
                                        </div>
                                    </form>
                                    <br/>
                                    <div  >
                                        <div align='center' id="carg_lst_pregs" style="display: none;height: 60px;"></div>
                                        <div id="lst_pregs" style="height: 90%; width: 100%"></div>
                                    </div>
                                </div>
                                <div class="accordion-separator"></div>
                            </li>
                            <li>
                                <input type="radio" name="select" class="accordion-select"  id="act-resp"  disabled="disabled"/>
                                <div class="accordion-title">
                                    <span>Respuestas</span>
                                </div>
                                <div class="accordion-content">
                                    <form id="frm_resp" name="frm_resp" method="post">
                                        <!--<div align='center' id="carg_lst_resp" style="position: relative">aaaa</div>-->
					<label class="lbl_nombre" id="nom_resp" style="color: #004669;font-family: tahoma; font-size: 20px; font-weight: bold"></label><br/>
                                        <div><label style="color: #004669;font-family: tahoma; font-size: 20px; font-weight: bold">Pregunta</label>
                                            <select data-placeholder="Seleccione..." name="pregunta" id="pregunta" class="chosen-select">
                                                </select>
                                        </div>
                                        <br/>                                        
                                        <div id="f_resp" style="display:none">
                                            <div id="tb_rtas">                                            
                                                <table id="respuestas" style="width: 95%" class="tg">
                                                    <tr style="background-color: #004669">
                                                        <th  style="color: #FFFFFF">Enunciado</th>
                                                        <th  style="color: #FFFFFF">Descripción</th>
                                                        <th  style="color: #FFFFFF">Valor</th>
                                                        <th  style="color: #FFFFFF">Imágen</th>
                                                        <th  style="color: #FFFFFF">Orden</th>
                                                        <th  style="color: #FFFFFF">Estado</th>
                                                    </tr>
                                                    <tr>
                                                        <td style="width: 30%;">
                                                            <input style="width: 100%;" id="enunc_resp1" name="enunc_resp1" type="text" maxlength="25" required/></td>
                                                        <td style="width: 40%;">
                                                            <textarea style="width: 100%;" id="desc_resp1" name="desc_resp1" type="text" cols="10" rows="2" ></textarea>
                                                        </td>
                                                        <td style="width: 7%;">
                                                            <input style="width: 100%;" class="nume3" id="valor_resp1" name="valor_resp1" type="number"/>
                                                        </td>
                                                        <td style="width: 10%">
                                                            <input type="file" id="imagen_resp1" name="imagen_resp1" onchange="alert(this.value)"/>
                                                            <input type="hidden" id="url_imagen_resp1" name="url_imagen_resp1" /><br/>
                                                            <div id='carg-img_resp1' name='carg-img_resp1' style="display: none"></div>
                                                        </td>
                                                    <td style="width: 8%;">
                                                        <input style="width: 100%;" class="nume2" id="orden_resp1" name="orden_resp1" type="number" required />
                                                    </td>
                                                    <td style="width: 5%;">
                                                        <div class="switch" style="width: 100%;">
                                                            <input name="estado_resp1" type="checkbox" id="estado_resp1" checked="checked" class="cmn-toggle cmn-toggle-yes-no">
                                                            <label for="estado_resp1" id="lbl_est_resp1" data-on="Activo" data-off="Inactivo"/>
                                                        </div>
                                                    </td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <input type="hidden" name="hid_preg_id" id="hid_preg_id" />
                                            <br/>
                                            <div align="center" colspan="2" style="height: 70px" id="btns_resp">
                                                <a class="botones" id="add_resp" >Añadir</a>&nbsp;&nbsp;&nbsp;&nbsp;
                                                <a class="botones" id="rem_resp" >Borrar</a>&nbsp;&nbsp;&nbsp;&nbsp;
                                                    <a class="botones" id="guardar_resp" >Guardar</a>
                                                <div id="errorContainer_resp" align="center">
                                                    <p>(*) Campos requeridos.</p>
                                                </div>
                                                <div id="carga_resp" style="display: none;height: 60px;"></div>
                                            </div>
                                            
                                        </div>
                                    </form>
                                    <br/>
                                    <div>
                                    </div>
                                </div>
                                <div class="accordion-separator"></div>
                            </li>
                        </ul>  
                    </div>
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
