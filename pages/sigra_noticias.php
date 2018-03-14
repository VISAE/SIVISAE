<?php
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

                ///Popup - inicio

                function activarpopupcrear() {
                    // Crear
                    $('#boton_crear').bind('click', function (e) {
                        e.preventDefault();
                        var form = document.form_crear;
                        limpiaForm(form);
                        $('#result').html('');
                        $('#popup_crear').bPopup();
                        $("#perfil, #sede").chosen();
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
                        $("#perfil_e, #sede_e").chosen();
                    });
                }

                function cargar_popup_editar(id) {
                    var str = $('#input_' + id).val();
                    var ids = str.split("|");
                    //Se llenan los campos segun el formulario
                    document.getElementById("id_e").value = ids[0];
                    document.getElementById("titulo_e").value = ids[1];
                    document.getElementById("fecha_e").value = ids[2];
                    document.getElementById("descripcion_e").value = ids[3];
                    document.getElementById("link_e").value = ids[4];
                    //Se limpia estado
                    $('#result_e').html('');
                }

                function activarpopupeliminar(id) {
                    //Eliminar
                    $('#boton_eliminar' + id).bind('click', function (e) {
                        e.preventDefault();
                        document.getElementById("id_el").value = id;
                        $('#result_el').html('');
                        $('#popup_eliminar').bPopup();
                    });
                }
                ///Popup - fin


                ///logica - inicio
                function listaGrilla() {
                    var form = document.form_noticias;
                    var dataString = $(form).serialize();
                    $.ajax({
                        type: 'POST',
                        url: 'src/noticiasCB.php',
                        data: dataString,
                        beforeSend: function () {
                        },
                        success: function (data) {
                            $('#list_grilla').html(data);
                            $('#tb_grilla').show();

                            $("#list_grilla").on("click", ".pagination a", function (e) {
                                e.preventDefault();
                                var page = $(this).attr("data-page"); //get page number from link
                                $("#list_grilla").load("src/eventosCB.php", {
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
                    $("#spinner").show();
                    var form = document.form_crear;
                    var dataString = $(form).serialize();
                    $.ajax({
                        type: 'POST',
                        url: 'src/creacion_noticiaCB.php',
                        data: dataString,
                        success: function (data) {
                            limpiaForm(form);
                            $('#btn_submit').attr("disabled", false);
                            $('#result').html(data);
                            $("#spinner").hide();
                            //Se recarga la grilla
                            listaGrilla();
                            setTimeout("CerrarPopup(1)", 1000);
                        }
                    });
                    return false;
                }

                ///Editar
                function submitFormEditar() {
                    $('#btn_submit_e').attr("disabled", true);
                    $("#spinner_e").show();
                    var form = document.form_editar;
                    var dataString = $(form).serialize();
                    $.ajax({
                        type: 'POST',
                        url: 'src/actualiza_noticiaCB.php',
                        data: dataString,
                        success: function (data) {
                            limpiaForm(form);
                            $('#btn_submit_e').attr("disabled", false);
                            $('#result_e').html(data);
                            $("#spinner_e").hide();
                            //Se recarga la grilla
                            listaGrilla();
                            setTimeout("CerrarPopup(2)", 1000);
                        }
                    });
                    return false;
                }

                ///Eliminar
                function submitFormEliminar() {
                    $('#btn_submit_el').attr("disabled", true);
                    $("#spinner_el").show();
                    var form = document.form_eliminar;
                    var dataString = $(form).serialize();
                    $.ajax({
                        type: 'POST',
                        url: 'src/eliminar_noticiaCB.php',
                        data: dataString,
                        success: function (data) {
                            limpiaForm(form);
                            $('#btn_submit_el').attr("disabled", false);
                            $('#result_el').html(data);
                            $("#spinner_el").hide();
                            //Se recarga la grilla
                            listaGrilla();
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
                        if (type === 'text' || type === 'password' || tag === 'textarea' || type === 'number')
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
                ///logica - fin

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

                        $("#fecha_e").datepicker({
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
                        <button title="Crear Noticia" id="boton_crear"  onclick="activarpopupcrear()" <?php echo $disabled_copy; ?> class="<?php echo $class_copy; ?>"></button>    
                    </div>

                    <!--opciones fin-->


                    <!--listado de inicio-->

                    <div align="center" style="background-color: #004669">
                        <h2 id='p_fieldset_autenticacion_2'>
                            NOTICIAS
                        </h2>
                    </div>

                    <div align="center" id="list_grilla"></div>

                    <!--listado de fin-->

                    <!--creacion de inicio-->
                    <div id="popup_crear">
                        <span class="button_cerrar b-close"></span>
                        <div align="center" style="background-color: #004669" >
                            <h2 id='p_fieldset_autenticacion_2'>
                                Crear Noticia
                            </h2>
                        </div>
                        <div  class="art-postcontent">
                            <div align="center">
                                <form id="form_crear" name="form_crear" method="post" onsubmit="return submitFormCrear()" action="src/creacion_noticiaCB.php">
                                    <div style="background-color: #E8E8E8">
                                        <table style="width: 400px">
                                            <tr>
                                                <td><label for="titulo">Título (*):</label></td>
                                                <td><input style="width: 180px;" id="titulo" name="titulo" type="text" maxlength="100" required="Por favor ingrese el título del evento."/></td>
                                            </tr>
                                            <tr>
                                                <td><label for="descripcion">Descripción (*):</label></td>
                                                <td><input style="width: 180px;" id="descripcion" name="descripcion" type="text" maxlength="500" required="por favor ingrese la descripción del evento."/></td>
                                            </tr>
                                            <tr>
                                                <td>Fecha (*):</td>
                                                <td><input style="width: 180px;" id="fecha" name="fecha"  type="date" required="Por favor ingrese la fecha del evento."/></td>
                                            </tr>
                                            <tr>
                                                <td><label for="link">Link:</label></td>
                                                <td><input style="width: 180px;" id="link" name="link" type="text" maxlength="250" /></td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <p><input class="submit_fieldset_autenticacion" type="submit" value="Crear"/></p>
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
                    </div>
                    <!--creacion de fin-->

                    <!--edicion de inicio-->
                    <div id="popup_editar">
                        <span class="button_cerrar b-close"></span>
                        <div align="center" style="background-color: #004669" >
                            <h2 id='p_fieldset_autenticacion_2'>
                                Editar Noticia
                            </h2>
                        </div>
                        <div  class="art-postcontent">
                            <div align="center">
                                <form id="form_editar" name="form_editar" method="post" onsubmit="return submitFormEditar()" action="src/actualiza_noticiaCB.php">
                                    <div style="background-color: #E8E8E8">
                                        <table style="width: 400px">
                                            <tr>
                                                <td><label for="titulo_e">Título (*):</label></td>
                                                <td><input style="width: 180px;" id="titulo_e" name="titulo_e" type="text" maxlength="100" required="Por favor ingrese el título del evento."/></td>
                                            </tr>
                                            <tr>
                                                <td><label for="descripcion_e">Descripción (*):</label></td>
                                                <td><input style="width: 180px;" id="descripcion_e" name="descripcion_e" type="text" maxlength="500" required="por favor ingrese la descripción del evento."/></td>
                                            </tr>
                                            <tr>
                                                <td>Fecha (*):</td>
                                                <td><input style="width: 180px;" readonly="true" id="fecha_e" name="fecha_e"  type="date" required="Por favor ingrese la fecha del evento."/></td>
                                            </tr>
                                            <tr>
                                                <td><label for="link_e">Link:</label></td>
                                                <td><input style="width: 180px;" id="link_e" name="link_e" type="text" maxlength="250" /></td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <input style="width: 180px;" id="id_e" name="id_e" type="hidden" maxlength="30" />
                                                    <p><input id="btn_submit_e" name="btn_submit_e" class="submit_fieldset_autenticacion" type="submit" value="Actualizar"/></p>
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
                    <!--edicion de fin-->

                    <!--eliminar inicio-->
                    <div id="popup_eliminar">
                        <span class="button_cerrar b-close"></span>
                        <div align="center" style="background-color: #004669" >
                            <h2 id='p_fieldset_autenticacion_2'>
                                Eliminar Noticia
                            </h2>
                        </div>
                        <div  class="art-postcontent">
                            <div align="center">
                                <form id="form_eliminar" name="form_eliminar" method="post" onsubmit="return submitFormEliminar()" action="src/eliminar_noticiaCB.php">
                                    <div style="background-color: #E8E8E8">
                                        <table style="width: 400px">
                                            <tr>
                                                <td><label for="cedula_e">¿Realmente desea eliminar la noticia?. Recuerde que para activar nuevamente la noticia necesitaría la aprobación del administrador del sistema.</label></td>                                           
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <input style="width: 180px;" id="id_el" name="id_e" type="hidden" maxlength="30" />
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
