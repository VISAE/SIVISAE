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
            <link href="js/iCheck/polaris/polaris.css" rel="stylesheet">
            <link rel="stylesheet" type="text/css" href="template/css/estilo_index.css">
            <script src="js/popup/bpopup.js" type="text/javascript"></script>
            <link rel="stylesheet" type="text/css" href="template/popup/estilo_popup.css">
            <link rel="stylesheet" type="text/css" href="template/grilla/estilo_grilla.css">
            <script src="js/iCheck/icheck.js"></script>
            <script src="js/sweetalert-master/dist/sweetalert-dev.js" type="text/javascript" languaje="javascript"></script>
            <link rel="stylesheet" href="js/sweetalert-master/dist/sweetalert.css">
            <script src="js/introLoader/jquery.introLoader.js" type="text/javascript" language="javascript"></script>
            <script src="js/introLoader/spin.min.js" type="text/javascript" language="javascript"></script>

            <!--scripts de funcionalidad - inicio-->
            <script type="text/javascript">

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
                    $('.icheck_p').on('ifChecked', function (event) {
                        mostrarPermisos($(this).val());
                    })
                            .on('ifUnchecked', function () {
                                mostrarPermisos($(this).val());
                            });

                    $('.icheck_e').on('ifChecked', function (event) {
                        mostrarPermisosE($(this).val());
                    })
                            .on('ifUnchecked', function () {
                                mostrarPermisosE($(this).val());
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
                function mostrarPermisos(opcion) {
                    $('#divperm' + opcion).toggle();
                }

                function mostrarPermisosE(opcion) {
                    $('#divperm_e' + opcion).toggle();
                }
                ///validaciones - fin

                ///Popup - inicio
                function activarpopupcrear() {
                    // Crear
                    $('#boton_crear').bind('click', function (e) {
                        e.preventDefault();
                        var form = document.crear_perfil;
                        limpiaForm(form);
                        $('#result').html('');
                        $('#popup_crear').bPopup();
                        $('.icheck, .icheck_p').iCheck({
                            checkboxClass: 'icheckbox_polaris',
                            radioClass: 'iradio_polaris',
                            increaseArea: '-10%' // optional
                        });
                    });
                }

                function activarpopupeditar(id) {
                    //Editar

                    $('#boton_editar' + id).bind('click', function (e) {
                        e.preventDefault();
                        var form = document.editar_perfil;
                        limpiaForm(form);
                        cargar_popup_editar(id);
                        $('#popup_editar').bPopup();
                        $('.icheck_e, .icheck').iCheck({
                            checkboxClass: 'icheckbox_polaris',
                            radioClass: 'iradio_polaris',
                            increaseArea: '-10%' // optional
                        });
                    });
                }

                function cargar_popup_editar(id) {
                    var str = $('#input_' + id).val();
                    var ids = str.split("|");
                    var data = new FormData();
                    data.append('perfil', id);
                    data.append('accion', "e");
                    data.append('desc_perfil', ids[1]);
                    //Se llenan los campos segun el formulario
                    $.ajax({
                        type: 'POST',
                        url: 'src/perfilesCB.php',
                        data: data,
                        cache: false,
                        contentType: false,
                        processData: false,
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
                        success: function (data) {
                            $('#result_e').hide();
                            $('#editar').html(data);
                            $('.icheck_e, .icheck').iCheck({
                                checkboxClass: 'icheckbox_polaris',
                                radioClass: 'iradio_polaris',
                                increaseArea: '-10%' // optional
                            });
                            var loader = $('#carg').data('introLoader');
                            loader.stop();
                        }

                    });
                    return false;
                    //Se limpia estado
                    //                    $('#result_e').html('');

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
                function listaPerfiles() {
                    var form = document.estudiantes_asignados;
                    var dataString = $(form).serialize();
                    var data = new FormData();
                    // data.append('perfil', id);
                    data.append('accion', "n");
                    $.ajax({
                        type: 'POST',
                        url: 'src/perfilesCB.php',
                        data: data,
                        cache: false,
                        contentType: false,
                        processData: false,
                        beforeSend: function () {
                        },
                        success: function (data) {
                            $('#list_perfiles').html(data);
                            $('#tb_perfiles').show();

                            $("#list_perfiles").on("click", ".pagination a", function (e) {
                                e.preventDefault();
                                var page = $(this).attr("data-page"); //get page number from link
                                $("#list_perfiles").load("src/perfilesCB.php", {
                                    "page": page, "accion": "n"
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
                    $("#spinner").show();
                    var form = document.crear_perfil;
                    var dataString = $(form).serialize();
                    $.ajax({
                        type: 'POST',
                        url: 'src/creacion_perfilCB.php',
                        data: dataString,
                        success: function (data) {
                            limpiaForm(form);
                            $('#result').html(data);
                            $("#spinner").hide();
                            //Se recarga la grilla
                            listaPerfiles();
                            setTimeout("CerrarPopup(1)", 1000);
                        }
                    });
                    return false;
                }

                ///Editar
                function submitFormEditar() {
                    $('#btn_submit_e').attr("disabled", true);
                    var form = document.editar_perfil;
                    var dataString = $(form).serialize();
                    $.ajax({
                        type: 'POST',
                        url: 'src/creacion_perfilCB.php',
                        data: dataString,
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
                        success: function (data) {
                            var loader = $('#carg').data('introLoader');
                            loader.stop();
                            $('#result_e').show();
                            $('#btn_submit_e').attr("disabled", false);
                            $('#result_e').html(data);
                            //Se recarga la grilla
                            listaPerfiles();
                            setTimeout("CerrarPopup(2)", 3000);
                            limpiaForm(form);
                        }
                    });
                    return false;
                }

                ///Eliminar
                function submitFormEliminar() {
                    $('#btn_submit_el').attr("disabled", true);
                    $("#spinner_el").show();
                    var form = document.eliminar_perfil;
                    var dataString = $(form).serialize();
                    $.ajax({
                        type: 'POST',
                        url: 'src/eliminar_perfilCB.php',
                        data: dataString,
                        success: function (data) {
                            limpiaForm(form);
                            $('#btn_submit_el').attr("disabled", false);
                            $('#result_el').html(data);
                            $("#spinner_el").hide();
                            //Se recarga la grilla
                            listaPerfiles();
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
                    //Se ocultan los permisos por opcion segun ids
                    var str = $('#control_ids').val();
                    var ids = str.split(",");
                    for (i = 0; i < ids.length; i++) {
                        document.getElementById("divperm".concat(ids[i])).style.display = "none";
                    }
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
                    // Se setea el conjunto de ids nuevamente
                    var elem = document.getElementById("control_ids");
                    elem.value = str;

                }
                ///logica - fin

                function agregar(data) {
                }
                function quitar(data) {
                }
            </script>
            <!--scripts de funcionalidad - fin-->

        </head>

        <body onload="nobackbutton();
                        listaPerfiles();">
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
                        <button title="Crear Perfil" id="boton_crear"  onclick="activarpopupcrear()" <?php echo $disabled_copy; ?> class="<?php echo $class_copy; ?>"></button>    
                    </div>

                    <!--opciones fin-->


                    <!--listado de usuarios inicio-->

                    <div align="center" style="background-color: #004669">
                        <h2 id='p_fieldset_autenticacion_2'>
                            Perfiles
                        </h2>
                    </div>

                    <div align="center" id="list_perfiles"></div>

                    <!--listado de usuarios fin-->

                    <!--creacion de perfiles inicio-->
                    <div id="popup_crear">
                        <span class="button_cerrar b-close"></span>    
                        <div align="center" style="background-color: #004669" >
                            <h2 id='p_fieldset_autenticacion_2'>
                                Crear Perfil
                            </h2>
                        </div>
                        <div class="art-postcontent">
                            <div align="center">
                                <!--aqui contenido incio-->
                                <form id="crear_perfil" name="crear_perfil" method="post" onsubmit="return submitFormCrear()" action="../src/creacion_perfilCB.php">
                                    <input type="hidden" id="accion" name="accion" value="n"/>
                                    <div style="background-color: #E8E8E8">
                                        <table>
                                            <tr>
                                                <td style="width: 200px"><label for="nombre_perfil">Nombre del Perfil (*):</label></td>
                                                <td><input id="nombre_perfil" name="nombre_perfil" type="text" maxlength="30" required="Falta el nombre"/></td>
                                            </tr>
                                            <tr>                                
                                                <td>
                                                    <h4>Opciones:</h4>
                                                </td>
                                            </tr>
                                            <?php
                                            $result = $consulta->opcionesPerfil();
                                            while ($row = mysql_fetch_array($result)) {
                                                $opid = $row[0];
                                                $desc = $row[1];
                                                $url = $row[2];
                                                echo '<tr><td>
                                    <label><input type="checkbox" class="icheck_p" value="' . $opid . '" name="opcion[]" id="opcion' . $opid . '"> ' . $desc . '</label>
                                </td>
                                <td style="width: 300px">
                                    <div id="divperm' . $opid . '" style="display: none">
                                        <label><input type="checkbox" class="icheck" value="1" name="perm' . $opid . '[]" id="perm' . $opid . '"> Crear</label>
                                        <label><input type="checkbox" class="icheck" value="2" name="perm' . $opid . '[]" id="perm' . $opid . '"> Editar</label>
                                        <label><input type="checkbox" class="icheck" value="3" name="perm' . $opid . '[]" id="perm' . $opid . '"> Eliminar</label>
                                    </div>
                                </td>
                            </tr>';
                                                $arr[] = $opid;
                                            }
                                            ?>
                                            <tr>
                                                <td style="alignment-adjust: central" colspan="2" >
                                                    <p><input id="control_ids" type="hidden" value="<?php echo implode(",", $arr); ?>"/></p>
                                                    <p><input id="crear" class="submit_fieldset_autenticacion" type="submit" value="Crear Perfil"/></p>
                                                    <div align="center" id="result"></div>
                                                    <div id="spinner" align="center" style="display:none;">
                                                        <img id="img-spinner" width="50" height="50" src="template/imagenes/generales/loading.gif" alt="Loading"/>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </form>
                                <!--aqui contenido fin-->
                            </div>
                        </div>
                    </div>
                    <!--creacion de perfiles fin-->

                    <!--edicion de usuarios inicio-->
                    <div id="popup_editar">
                        <span class="button_cerrar b-close"></span>
                        <div align="center" style="background-color: #004669" >
                            <h2 id='p_fieldset_autenticacion_2'>
                                Editar Perfil
                            </h2>
                        </div>
                        <div  class="art-postcontent">
                            <div align="center">
                                <!--aqui contenido incio-->
                                <form id="editar_perfil" name="editar_perfil" method="post" >
                                    <input type="hidden" id="accion_e" name="accion_e" value="e"/>
                                    <div id="carg"></div>
                                    <div style="background-color: #E8E8E8" id="editar">

                                    </div>
                                    <div id="result_e"></div>
                                </form>
                                <!--aqui contenido fin-->
                            </div>
                        </div>
                    </div>
                    <!--edicion de usuarios fin-->


                    <!--eliminar usuarios inicio-->
                    <div id="popup_eliminar">
                        <span class="button_cerrar b-close"></span>
                        <div align="center" style="background-color: #004669" >
                            <h2 id='p_fieldset_autenticacion_2'>
                                Eliminar Perfil
                            </h2>
                        </div>
                        <div  class="art-postcontent">
                            <div align="center">
                                <form id="eliminar_perfil" name="eliminar_perfil" method="post" onsubmit="return submitFormEliminar()" action="src/eliminar_perfilCB.php">
                                    <div style="background-color: #E8E8E8">
                                        <table style="width: 400px">
                                            <tr>
                                                <td><label for="cedula_e">¿Realmente desea eliminar el perfil?. Recuerde que para activar nuevamente el perfil necesitaría la aprobación del administrador del sistema.</label></td>                                           
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <input style="width: 180px;" id="id_el" name="id_el" type="hidden" maxlength="30" />
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
