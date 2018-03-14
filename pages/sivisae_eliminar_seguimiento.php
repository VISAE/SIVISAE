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
                            title: '¡Atención!',
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
                        url: 'src/eliminarSeguimientosCB.php',
                        data: dataString,
                        beforeSend: function () {
                        },
                        success: function (data) {
                            $('#list_grilla').html(data);
                            $('#tb_grilla').show();

                            $("#list_grilla").on("click", ".pagination a", function (e) {
                                e.preventDefault();
                                var page = $(this).attr("data-page"); //get page number from link
                                $("#list_grilla").load("src/eliminarSeguimientoCB.php", {
                                    "page": page

                                },
                                function () { //get content from PHP page

                                });
                            });
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
                        url: 'src/confirmaEliminarSeguimientoCB.php',
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
                    <!--opciones inicio-->



                    <!--opciones fin-->


                    <!--listado de inicio-->

                    <div align="center" style="background-color: #004669">
                        <h2 id='p_fieldset_autenticacion_2'>
                            SOLICIITUDES PARA LA ELIMINACIÓN DE SEGUIMIENTOS
                        </h2>
                    </div>

                    <div align="center" id="list_grilla"></div>

                    <!--listado de fin-->


                    <!--eliminar inicio-->
                    <div id="popup_eliminar">
                        <span class="button_cerrar b-close"></span>
                        <div align="center" style="background-color: #004669" >
                            <h2 id='p_fieldset_autenticacion_2'>
                                Eliminar Seguimiento
                            </h2>
                        </div>
                        <div  class="art-postcontent">
                            <div align="center">
                                <form id="form_eliminar" name="form_eliminar" method="post" onsubmit="return submitFormEliminar()" action="src/confirmaEliminarSeguimientoCB.php">
                                    <div style="background-color: #E8E8E8">
                                        <table style="width: 400px">
                                            <tr>
                                                <td><label for="advertencia_e">¿Realmente desea eliminar este seguimiento?. Recuerde que para activar nuevamente este seguimiento necesitaría la aprobación del administrador del sistema.</label></td>                                           
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
            include "../template/sivisae_footer_home.php";
            ?>
        </body>
        <?php
    }
}
$consulta->destruir();
?>
