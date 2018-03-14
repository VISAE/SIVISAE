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
            <script src="js/sigra/js-eventos.js" type="text/javascript" language="javascript"></script>
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
                    $("#cupos").keydown(function (event) {
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
                #errorContainer, #errorContainer_e {
                    display: none;
                    overflow: auto;
                    background-color: #FFDDDD;
                    border: 1px solid #FF2323;
                    padding-top: 1;
                }

                #errorContainer, #errorContainer_e label {
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
            </style>
        </head>

        <body onload="nobackbutton();
                        listaGrilla();">
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

                    <div align="right">
                        <button title="Crear Evento" id="boton_crear" <?php echo $disabled_copy; ?> class="<?php echo $class_copy; ?>"></button>    
                    </div>

                    <!--opciones fin-->


                    <!--listado de inicio-->

                    <div align="center" style="background-color: #004669">
                        <h2 id='p_fieldset_autenticacion_2'>
                            EVENTOS
                        </h2>
                    </div>
                    <div>
                        <div align="center" id="carg"></div>
                        <div align="center" id="list_grilla"></div>
                    </div>
                    <!--listado de fin-->

                    <!--creacion de inicio-->
                    <div id="popup_crear">
                        <span class="button_cerrar b-close"></span>
                        <div align="center" style="background-color: #004669" >
                            <h2 id='p_fieldset_autenticacion_2'>
                                Crear Evento
                            </h2>
                        </div>
                        <div  class="art-postcontent">
                            
                            <div align="center">
                                <form id="form_crear" name="form_crear" method="post">
                                    <div style="background-color: #E8E8E8">
                                        <table style="width: 600px">
                                            <tr>
                                                <td><label for="nombre">Nombre:</label></td>
                                                <td><input style="width: 180px;" id="nombre" name="nombre" type="text" maxlength="25" required/></td>
                                            </tr>
                                            <tr>
                                                <td><label for="fecha_ini">Fecha Inicio:</label></td>
                                                <td><input class="fecha" style="width: 180px;" id="fecha_ini" name="fecha_ini" type="text" required /></td>
                                            </tr>
                                            <tr>
                                                <td><label for="fecha_fin">Fecha Fin:</label></td>
                                                <td><input class="fecha" style="width: 180px;" id="fecha_fin" name="fecha_fin" type="text" required /></td>
                                            </tr>
                                            <tr>
                                                <td><label for="lugar">Lugar:</label></td>
                                                <td><input style="width: 180px;" id="lugar" name="lugar" type="text" required/></td>
                                            </tr>
                                            <tr>
                                                <td><label for="organizador">Organizador:</label></td>
                                                <td class="td-org">
                                                    <select data-placeholder="Seleccione..." id="organizador" required name="organizador" onchange="c_organizador('');" >
                                                        <option value=""></option>
                                                        <?php $organizadores = $consulta->getOrganizadores(); 
//                                                        while ($linea = mysqli_fetch_array($lineas)) {
                                                        foreach($organizadores as $organizador){
                                                            echo '<option value="'.$organizador[0].'">'.ucwords(preg_replace($sintilde, $tildes, utf8_decode($organizador[1]))).'</option>';
                                                        } ?>
                                                        <option value="o">Otro (Crear)</option>
                                                    </select>
                                                    <div id="otro-organizador"></div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><label for="poblacion">Población Objetivo:</label></td>
                                                <td><select data-placeholder="Seleccione..." id="poblacion" name="poblacion" required>
                                                        <option value=""></option>
                                                        <option value="ESTUDIANTES">Estudiantes</option>
                                                        <option value="FUNCIONARIOS">Funcionarios</option>
                                                        <option value="GRADUADOS">Graduados</option>
                                                    </select>
                                                </td>
                                            </tr>                                               
                                            <tr>
                                                <td><label for="cupos">Cantidad Cupos:</label></td>
                                                <td><input style='width: 180px;' min='1' id='cupos' name='cupos' type='number'  required/>
                                                </td>
                                            </tr>                                               
                                            <tr>
                                                <td><label for="tp_asist">Tipo Asistencia:</label></td>
                                                <td><select data-placeholder="Seleccione..." id="tp_asist" name="tp_asist" required>
                                                        <option value=""></option>
                                                        <option value="VIRTUAL">Virtual</option>
                                                        <option value="PRESENCIAL">Presencial</option>
                                                    </select>
                                                </td>
                                            </tr>                                               
                                            <tr>
                                                <td><label for="banner_eve">URL Banner Evento:</label></td>
                                                <td><input type='file'  multiple='multiple' id='banner_eve' name="banner_eve" required/></td>
                                            </tr>
                                            <tr class="carg_banner_eve" style="display: none">
                                                <td>Archivo cargado:</td>
                                                <td><div id='carg-banner_eve' name='carg-banner_eve'></div></td>
                                            </tr>
                                            <tr>
                                                <td><label for="doc_eve">URL Documento Evento:</label></td>
                                                <td><input type='file'  multiple='multiple' id='doc_eve' name="doc_eve" required/></td>
                                            </tr>
                                            <tr class="carg_doc_eve" style="display: none">
                                                <td>Archivo cargado:</td>
                                                <td><div id='carg-doc_eve' name='carg-doc_eve'></div></td>
                                            </tr>
                                            <tr>
                                                <td><label for="proyecto">Proyecto:</label></td>
                                                <td>
                                                    <select data-placeholder="Seleccione..." id="proyecto" required name="proyecto" onchange="infAd('')">
                                                        <option value=""></option>
                                                        <?php $proyectos = $consulta->traerProyectos('',''); 
                                                        while ($proyecto = mysqli_fetch_array($proyectos)) {
                                                            echo '<option value="'.$proyecto[0].'">'.ucwords(preg_replace($sintilde, $tildes, utf8_decode($proyecto[1]))).'</option>';
                                                        } ?>
                                                        <!--<option value="o">Otro (Crear)</option>-->
                                                    </select>
                                                    <div id="otro-proyecto"></div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" style="height: 80px">
                                                    <p align="center"><input class="botones" id="btnCrear" type="submit" value="Crear"/></p>
                                                    <div align="center" id="frmC" style="display: none;height: 60px;background: #E8E8E8"></div>
                                                    <div align="center" id="result" style="height: 20px">                                                        
                                                    </div>
                                                    <div id="errorContainer" align="center">
                                                        <p>(*) Campos requeridos.</p>
                                                    </div>
<!--                                                    <div id="spinner" align="center" style="display:none;">
                                                        <img id="img-spinner" width="50" height="50" src="template/imagenes/generales/loading.gif" alt="Loading"/>
                                                    </div>-->
                                                </td>
                                            </tr>
                                        </table>
                                    </div>

                                </form>
                                
                            </div>
                        </div>
                    </div>
                    <!--creacion de fin-->

                    <!--edicion de inicio-->
                    <div id="popup_editar">
                        <span class="button_cerrar b-close"></span>
                        <div align="center" style="background-color: #004669" >
                            <h2 id='p_fieldset_autenticacion_2'>
                                Editar Proyecto
                            </h2>
                        </div>
                        <div  class="art-postcontent">
                            <div align="center">
                                <!--<form id="form_editar" name="form_editar" method="post" onsubmit="return submitFormEditar()" action="src/proyectosCB.php">-->
                                <form id="form_editar" name="form_editar" method="post" >
                                    <div style="background-color: #E8E8E8">
                                        <table style="width: 600px">
                                            <tr>
                                                <td><label for="nombre_e">Nombre:</label></td>
                                                <td><input style="width: 180px;" id="nombre_e" disabled="disabled" name="nombre_e" type="text" maxlength="25" required/></td>
                                            </tr>
                                            <tr>
                                                <td><label for="fecha_ini_e">Fecha Inicio:</label></td>
                                                <td><input class="fecha" style="width: 180px;" id="fecha_ini_e" name="fecha_ini_e" type="text" required /></td>
                                            </tr>
                                            <tr>
                                                <td><label for="fecha_fin_e">Fecha Fin:</label></td>
                                                <td><input class="fecha" style="width: 180px;" id="fecha_fin_e" name="fecha_fin_e" type="text" required /></td>
                                            </tr>
                                            <tr>
                                                <td><label for="lugar_e">Lugar:</label></td>
                                                <td><input style="width: 180px;" id="lugar_e" name="lugar_e" type="text" required/></td>
                                            </tr>
                                            <tr>
                                                <td><label for="organizador_e">Organizador:</label></td>
                                                <td class="td-org">
                                                    <select data-placeholder="Seleccione..." id="organizador_e" required name="organizador_e" onchange="c_organizador('_e');" >
                                                        <option value=""></option>
                                                        <?php 
                                                        foreach($organizadores as $organizador){
                                                            echo '<option value="'.$organizador[0].'">'.ucwords(preg_replace($sintilde, $tildes, utf8_decode($organizador[1]))).'</option>';
                                                        } ?>
                                                        <option value="o">Otro (Crear)</option>
                                                    </select>
                                                    <div id="otro-organizador_e"></div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><label for="poblacion_e">Población Objetivo:</label></td>
                                                <td><select data-placeholder="Seleccione..." id="poblacion_e" name="poblacion_e" required>
                                                        <option value=""></option>
                                                        <option value="ESTUDIANTES">Estudiantes</option>
                                                        <option value="FUNCIONARIOS">Funcionarios</option>
                                                        <option value="GRADUADOS">Graduados</option>
                                                    </select>
                                                </td>
                                            </tr>                                               
                                            <tr>
                                                <td><label for="cupos_e">Cantidad Cupos:</label></td>
                                                <td><input style='width: 180px;' min='1' id='cupos_e' name='cupos_e' type='number'  required/>
                                                </td>
                                            </tr>                                               
                                            <tr>
                                                <td><label for="tp_asist_e">Tipo Asistencia:</label></td>
                                                <td><select data-placeholder="Seleccione..." id="tp_asist_e" name="tp_asist_e" required>
                                                        <option value=""></option>
                                                        <option value="VIRTUAL">Virtual</option>
                                                        <option value="PRESENCIAL">Presencial</option>
                                                    </select>
                                                </td>
                                            </tr>                                               
                                            <tr>
                                                <td><label for="banner_eve_e">URL Banner Evento:</label></td>
                                                <td><input type='file'  multiple='multiple' id='banner_eve_e' name="banner_eve_e" /></td>
                                            </tr>
                                            <tr class="carg_banner_eve_e" style="display: none">
                                                <td>Archivo cargado:</td>
                                                <td><div id='carg-banner_eve_e' name='carg-banner_eve_e'></div></td>
                                            </tr>
                                            <tr>
                                                <td><label for="doc_eve_e">URL Documento Evento:</label></td>
                                                <td><input type='file'  multiple='multiple' id='doc_eve_e' name="doc_eve_e" requiered/></td>
                                            </tr>
                                            <tr class="carg_doc_eve_e" style="display: none">
                                                <td>Archivo cargado:</td>
                                                <td><div id='carg-doc_eve_e' name='carg-doc_eve_e'></div></td>
                                            </tr>
                                            <tr>
                                                <td><label for="proyecto_e">Proyecto:</label></td>
                                                <td>
                                                    <select data-placeholder="Seleccione..." id="proyecto_e" required name="proyecto_e" onchange="infAd('_e')">
                                                        <option value=""></option>
                                                        <?php $proyectos = $consulta->traerProyectos('',''); 
                                                        while ($proyecto = mysqli_fetch_array($proyectos)) {
                                                            echo '<option value="'.$proyecto[0].'">'.ucwords(preg_replace($sintilde, $tildes, utf8_decode($proyecto[1]))).'</option>';
                                                        } ?>
                                                        <!--<option value="o">Otro (Crear)</option>-->
                                                    </select>
                                                    <div id="otro-proyecto_e"></div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" style="height: 80px">
                                                    <input name="even_id_e" id="even_id_e" type="hidden" />
                                                    <p><input id="btn_submit_e" name="btn_submit_e" class="submit_fieldset_autenticacion" type="submit" value="Actualizar"/></p>
                                                    <div align="center" id="frmE" style="display: none; height: 60px;background: #E8E8E8"></div>
                                                    <div align="center" id="result_e">                                                        
                                                    </div>
                                                    <div id="errorContainer_e" align="center">
                                                        <p>(*) Campos requeridos.</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </form>                  
                            </div>
                        </div>
                    </div>
                    <!--edicion de fin-->

                    <!--eliminar inicio-->
                    <div id="popup_eliminar">
                        <span class="button_cerrar b-close"></span>
                        <div align="center" style="background-color: #004669" >
                            <h2 id='p_fieldset_autenticacion_2'>
                                Eliminar Proyecto
                            </h2>
                        </div>
                        <div  class="art-postcontent">
                            <div align="center">
                                <form id="form_eliminar" name="form_eliminar" method="post" onsubmit="return submitFormEliminar()" action="src/proyectosCB.php">
                                    <div style="background-color: #E8E8E8">
                                        <table style="width: 400px">
                                            <tr>
                                                <td><label for=""><p align="justify">¿Realmente desea eliminar el Proyecto?.<br/>Recuerde que para activar nuevamente el Proyecto necesitaría la aprobación del administrador del sistema.</p></label></td>                                           
                                            </tr>
                                            <tr>
                                                <td colspan="2" style="height: 80px">
                                                    <input style="width: 180px;" id="proy_id_el" name="proy_id_el" type="hidden" maxlength="30" />
                                                    <input style="width: 180px;" id="proy_id_el_p" name="proy_id_el_p" type="hidden" maxlength="30" />
                                                    <p><input class="submit_fieldset_autenticacion" id="btn_submit_el" type="submit" value="Eliminar"/></p>
                                                    <div align="center" id="frmEl" style="display: none; height: 60px"></div>
                                                    <div align="center" id="result_el"></div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>

                                </form>                  
                            </div>
                        </div>
                    </div>
                    <!--eliminar fin-->
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
