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

            // opcion directorio
            $class_directory = "boton_d";
            $disabled_directory = "";

            $_SESSION['opc_ed'] = "class='$class_edit' $disabled_edit";
            $_SESSION['opc_el'] = "class='$class_delete' $disabled_delete";


            include "../template/sivisae_link_home.php";
            $respuesta = $consulta->perfiles();
            $sedes = $consulta->traerSedes();
            ?>

            <!--contenedor-->
            <link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
            <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
            <script src="http://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>
            <link rel="stylesheet" type="text/css" href="template/css/estilo_index.css">
            <link rel="stylesheet" type="text/css" href="js/Chosen1.4/chosen.css">
            <link rel="stylesheet" type="text/css" href="js/Chosen1.4/chosen.min.css">
            <link type="text/css" rel="stylesheet" href="js/qtip/jquery.qtip.css" />
            <link type="text/css" rel="stylesheet" href="js/introLoader/css/introLoader.min.css" />
            <link rel="stylesheet" href="template/popup/style.min.css">
            <script src="js/popup/bpopup.js" type="text/javascript"></script>
            <link rel="stylesheet" type="text/css" href="template/popup/estilo_popup.css">
            <link rel="stylesheet" type="text/css" href="template/grilla/estilo_grilla.css">
            <script src="js/Chosen1.4/chosen.jquery.js" type="text/javascript" language="javascript"></script>
            <script src="js/Chosen1.4/chosen.jquery.min.js" type="text/javascript" language="javascript"></script>
            <script src="js/sweetalert-master/dist/sweetalert-dev.js" type="text/javascript" languaje="javascript"></script>
            <link rel="stylesheet" href="js/sweetalert-master/dist/sweetalert.css">
            <script type="text/javascript" src="js/qtip/jquery.qtip.js"></script>
            <script src="js/introLoader/jquery.introLoader.js" type="text/javascript" language="javascript"></script>
            <script src="js/introLoader/spin.min.js" type="text/javascript" language="javascript"></script>
            <script src="js/popup/bpopup.js" type="text/javascript"></script>
            <link rel="stylesheet" type="text/css" href="template/popup/estilo_popup.css">
            <!--contenedor-->


            <script type="text/javascript" language="javascript">
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
                    $('.sel_zona').hide();

                });

                function nobackbutton() {
                    window.location.hash = "no-back-button";
                    window.location.hash = "Again-No-back-button"; //chrome
                    window.onhashchange = function () {
                        window.location.hash = "no-back-button";
                    }
                }

                function listaEstudiantes() {
                    if ($('#periodo').val() !== '') {
                        var form = document.estudiantes_asignados;
                        var dataString = $(form).serialize();
                        $.ajax({
                            type: 'POST',
                            url: 'src/seguimientoCB.php',
                            data: dataString,
                            beforeSend: function () {
                                //                                $("#spinner").show();
                                startLoad();
                            },
                            success: function (data) {
                                $('#list_estudiantes').html(data);
                                //                                $("#spinner").hide();
                                stopLoad();
                                var aud = $("#auditor").val();
                                var reg = $("#registros").val();
                                var search = $("#buscar").val();
                                var periodo = $("#periodo").val();
                                var escuela = $("#escuela").val();
                                var programa = $("#programa").val();

                                $("#list_estudiantes").on("click", ".pagination a", function (e) {
                                    //                                    $("#spinner").show();
                                    startLoad();
                                    e.preventDefault();
                                    var page = $(this).attr("data-page"); //get page number from link
                                    $("#list_estudiantes").load("src/seguimientoCB.php", {
                                        "page": page,
                                        "auditor": aud,
                                        "registros": reg,
                                        "buscar": search,
                                        "periodo": periodo,
                                        "escuela": escuela,
                                        "programa": programa
                                    },
                                    function () { //get content from PHP page 
                                        //                                        $("#spinner").hide();
                                        stopLoad();
                                    });
                                    document.getElementById('p_fieldset_autenticacion_2').scrollIntoView(true);
                                });
                            }
                        });
                        return false;
                    } else {
                        swal({
                            title: 'Seleccione el Periodo primero',
                            text: '',
                            type: 'error',
                            confirmButtonColor: '#004669',
                            confirmButtonText: 'Aceptar'
                        });
                        $('#periodo').focus();
                        return false;
                    }
                }

                function activarpopupcaracterizacion(id) {
                    //Editar

                    $('#boton_caracterizacion' + id).bind('click', function (e) {
                        alert(id);
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

                function crearReporte() {
                    //                    var aud = $("#auditor").val();
                    //                                var search = $("#buscar").val();
                    //                                var periodo = $("#periodo").val();
                    //                                var escuela = $("#escuela").val();
                    //                                var programa = $("#programa").val();
                    //                    var data = new FormData();
                    //    data.append('auditor', aud);
                    //    data.append('buscar', search);
                    //    data.append('periodo', periodo);
                    //    data.append('escuela', escuela);
                    //    data.append('programa', programa);
                    var form = document.estudiantes_asignados;
                    var dataS = $(form).serialize();
                    $.ajax({
                        type: 'POST',
                        url: 'src/reporte_asignados.php',
                        data: dataS,
                        beforeSend: function () {
                            $('#spinner').show();
                        },
                        success: function (data) {
                            $('#spinner').hide();
                            swal({
                                title: '¡Descargue su documento!',
                                text: "<a href='" + data + "' id='pdf' class='botones'>AQUI</a>",
                                type: 'success',
                                html: true,
                                confirmButtonColor: '#004669',
                                confirmButtonText: 'Aceptar'
                            });

                        }
                    });
                    return false;
                }

                // evento para el directorio
                function activarpopupdirectorio() {
                    // Crear
                    $('#boton_directorio').bind('click', function (e) {
                        e.preventDefault();
                        var form = document.directorio;
                        limpiaForm(form);
                        $('#tipo_per').chosen();
                        $('#result_el').html('');
                        $('#list_grilla_busqueda').html('');
                        $('#popup_directorio').bPopup();
                    });
                    return false;
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

                ///Crear
                function submitFormDirectorioBuscar() {
                    $('#btn_submit').attr("disabled", true);
                    $("#spinner").show();
                    var form = document.directorio;
                    var dataString = $(form).serialize();
                    $.ajax({
                        type: 'POST',
                        url: 'src/directorio_CB.php',
                        data: dataString,
                        success: function (data) {
                            limpiaForm(form);
                            $('#tipo_per').chosen();
                            $('#btn_submit').attr("disabled", false);
                            $('#list_grilla_busqueda').html(data);
                            $("#spinner").hide();
                        }
                    });
                    return false;
                }

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
                        $("#fecha_inicio").datepicker({
                            firstDay: 1,
                            changeYear: true,
                            changeMonth: true,
                            dateFormat: 'yy/mm/dd',
                            yearRange: '-0:+0'
                        });

                        $("#fecha_fin").datepicker({
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
              ">
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
                        <button title="Directorio Unadista (Doble Click)" id="boton_directorio" onclick="activarpopupdirectorio()" <?php echo $disabled_directory; ?> class="<?php echo $class_directory; ?>"></button>    
                    </div>
                    <!--opciones fin-->


                    <!--listado de usuarios inicio-->

                    <div align="center" style="background-color: #004669">
                        <h2 id='p_fieldset_autenticacion_2'>
                            TABLERO RIESGOS DE GESTIÓN 
                        </h2>
                    </div>
                    <div class="art-postcontent">
                        <div align="center">
                            <form id="estudiantes_asignados" name="estudiantes_asignados">
                                <br>
                                <?php
                                include "sivisae_filtro.php";
                                ?>
                                <br>
                                <div id="dynElement" >
                                </div>
                                <div id="list_estudiantes">
                                    <div id="spinner" align="center" style="display:none;">
                                        <img id="img-spinner" width="50" height="50" src="template/imagenes/generales/loading.gif" alt="Loading"/>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!--eliminar usuarios inicio-->
                    <div id="popup_directorio">
                        <span class="button_cerrar b-close"></span>
                        <div align="center" style="background-color: #004669" >
                            <h2 id='p_fieldset_autenticacion_2'>
                                Directorio Unadista
                            </h2>
                        </div>
                        <div  class="art-postcontent">
                            <div align="center">
                                <form id="directorio" name="directorio" method="post" onsubmit="return submitFormDirectorioBuscar()" action="src/eliminar_usuarioCB.php">
                                    <div style="background-color: #E8E8E8" >
                                        <table style="width: 400px">
                                            <tr>
                                                <td><label for="cedula_e">Buscar (*)</label></td>
                                                <td><input style="width: 180px;" id="documento_buscar" name="documento_buscar" type="text" required="Falta el número de cédula" maxlength="30" />    </td>  
                                            </tr>
                                            <tr>
                                                <td><label for="tipo_per">Tipo (*)</label></td>
                                                <td>
                                                    <select data-placeholder="Seleccione"  name='tipo_per' id='tipo_per' required="Seleccione el tipo" style="width: 180px;">";
                                                        <option value=''></option>
                                                        <option value='e'>Estudiante</option>
                                                        <option value='t'>E-Mediador</option>
                                                        <option value='a'>Auditor</option>
                                                        <option value='c'>Consejero</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <p><input class="submit_fieldset_autenticacion" type="submit" value="Buscar"/></p>
                                                    <div align="center" id="result_el"></div>
                                                    <div id="spinner_el" align="center" style="display:none;">
                                                        <img id="img-spinner" width="50" height="50" src="template/imagenes/generales/loading.gif" alt="Loading"/>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                        <div align="center" id="list_grilla_busqueda"></div>
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
