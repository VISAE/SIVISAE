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

            //Se configuran los permisos (crear, editar, eliminar)
            $modulo = 1;

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

            $copy = 1;
            $edit = 1;
            $delete = 1;

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

            <link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
            <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
            <script src="http://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>

            <!--contenedor-->
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
            <link type="text/css" rel="stylesheet" href="js/introLoader/css/introLoader.min.css" />
            <script src="js/introLoader/jquery.introLoader.js" type="text/javascript" language="javascript"></script>
            <script src="js/introLoader/spin.min.js" type="text/javascript" language="javascript"></script>
            <!--scripts de funcionalidad - inicio-->

            <script>

        $(function () {
            $("#f_nac").datepicker();
        });
            </script>


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
                    $("#telefono, #celular, #cedula, #telefono_e, #celular_e, #cedula_e").keydown(function (event) {
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

                $(document).ready(function () {
                    $("#correo").change(function () {
                        var str = $("#correo").val();
                        var n = str.indexOf("@");
                        var usr = str.substring(0, n);
                        $("#usuario").val(usr);
                        if (ValidaEmail($("#correo").val()) === false)
                        {
                            swal({
                                title: '¡Por favor!',
                                text: 'Ingrese una dirección de correo electrónico válido',
                                type: 'error',
                                confirmButtonColor: '#004669',
                                confirmButtonText: 'Aceptar'
                            });
                            $("#correo").focus();
                            $("#correo").val("");
                            return false;
                        }
                    });


                    $("#correo_e").change(function () {
                        var str = $("#correo_e").val();
                        var n = str.indexOf("@");
                        var usr = str.substring(0, n);
                        if (ValidaEmail($("#correo_e").val()) === false)
                        {
                            swal({
                                title: '¡Por favor!',
                                text: 'Ingrese una dirección de correo electrónico válido',
                                type: 'error',
                                confirmButtonColor: '#004669',
                                confirmButtonText: 'Aceptar'
                            });
                            $("#correo_e").focus();
                            $("#correo_e").val("");
                            return false;
                        }
                    });
                });

                function ValidaEmail(email) {
                    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
                    return regex.test(email);
                }
        ///validaciones - fin

        ///Popup - inicio

                function activarpopupcrear() {
                    // Crear
                    $('#boton_crear').bind('click', function (e) {
                        e.preventDefault();
                        var form = document.crear_usuario;
                        $('#result').html('');
                        $('#popup_crear').bPopup();
                    });
                }

                function activarpopupeditar(id) {
                    //Editar

                    $('#boton_editar' + id).bind('click', function (e) {
                        e.preventDefault();
                        var form = document.editar_usuario;
                        limpiaForm(form);
                        cargar_popup_editar(id);
                        $('#popup_editar').bPopup();
                        $("#hora_ini, #jornada_hora_ini, #hora_fin, #jornada_hora_fin").chosen();
                    });
                }

                function cargar_popup_editar(id) {
                    var str = $('#input_' + id).val();
                    var ids = str.split("|");
                    //Se llenan los campos segun el formulario
                    document.getElementById("id_e").value = ids[0];
                    document.getElementById("dia_e").value = ids[1];
                    document.getElementById("horario_e").value = ids[2];
                    //Se limpia estado
                    $('#result_e').html('');
                }

                function activarpopupeliminar(id, dia) {
                    //Eliminar
                    $('#boton_eliminar' + id).bind('click', function (e) {
                        e.preventDefault();
                        document.getElementById("id_el").value = id;
                        document.getElementById("id_d").value = dia;
                        $('#result_el').html('');
                        $('#popup_eliminar').bPopup();
                    });
                }
        ///Popup - fin


        ///logica - inicio
                function listaEstudiantes() {
                    var form = document.estudiantes_asignados;
                    var dataString = $(form).serialize();

                    $.ajax({
                        type: 'POST',
                        url: 'src/perfilUsuarioCB.php',
                        data: dataString,
                        beforeSend: function () {
                        },
                        success: function (data) {
                            $('#list_estudiantes').html(data);
                            $('#tb_estudiantes').show();

                            $("#list_estudiantes").on("click", ".pagination a", function (e) {
                                e.preventDefault();
                                var page = $(this).attr("data-page"); //get page number from link
                                $("#list_estudiantes").load("src/perfilUsuarioCB.php", {
                                    "page": page

                                },
                                function () { //get content from PHP page

                                });
                            });
                        }

                    });
                    return false;
                }

        ///Crear
                function submitFormCrear() {
                    $('#btn_submit').attr("disabled", true);
                    startLoad();
                    var form = document.crear_usuario;
                    var dataString = $(form).serialize();
                    $.ajax({
                        type: 'POST',
                        url: 'src/actualiza_perfilUsuarioCB.php',
                        data: dataString,
                        success: function (data) {
                            $('#btn_submit').attr("disabled", false);
                            $('#result').html(data);
                            stopLoad();
                            listaEstudiantes();
                            setTimeout("CerrarPopup(1)", 1000);
                        }
                    });
                    return false;
                }

        ///Editar
                function submitFormEditar() {
                    $('#btn_submit_e').attr("disabled", true);
                    $("#spinner_e").show();
                    var form = document.editar_usuario;
                    var dataString = $(form).serialize();
                    $.ajax({
                        type: 'POST',
                        url: 'src/actualiza_horarioUsuarioCB.php',
                        data: dataString,
                        success: function (data) {
                            limpiaForm(form);
                            $('#btn_submit_e').attr("disabled", false);
                            $('#result_e').html(data);
                            $("#spinner_e").hide();
                            //Se recarga la grilla
                            listaEstudiantes();
                            setTimeout("CerrarPopup(2)", 1000);
                        }
                    });
                    return false;
                }

        ///Eliminar
                function submitFormEliminar() {
                    $('#btn_submit_el').attr("disabled", true);
                    $("#spinner_el").show();
                    var form = document.eliminar_usuario;
                    var dataString = $(form).serialize();
                    $.ajax({
                        type: 'POST',
                        url: 'src/elimina_horarioUsuarioCB.php',
                        data: dataString,
                        success: function (data) {
                            limpiaForm(form);
                            $('#btn_submit_el').attr("disabled", false);
                            $('#result_el').html(data);
                            $("#spinner_el").hide();
                            //Se recarga la grilla
                            listaEstudiantes();
                            setTimeout("CerrarPopup(3)", 1000);
                        }
                    });
                    return false;
                }

                function CerrarPopup(popup) {
                    if (popup == 1)
                    {
                        $('#popup_crear').bPopup().close();
                    }
                    if (popup == 2)
                    {
                        $('#popup_editar').bPopup().close();
                    }
                    if (popup == 3)
                    {
                        $('#popup_eliminar').bPopup().close();
                    }
                }

                function limpiaForm(miForm) {
                    // recorremos todos los campos que tiene el formulario
                    $(':input', miForm).each(function () {
                        var type = this.type;
                        var tag = this.tagName.toLowerCase();
                        //limpiamos los valores de los campos…
                        if (type === 'text' || type === 'password' || tag === 'textarea')
                            this.value = "";
                        // excepto de los checkboxes y radios, le quitamos el checked
                        // pero su valor no debe ser cambiado
                        else if (type === 'checkbox' || type === 'radio')
                            this.checked = false;
                        // los selects le ponesmos el indice a -
                        else if (tag === 'select')
                        {
                            this.selectedIndex = 0;
                            $(this).chosen('destroy');
                            $(this).prop('selectedIndex', 0);
                        }
                    });
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

        ///logica - fin

            </script>
            <!--scripts de funcionalidad - fin-->



        </head>

        <?php
        if ($_SESSION["actualiza_datos"] === "1") {
            echo '<script type="text/javascript" language="javascript">
                    $(document).ready(function () {
                                swal({
                            title: "Actualización de datos",
                            text: "Por favor actualice sus datos de contacto y horarios de atención a estudiantes, aspirantes y graduados.",
                            type: "warning",
                            confirmButtonColor: "#004669",
                            confirmButtonText: "Aceptar"
                        },
                        function () {
                            
                        });
                        });
                    </script>';
        } 
        ?>

        <body onload="nobackbutton();
                listaEstudiantes()">
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

                    <div align="right">
                        <button title="Modificar Datos de  Usuario" id="boton_crear" onclick="activarpopupcrear()" <?php echo $disabled_copy; ?> class="<?php echo $class_copy; ?>"></button>    
                    </div>

                    <!--opciones fin-->


                    <!--listado de usuarios inicio-->

                    <div align="center" style="background-color: #004669">
                        <h2 id='p_fieldset_autenticacion_2'>
                            Perfil de Usuario
                        </h2>
                    </div>

                    <div id="list_estudiantes"></div>

                    <!--listado de usuarios fin-->

                    <!--creacion de usuarios inicio-->
                    <div id="popup_crear">
                        <span class="button_cerrar b-close"></span>
                        <div align="center" style="background-color: #004669" >
                            <h2 id='p_fieldset_autenticacion_2'>
                                Modificar Datos de Usuario
                            </h2>
                        </div>
                        <div  class="art-postcontent">
                            <div align="center">
                                <form id="crear_usuario" name="crear_usuario" method="post" onsubmit="return submitFormCrear()" action="src/actualiza_perfilUsuarioCB.php">
                                    <div style="background-color: #E8E8E8">
                                        <table style="width: 400px">
                                            <tr>
                                                <td >Correo electrónico (*):</td>
                                                <td><input style="width: 180px;" id="correo" value="<?php echo $_SESSION["correo"]; ?>" name="correo"  type="text" maxlength="30" required="Falta el Correo electronico"/></td>
                                            </tr>                            
                                            <tr>
                                                <td >Teléfono fijo:</td>
                                                <td><input style="width: 180px;" id="telefono" value="<?php echo $_SESSION["telefono"]; ?>" name="telefono" type="text" maxlength="10" /></td>
                                            </tr>
                                            <tr>
                                                <td >Celular (*):</td>
                                                <td><input style="width: 180px;" id="celular" value="<?php echo $_SESSION["celular"]; ?>" name="celular" type="text" maxlength="11" required="Falta el Numero celular"/></td>
                                            </tr>
                                            <tr>
                                                <td >Skype:</td>
                                                <td><input style="width: 180px;" id="skype" value="<?php echo $_SESSION["skype"]; ?>" name="skype" type="text" maxlength="30" /></td>
                                            </tr>
                                            <tr>
                                                <td >Fecha de Nacimiento:</td>
                                                <td><input style="width: 180px;" id="f_nac" value="<?php echo $_SESSION["fecha_nac_compl"]; ?>" name="f_nac" type="text" maxlength="30" /></td>
                                            </tr>

                                            <tr>
                                                <td colspan="2">
                                                    <p><input class="submit_fieldset_autenticacion" type="submit" value="Actualizar"/></p>
                                                    <div align="center" id="result"></div>
                                                    <div id="dynElement" >
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>

                                </form>                  
                            </div>
                        </div>
                    </div>
                    <!--creacion de usuarios fin-->

                    <!--edicion de usuarios inicio-->
                    <div id="popup_editar">
                        <span class="button_cerrar b-close"></span>
                        <div align="center" style="background-color: #004669" >
                            <h2 id='p_fieldset_autenticacion_2'>
                                Editar Usuario
                            </h2>
                        </div>
                        <div  class="art-postcontent">
                            <div align="center">
                                <form id="editar_usuario" name="editar_usuario" method="post" onsubmit="return submitFormEditar()" action="src/actualiza_usuarioCB.php">
                                    <div style="background-color: #E8E8E8">
                                        <table style="width: 400px">
                                            <tr>
                                                <td colspan="2"><label for="dia_e">Día:</label></td>
                                                <td colspan="2"><input style="width: 180px;" id="dia_e" name="dia_e" type="text" maxlength="15" readonly="true"/></td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">Horario actual (*):</td>
                                                <td colspan="2"><input style="width: 180px;" id="horario_e" name="horario_e" type="text" maxlength="30" readonly="true" /></td>
                                            </tr>
                                            <tr>
                                                <td><label>Hora Inicio *</label><br/>
                                                    <select style="width: 90px;"  id="hora_ini" name="hora_ini" class="chosen-select" data-placeholder="Buscar...">
                                                        <option value="1:00">1:00</option>
                                                        <option value="2:00">2:00</option>
                                                        <option value="3:00">3:00</option>
                                                        <option value="4:00">4:00</option>
                                                        <option value="5:00">5:00</option>
                                                        <option value="6:00">6:00</option>
                                                        <option value="7:00">7:00</option>
                                                        <option value="8:00">8:00</option>
                                                        <option value="9:00">9:00</option>
                                                        <option value="10:00">10:00</option>
                                                        <option value="11:00">11:00</option>
                                                        <option value="12:00">12:00</option>
                                                    </select>
                                                </td>
                                                <td><label>Jornada *</label><br/><select style="width: 90px;" id="jornada_hora_ini" name="jornada_hora_ini" class="chosen-select" data-placeholder="Buscar...">
                                                        <option value="AM">AM</option>
                                                        <option value="PM">PM</option>
                                                    </select>
                                                </td>
                                                <td><label>Hora Fin *</label><br/>
                                                    <select style="width: 90px;" id="hora_fin" name="hora_fin" class="chosen-select" data-placeholder="Buscar...">
                                                        <option value="1:00">1:00</option>
                                                        <option value="2:00">2:00</option>
                                                        <option value="3:00">3:00</option>
                                                        <option value="4:00">4:00</option>
                                                        <option value="5:00">5:00</option>
                                                        <option value="6:00">6:00</option>
                                                        <option value="7:00">7:00</option>
                                                        <option value="8:00">8:00</option>
                                                        <option value="9:00">9:00</option>
                                                        <option value="10:00">10:00</option>
                                                        <option value="11:00">11:00</option>
                                                        <option value="12:00">12:00</option>
                                                    </select>
                                                </td>
                                                <td><label>Jornada *</label><br/><select style="width: 90px;" id="jornada_hora_fin" name="jornada_hora_fin" class="chosen-select" data-placeholder="Buscar...">
                                                        <option value="AM">AM</option>
                                                        <option value="PM">PM</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="4">
                                                    <input style="width: 180px;" id="id_e" name="id_e" type="hidden" maxlength="30" />
                                                    <p><input id="btn_submit_e" name="btn_submit_e" class="submit_fieldset_autenticacion" type="submit" value="Actualizar Horario"/></p>
                                                    <div align="center" id="result_e"></div>
                                                    <div id="spinner_e" align="center" style="display:none;">
                                                        <img id="img-spinner" width="50" height="50" src="template/imagenes/generales/loading.gif" alt="Loading"/>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>

                                </form>                  
                            </div>
                        </div>
                    </div>
                    <!--edicion de usuarios fin-->

                    <!--eliminar usuarios inicio-->
                    <div id="popup_eliminar">
                        <span class="button_cerrar b-close"></span>
                        <div align="center" style="background-color: #004669" >
                            <h2 id='p_fieldset_autenticacion_2'>
                                Eliminar Usuario
                            </h2>
                        </div>
                        <div  class="art-postcontent">
                            <div align="center">
                                <form id="eliminar_usuario" name="eliminar_usuario" method="post" onsubmit="return submitFormEliminar()" action="src/elimina_horarioUsuarioCB.php">
                                    <div style="background-color: #E8E8E8">
                                        <table style="width: 400px">
                                            <tr>
                                                <td><label for="cedula_e">¿Realmente desea eliminar el horario?. Recuerde que la modificacón del horario se ve reflejada automáticamente en el directorio de consejeria nacional.</label></td>                                           
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <input style="width: 180px;" id="id_el" name="id_el" type="hidden" maxlength="30" />
                                                    <input style="width: 180px;" id="id_d" name="id_d" type="hidden" maxlength="30" />
                                                    <p><input class="submit_fieldset_autenticacion" type="submit" value="Eliminar"/></p>
                                                    <div align="center" id="result_el"></div>
                                                    <div id="spinner_el" align="center" style="display:none;">
                                                        <img id="img-spinner" width="50" height="50" src="template/imagenes/generales/loading.gif" alt="Loading"/>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>

                                </form>                  
                            </div>
                        </div>
                    </div>
                    <!--eliminar usuarios fin-->
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
