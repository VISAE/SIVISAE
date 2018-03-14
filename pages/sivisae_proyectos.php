<?php
/*
 * 
 *   @author Ing. Andres Mendez
 * 
 */
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
            $respuesta = $consulta->perfiles();
            $sedes = $consulta->traerSedes();
            $sintilde = explode(',', SIN_TILDES);
            $tildes = explode(',', TILDES);
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
            <script src="js/sweetalert2-master/dist/sweetalert2.min.js" type="text/javascript" languaje="javascript"></script>
            <link rel="stylesheet" href="js/sweetalert2-master/dist/sweetalert2.css">
            <script src="js/Chosen1.4/chosen.jquery.js" type="text/javascript" language="javascript"></script>
            <script src="js/Chosen1.4/chosen.jquery.min.js" type="text/javascript" language="javascript"></script>
            <link type="text/css" rel="stylesheet" href="js/introLoader/css/introLoader.min.css" />
            <script src="js/introLoader/jquery.introLoader.js" type="text/javascript" language="javascript"></script>
            <script src="js/introLoader/spin.min.js" type="text/javascript" language="javascript"></script>
            <script src="js/sigra/validaciones.js" type="text/javascript" language="javascript"></script>

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
                (function ($) {
                    $(function () {
                        // Crear
                        $('#boton_crear').bind('click', function (e) {
                            $('#result').html("<label style='color: #EC2121'></label>");
                            e.preventDefault();
                            //                            window.location.href = "pages/sivisae_crear_proyecto.php?op=14";
                            var form = document.form_crear;
                            //                            limpiaForm(form);
                            $('#result').html('');
                            $('#popup_crear').bPopup();
                            $('#btnCrear').show();
                            $('#otro-linea').html('');
                            $('#otro-cobertura').html('');
                            $("#linea, #eje, #cobertura").chosen({no_results_text: "No se encontraron Coincidencias!", width: "200px"});
                        });
                    });

                })(jQuery);

                function activarpopupeditar(id) {
                    //Editar
                    $('#result_e').html("<label style='color: #EC2121'></label>");
                    $('#boton_editar' + id).bind('click', function (e) {
                        e.preventDefault();
                        var form = document.editar_usuario;
                        //limpiaForm(form);
                        $('#popup_editar').bPopup();
                        $('#btn_submit_e').show();
                        $('#otro-linea_e').html('');
                        $('#otro-cobertura_e').html('');
                        $("#linea_e, #cobertura_e, #eje_e").chosen({no_results_text: "No se encontraron Coincidencias!", width: "200px"});
                        cargar_popup_editar(id);
                    });
                }

                function cargar_popup_editar(id) {

                    var str = $('#input_' + id).val();
                    //                    alert(str);
                    var ids = str.split("|");
                    //Se llenan los campos segun el formulario
                    $.ajax({
                        type: 'POST',
                        url: 'src/proyectosCB.php',
                        data: "accion=traer_proy&pr_id=" + id,
                        dataType: "JSON",
                        beforeSend: function () {
                            startLoad("frmE");
                        },
                        success: function (data) {
                            $("#proy_id_e").val(data.id);
                            $("#nombre_e").val(data.nombre);
                            $("#eje_e").val(data.eje).trigger("chosen:updated");
                            $('#linea_e').html('');
                            $('#linea_e').html(data.contenido);
                            $("#linea_e").val(data.linea).trigger("chosen:updated");
                            $("#cobertura_e").val(data.cobertura).trigger("chosen:updated");
                            $("#presupuesto_e").val(data.pres);
                            c_cobertura("_e", data.cober);
                            $('#result_e').html('');
                            //                            $('.chzn').val(data.cober.split("|")).trigger("chosen:updated");
                        }
                    });
                }

                function activarpopupeliminar(id, perfil) {
                    //Eliminar
                    $('#boton_eliminar' + id).bind('click', function (e) {
                        e.preventDefault();
                        document.getElementById("proy_id_el").value = id;
                        document.getElementById("proy_id_el_p").value = perfil;
                        $('#result_el').html('');
                        $('#popup_eliminar').bPopup();
                    });
                }
                ///Popup - fin


                ///logica - inicio
                function listaGrilla() {
                    var form = document.form_eventos;
                    var dataString = $(form).serialize();
                    $.ajax({
                        type: 'POST',
                        url: 'src/proyectosCB.php',
                        data: "accion=listado" + dataString,
                        beforeSend: function () {
                            startLoad("carg");
                        },
                        success: function (data) {
                            $('#list_grilla').html(data);
                            stopLoad("carg");
                            $("#list_grilla").on("click", ".pagination a", function (e) {
                                startLoad("carg");
                                e.preventDefault();
                                var page = $(this).attr("data-page"); //get page number from link
                                $("#list_grilla").load("src/proyectosCB.php", {
                                    "accion": "listado",
                                    "page": page
                                },
                                function () { //get content from PHP page
                                    stopLoad("carg");
                                });
                            });
                        }

                    });
                    return false;
                }

                ///Crear
                function submitFormCrear() {
                    $('#btn_submit').attr("disabled", true);
                    var nom = $('#nombre').val();
                    var form = document.form_crear;
                    var dataString = $(form).serialize();
                    var foo = [];
                    if ($('.chzn').length) {
                        $('.chzn :selected').each(function (i, selected) {
                            foo[i] = $(selected).val();
                        });
                    }
                    var alertas = validarProyecto('');
                    if (alertas.length > 0) {
                        var alerta = alertas.join('<br>');
                        $('#result').html("<label style='color: #EC2121'>" + alerta + "</label>");
                        return false;
                    } else {
                        $.ajax({
                            type: 'POST',
                            url: 'src/proyectosCB.php',
                            data: "accion=crear_proy&" + dataString + "&chzn=" + foo.join("|"),
                            dataType: "JSON",
                            beforeSend: function () {
                                startLoad("frmC");
                                $('#btnCrear').hide();
                            },
                            success: function (data) {
                                $('#btn_submit').attr("disabled", false);
                                stopLoad("frmC");
                                //                            alert(data);
                                if (data.cod === "uno") {
                                    $('#result').html("<label style='color: #004669'>Se creo el Proyecto " + nom + " correctamente.</label>");
                                    //Se recarga la grilla
                                    limpiaForm(form);
                                    //                                setTimeout("CerrarPopup(1)", 3000);
                                    setTimeout("CerrarPopup(1)", 3000);
                                } else {
                                    $('#result').html("<label style='color: #EC2121'>El Proyecto " + nom + " ya existe.</label>");
                                    $('#btnCrear').show();
                                }
                            }
                        });
                        return false;
                    }
                }

                ///Editar
                function submitFormEditar() {
                    $('#btn_submit_e').attr("disabled", true);
                    var nom = $('#nombre_e').val();
                    startLoad("frmE");
                    var alertas = validarProyecto('_e');
                    if (alertas.length > 0) {
                        var alerta = alertas.join('<br>');
                        $('#result_e').html("<label style='color: #EC2121'>" + alerta + "</label>");
                        stopLoad("frmE");
                        $('#btn_submit_e').attr("disabled", false);
                        return false;
                    } else {
                        var foo = [];
                        if ($('.chzn').length) {
                            $('.chzn :selected').each(function (i, selected) {
                                foo[i] = $(selected).val();
                            });
                        }
                        var form = document.form_editar;
                        var dataString = $(form).serialize();
                        $.ajax({
                            type: 'POST',
                            url: 'src/proyectosCB.php',
                            data: "accion=update_proy&" + dataString + "&chzn=" + foo.join("|"),
                            beforeSend: function () {
                                $('#btn_submit_e').hide();
                            },
                            success: function (data) {
                                $('#btn_submit_e').attr("disabled", false);
                                stopLoad("frmE");
                                $('#result_e').html(data);
                                //Se recarga la grilla
                                limpiaForm(form);
                                //                            listaGrilla();
                                setTimeout("CerrarPopup(2)", 3000);
                            }
                        });
                    }
                    return false;
                }

                ///Eliminar
                function submitFormEliminar() {
                    $('#btn_submit_el').attr("disabled", true);
                    $('#btn_submit_el').hide();
                    //                    $("#spinner_el").show();
                    var form = document.form_eliminar;
                    var dataString = $(form).serialize();
                    $.ajax({
                        type: 'POST',
                        url: 'src/proyectosCB.php',
                        data: "accion=elim_proy&" + dataString,
                        beforeSend: function () {
                            startLoad("frmEl");
                        },
                        success: function (data) {
                            stopLoad("frmEl");
                            //                            $('#btn_submit_el').attr("disabled", false);
                            $('#result_el').html(data);
                            //Se recarga la grilla

                            setTimeout("CerrarPopup(3)", 3000);
                        }
                    });
                    return false;
                }

                function CerrarPopup(popup) {
                    if (popup == 1)
                    {
                        $('#popup_crear').bPopup().close();
                        listaGrilla();
                    }
                    if (popup == 2)
                    {
                        $('#popup_editar').bPopup().close();
                        listaGrilla();
                    }
                    if (popup == 3)
                    {
                        $('#popup_eliminar').bPopup().close();
                        listaGrilla();
                    }
                }

                function limpiaForm(miForm) {
                    // recorremos todos los campos que tiene el formulario
                    $(':input', miForm).each(function () {
                        var type = this.type;
                        var tag = this.tagName.toLowerCase();
                        //limpiamos los valores de los campos…
                        if (type === 'text' || type === 'password' || tag === 'textarea' || tag === 'number')
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
                            $(this).chosen({no_results_text: "No se encontraron Coincidencias!", width: "200px"});
                            $(this).prop('selectedIndex', 0);
                        }
                    });
                }
                function c_linea(tp) {
                    $('#result').html("<label style='color: #EC2121'></label>");
                    $('#result_e').html("<label style='color: #EC2121'></label>");
                    if ($('#linea' + tp).val() === 'o') {
                        var dataString = {
                            "accion": "linea",
                            "accion2": "campos",
                            "tp": tp
                        };
                        $.ajax({
                            type: 'POST',
                            url: 'src/proyectosCB.php',
                            data: dataString,
                            success: function (data) {
                                $('#otro-linea' + tp).html(data);
                            }
                        });
                    } else {
                        $('#otro-linea' + tp).html("");
                    }
                }

                function c_eje() {
                    var eje = $('#eje').val();
                    var dataString = {
                        "accion": "linea_eje",
                        "accion2": "cargar",
                        "tp": eje
                    };
                    $.ajax({
                        type: 'POST',
                        url: 'src/proyectosCB.php',
                        data: dataString,
                        dataType: "JSON",
                        success: function (data) {
                            if (data != "no-data") {
                                $('#linea').html('');
                                $('#linea').html(data);
                                $('#linea').val(0).trigger("chosen:updated");
                            }
                        }
                    });
                }

                function c_ejee() {
                    var eje = $('#eje_e').val();
                    var dataString = {
                        "accion": "linea_eje",
                        "accion2": "cargar",
                        "tp": eje
                    };
                    $.ajax({
                        type: 'POST',
                        url: 'src/proyectosCB.php',
                        data: dataString,
                        dataType: "JSON",
                        success: function (data) {
                            if (data != "no-data") {
                                $('#linea_e').html('');
                                $('#linea_e').html(data);
                                $('#linea_e').val(0).trigger("chosen:updated");
                                //validar segun el parametro el combo que toca cambiar
                            }
                        }
                    });
                }

                function n_linea(tp) {
                    if ($('#eje' + tp).val() != "")
                    {
                        var dataString = {
                            "accion": "linea",
                            "accion2": "crear",
                            "desc_linea": $('#desc_linea' + tp).val(),
                            "desc_ejec": $('#eje' + tp).val(),
                            "desc_ejee": $('#eje_e' + tp).val(),
                        };
                        $.ajax({
                            type: 'POST',
                            url: 'src/proyectosCB.php',
                            data: dataString,
                            dataType: "JSON",
                            success: function (data) {
                                if (data.id !== '0') {
                                    $(".td-linea").html(data.html1);
                                    $("#linea").chosen({no_results_text: "No se encontraron Coincidencias!", width: "200px"});
                                    $(".td-linea_e").html(data.html2);
                                    $("#linea_e").chosen({no_results_text: "No se encontraron Coincidencias!", width: "200px"});
                                    $('#linea' + tp).val(data.id).trigger("chosen:updated");
                                } else {
                                    $('#otro-linea' + tp).html("<label style='color: #EC2121'>Línea existente, por favor validar.</label>");
                                }
                            }
                        });

                    } else
                    {
                        $('#result').html("<label style='color: #EC2121'>Debe seleccionar primero el eje al cual pertenece la línea.</label>");
                        $('#result_e').html("<label style='color: #EC2121'>Debe seleccionar primero el eje al cual pertenece la línea.</label>");
                    }
                    return false;
                }
                function c_cobertura(tp, valores) {
                    var v = $('#cobertura' + tp + ' option:selected').html();
                    //                        if($("#cobertura"+tp).val()==='o'){
                    var dataString = {
                        "accion": "cobertura",
                        "accion2": "campos",
                        "tp": tp,
                        "campo": v
                    };
                    $.ajax({
                        type: 'POST',
                        url: 'src/proyectosCB.php',
                        data: dataString,
                        success: function (data) {
                            $('#otro-cobertura' + tp).html(data);
                            $('.chzn').chosen({no_results_text: "No se encontraron Coincidencias!", width: "200px"});
                        },
                        complete: function () {
                            if (valores !== '') {
                                $('.chzn').val(valores.split("|")).trigger("chosen:updated");
                            }
                            stopLoad("frmE");
                        }
                    });
                }
                function n_cobertura(tp) {
                    var dataString = {
                        "accion": "cobertura",
                        "accion2": "crear",
                        "desc_cobertura": $('#desc_cobertura' + tp).val()
                    };
                    $.ajax({
                        type: 'POST',
                        url: 'src/proyectosCB.php',
                        data: dataString,
                        dataType: "JSON",
                        success: function (data) {
                            if (data.id !== '0') {
                                $(".td-cobertura").html(data.html1);
                                $("#cobertura").chosen({no_results_text: "No se encontraron Coincidencias!", width: "200px"});
                                $(".td-cobertura_e").html(data.html2);
                                $("#cobertura_e").chosen({no_results_text: "No se encontraron Coincidencias!", width: "200px"});
                                $('#cobertura' + tp).val(data.id).trigger("chosen:updated");
                            } else {
                                $('#otro-cobertura' + tp).html("<label style='color: #EC2121'>Cobertura existente, por favor validar.</label>");
                            }
                        }
                    });
                    return false;
                }

                function startLoad(div) {
                    $('#' + div).show();
                    if (div !== "carg") {
                        $("#" + div).introLoader({
                            animation: {
                                name: 'simpleLoader',
                                options: {
                                    stop: false,
                                    fixed: false,
                                    exitFx: 'fadeOut',
                                    ease: "linear",
                                    style: 'light',
                                    customGifBgColor: '#E8E8E8'
                                }
                            },
                            spinJs: {
                                lines: 13, // The number of lines to draw 
                                length: 10, // The length of each line 
                                width: 5, // The line thickness 
                                radius: 10, // The radius of the inner circle 
                                corners: 1, // Corner roundness (0..1) 
                                color: '#004669', // #rgb or #rrggbb or array of colors 
                            }
                        });
                    } else {
                        $("#" + div).introLoader({
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
                                length: 30, // The length of each line 
                                width: 10, // The line thickness 
                                radius: 30, // The radius of the inner circle 
                                corners: 1, // Corner roundness (0..1) 
                                color: '#004669', // #rgb or #rrggbb or array of colors 
                            }
                        });
                    }
                }
                function stopLoad(div) {
                    //                    $('#list_graduados').show();
                    $('#' + div).hide();
                    var loader = $('#' + div).data('introLoader');
                    loader.stop();
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
            <?php include "../template/sivisae_head_home.php"; ?>
            <!--Encabezado - Fin-->

            <main>

                <div >
                    <!--Menu - Inicio-->
                    <?php include "sivisae_menu.php"; ?>
                    <!--Menu - Fin-->
                    <!--Barra de estado inicio-->
                    <?php include "sivisae_barra_estado.php"; ?>
                    <!--Barra de estado fin-->
                    <!--opciones inicio-->

                    <div align="right">
                        <button title="Crear Proyecto" id="boton_crear" <?php echo $disabled_copy; ?> class="<?php echo $class_copy; ?>"></button>    
                    </div>

                    <!--opciones fin-->


                    <!--listado de inicio-->

                    <div align="center" style="background-color: #004669">
                        <h2 id='p_fieldset_autenticacion_2'>
                            GESTOR DE PROYECTOS
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
                                Crear Proyecto
                            </h2>
                        </div>
                        <div  class="art-postcontent">

                            <div align="center">
                                <form id="form_crear" name="form_crear" method="post" onsubmit="return submitFormCrear()" action="src/proyectosCB.php">
                                    <div style="background-color: #E8E8E8">
                                        <table style="width: 400px">
                                            <tr>
                                                <td><label for="nombre">Nombre (*):</label></td>
                                                <td><input style="width: 180px;" id="nombre" name="nombre" type="text" maxlength="25" /></td>
                                            </tr>
                                            <tr>
                                                <td><label for="eje">Eje (*):</label></td>
                                                <td><select data-placeholder="Seleccione..." id="eje" name="eje" onchange="c_eje();">
                                                        <option value=""></option>
                                                        <option value="CONSEJERIA">Consejería</option>
                                                        <option value="BIENESTAR">Bienestar</option>
                                                        <option value="EGRESADOS">Egresados</option>
                                                        <option value="MONITORES">Monitores</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Línea (*):</td>
                                                <td class="td-linea">
                                                    <select data-placeholder="Seleccione..." id="linea" name="linea" onchange="c_linea('');">
                                                        <option value=""></option>
                                                        <?php
//                                                        $lineas = $consulta->getLinea("");
//                                                        foreach ($lineas as $linea) {
//                                                            echo '<option value="' . $linea[0] . '">' . ucwords(preg_replace($sintilde, $tildes, utf8_decode($linea[1]))) . '</option>';
//                                                        }
                                                        ?>
                                                        <option value="o">Otro (Crear)</option>
                                                    </select>
                                                    <div id="otro-linea"></div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><label for="presupuesto">Presupuesto (*):</label></td>
                                                <td><input style="width: 180px;" id="presupuesto" name="presupuesto" type="text" maxlength="25" required="Por favor ingrese el presupuesto del proyecto."/></td>
                                            </tr>
                                            <tr>
                                                <td>Cobertura (*):</td>
                                                <td class="td-cobertura">
                                                    <select data-placeholder="Seleccione..." id="cobertura" name="cobertura" onchange="c_cobertura('', '');">
                                                        <option value=""></option>
                                                        <?php
                                                        $coberturas = $consulta->getCobertura();
                                                        foreach ($coberturas as $cobertura) {
                                                            echo '<option value="' . $cobertura[0] . '">' . ucwords(preg_replace($sintilde, $tildes, utf8_decode($cobertura[1]))) . '</option>';
                                                        }
                                                        ?>
                                                        <!--                                                        <option value="o">Otro (Crear)</option>-->
                                                    </select>
                                                    <div id="otro-cobertura"></div>
                                                </td>
                                            </tr>   
                                            <tr>
                                                <td colspan="2" style="height: 80px">
                                                    <p><input class="submit_fieldset_autenticacion" id="btnCrear" type="submit" value="Crear"/></p>
                                                    <div align="center" id="frmC" style="display: none;height: 60px;background: #E8E8E8"></div>
                                                    <div align="center" id="result" style="height: 20px">                                                        
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
                                Editar Proyecto
                            </h2>
                        </div>
                        <div  class="art-postcontent">
                            <div align="center">
                                <form id="form_editar" name="form_editar" method="post" onsubmit="return submitFormEditar()" action="src/proyectosCB.php">
                                    <div style="background-color: #E8E8E8">
                                        <table style="width: 400px">
                                            <tr>
                                                <td><label for="nombre_e">Nombre (*):</label></td>
                                                <td><input style="width: 180px;" id="nombre_e" name="nombre_e" disabled="disabled" type="text" maxlength="25" required="Por favor ingrese el Nombre del Proyecto."/></td>
                                            </tr>
                                            <tr>
                                                <td><label for="eje_e">Eje (*):</label></td>
                                                <td><select data-placeholder="Seleccione..." id="eje_e" name="eje_e" onchange="c_ejee();" >
                                                        <option value=""></option>
                                                        <option value="CONSEJERIA">Consejería</option>
                                                        <option value="BIENESTAR">Bienestar</option>
                                                        <option value="EGRESADOS">Egresados</option>
                                                        <option value="MONITORES">Monitores</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Línea (*):</td>
                                                <td class="td-linea_e">
                                                    <select data-placeholder="Seleccione..." id="linea_e" name="linea_e" onchange="c_linea('_e');">
                                                        <option value=""></option>
                                                        <?php
//                                                        foreach ($lineas as $linea) {
//                                                            echo '<option value="' . $linea[0] . '">' . ucwords(preg_replace($sintilde, $tildes, utf8_decode($linea[1]))) . '</option>';
//                                                        }
                                                        ?>
                                                        <option value="o">Otro (Crear)</option>
                                                    </select>
                                                    <div id="otro-linea_e"></div>
                                                </td>
                                            </tr>   
                                            <tr>
                                                <td><label for="presupuesto_e">Presupuesto (*):</label></td>
                                                <td><input style="width: 180px;" id="presupuesto_e" name="presupuesto_e" type="text" maxlength="25" required="Por favor ingrese el presupuesto del proyecto."/></td>
                                            </tr>
                                            <tr>
                                                <td>Cobertura (*):</td>
                                                <td class="td-cobertura_e">
                                                    <select data-placeholder="Seleccione..." id="cobertura_e" name="cobertura_e" onchange="c_cobertura('_e', '');">
                                                        <option value=""></option>
                                                        <?php
                                                        foreach ($coberturas as $cobertura) {
                                                            echo '<option value="' . $cobertura[0] . '">' . ucwords(preg_replace($sintilde, $tildes, utf8_decode($cobertura[1]))) . '</option>';
                                                        }
                                                        ?>
                                                        <!--                                                        <option value="o">Otro (Crear)</option>-->
                                                    </select>
                                                    <div id="otro-cobertura_e"></div>
                                                </td>
                                            </tr>   
                                            <tr>
                                                <td colspan="2" style="height: 80px">
                                                    <input name="proy_id_e" id="proy_id_e" type="hidden" />
                                                    <p><input id="btn_submit_e" name="btn_submit_e" class="submit_fieldset_autenticacion" type="submit" value="Actualizar"/></p>
                                                    <div align="center" id="frmE" style="display: none; height: 60px;background: #E8E8E8"></div>
                                                    <div align="center" id="result_e">                                                        
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
            include "../template/sivisae_footer_home.php";
            ?>
        </body>
        <?php
    }
}
$consulta->destruir();
?>
