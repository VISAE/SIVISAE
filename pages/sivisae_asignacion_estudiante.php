<?php
session_start();
include_once '../config/sivisae_class.php';
$consulta = new sivisae_consultas();

$periodos = $consulta->periodos();
$zonas = $consulta->zonas("T");
$escuelas = $consulta->escuelas();
$permisos_filtro = $consulta->filtro_variables($_SESSION['modulo'], $_SESSION['perfilid']);
    while ($row = mysql_fetch_array($permisos_filtro)) {
        $filtro_escuelas = $row[0];
        $filtro_zonas = $row[1];
    }
$cead = $consulta->ceadSegunZona("T", $filtro_zonas, $_SESSION["sede"]);
$programa = $consulta->programaSegunEscuela("T", $filtro_escuelas, $_SESSION["programa_usuario"]);
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
            
            include "../template/sivisae_link_home.php";
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
            <script src="js/iCheck/icheck.js"></script>
            <script src="js/introLoader/jquery.introLoader.js" type="text/javascript" language="javascript"></script>
            <script src="js/introLoader/spin.min.js" type="text/javascript" language="javascript"></script>
            <script src="js/Chosen1.4/chosen.jquery.js" type="text/javascript" language="javascript"></script>
            <script src="js/Chosen1.4/chosen.jquery.min.js" type="text/javascript" language="javascript"></script>
            <script src="js/flipclock/compiled/flipclock.min.js" type="text/javascript" language="javascript"></script>
            <script type="text/javascript" src="js/qtip/jquery.qtip.js"></script>
            <style type="text/css" class="init">

            </style>

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
                    $(".chosen-select-deselect").chosen({allow_single_deselect: true});
                    $(".chosen-select").chosen({no_results_text: "Uups, No se encontraron registros!"});
                    $("#filtro_tb").hide();
                    $('.lbl').hide();
                    $('.btn').hide();
                    $('#zona, #escuela').chosen().change(function () {
                        filtros($(this).prop("id").toLowerCase());
                    });
                });

                (function ($) {
                    $(function () {
                        // Asignar
                        $('#btn_asignar1, #btn_asignar2').bind('click', function (e) {
                            if ($('#selec').val() !== '') {
                                e.preventDefault();
                                $("#result").empty();
                                $('#popup_asignar').bPopup();
                                $('#auditor_sel').chosen();
                            } else {
                                swal({
                                    title: '¡Debe seleccionar al menos un estudiante!',
                                    text: '',
                                    type: 'error',
                                    confirmButtonColor: '#004669',
                                    confirmButtonText: 'Aceptar'
                                });
                                return false;
                            }
                        });

                        $('#btn_cargar').bind('click', function (e) {
                            e.preventDefault();

                            $('#popup_cargar').bPopup();
                            $("#archivo").replaceWith($("#archivo").val('').clone(true));
                            $('#lee').empty();
                        });
                    });
                })(jQuery);


                function filtros(cual) {
                    var dataString = {
                        "select": cual,
                        "valores": $("#" + cual).val()
                    };
                    $.ajax({
                        type: 'POST',
                        url: 'src/filtrosCB.php',
                        data: dataString,
                        success: function (data) {
                            if (data !== null) {
                                if (cual === 'zona') {
                                    $("#cead").hide();
                                    $(".f").html(data);
                                    $(".chosen-select").chosen();
                                }
                                if (cual === 'escuela') {
                                    $("#programa").hide();
                                    $('.e').html(data);
                                    $(".chosen-select").chosen();
                                }
                            } else {
                                $("#cead").show();
                                $("#programa").show();
                            }
                        }

                    });
                }

                function listaEstudiantes() {
                    if ($('#periodo').val() != '') {
                        startLoad();
                        var form = document.asignacion_estudiantes;
                        var dataString = $(form).serialize();
                        $.ajax({
                            type: 'POST',
                            url: 'src/control_estudiantes.php',
                            data: dataString,
                            beforeSend: function () {
                                startLoad();
                                $('.lbl').show();
                            },
                            success: function (data) {
                                stopLoad();
                                $('#list_estudiantes').html(data);
                                $("#filtro_tb").show();
                                $(".aud").hide();
                                $('#tb_estudiantes').show();
                                $('.btn').show();

                                var aud = $("#auditor_sel").val();
                                var reg = $("#registros").val();
                                var search = $("#buscar").val();
                                var periodo = $("#periodo").val();
                                var zona = $("#zona").val();
                                var cead = $("#cead").val();
                                var escuela = $("#escuela").val();
                                var programa = $("#programa").val();

                                $("#list_estudiantes").on("click", ".pagination a", function (e) {
                                    e.preventDefault();
                                    startLoad();
                                    var page = $(this).attr("data-page"); //get page number from link
                                    var est = $("#selec").val();
                                    $("#list_estudiantes").load("src/control_estudiantes.php", {
                                        "page": page,
                                        "auditor_sel": aud,
                                        "registros": reg,
                                        "buscar": search,
                                        "periodo": periodo,
                                        "zona": zona,
                                        "cead": cead,
                                        "escuela": escuela,
                                        "programa": programa,
                                        "selec_est": est.split(",")
                                    },
                                    function () { //get content from PHP page 
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
                function nobackbutton() {
                    window.location.hash = "no-back-button";
                    window.location.hash = "Again-No-back-button" //chrome
                    window.onhashchange = function () {
                        window.location.hash = "no-back-button";
                    }
                }

                function agregar(data) {
                    var caja = $('#selec').val();
                    var tx;
                    if (caja.length >= 1) {
                        tx = caja + "," + data;
                    } else {
                        tx = data;
                    }
                    var arr = tx.split(",");
                    $('#selec').val(tx);
                    $('.lbl').text("Cantidad de estudiantes seleccionados: " + arr.length);
                }

                function quitar(data) {
                    var caja = $('#selec').val();
                    $('#selec').val("");
                    var arr = caja.split(",");
                    var es = $.inArray(data, arr);
                    if (es >= 0) {
                        arr.splice(es, 1);
                    }
                    $('#selec').val(arr.join(","));
                    $('.lab').text("Cantidad de estudiantes seleccionados: " + arr.length);
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

                function asignar() {

                    var auditor = $('#auditor_sel').val();
                    var est_selec = $('#selec').val();
                    var periodo = $("#periodo").val();
                    var parametros = {
                        "auditor": auditor,
                        "estudiantes": est_selec,
                        "periodo": periodo
                    };
                    $.ajax({
                        data: parametros,
                        url: 'src/asignar.php',
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
                            $("#result").show();
                            $("#result").html(response);
                            setTimeout("CerrarPopup(1)", 3000);
                            listaEstudiantes();
                            $(".lbl").text("");
                            $('#auditor_sel').chosen('destroy');
                            $('#auditor_sel').prop('selectedIndex', 0);
                            $('#selec').val("");

                        }
                    });
                    return false;
                }

                function cargarAsignar() {
                    var data = new FormData();
                    data.append('archivo', $('#archivo')[0].files[0]);
                    //hacemos la petición ajax  
                    $.ajax({
                        url: 'src/leer_archivo_cargue.php',
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
                            $("#lee").html(response);
                            //                            setTimeout("CerrarPopup(2)", 3000);
                            if ($('#periodo').val() !== '') {
                                listaEstudiantes();
                            }
                            $('.cerrar').show();
                            $(".lbl").text("");
                            $('#selec').val("");
                        }
                    });
                    return false;
                }

                function ShowHide(div) {
                    $('#' + div).animate({'height': 'toggle'}, {duration: 1000});
                    var ver = $('#btn-' + div).text();
                    if (ver === 'Ver detalles') {
                        $('#btn-' + div).html('Ocultar detalles');
                    }
                    if (ver === 'Ocultar detalles') {
                        $('#btn-' + div).html('Ver detalles');
                    }
                    return false;
                }
            </script>

        </head>

        <body onload="nobackbutton();">
            <!--Encabezado - Inicio-->
            <?php include "../template/sivisae_head_home.php"; ?>
            <!--Encabezado - Fin-->
            <!--Menu - Inicio-->
            <?php include "sivisae_menu.php"; ?>
            <!--Menu - Fin-->
            <main>
                <!--aqui contenido incio-->
                <div >

                    <!--Barra de estado inicio-->
                    <?php
                    include "sivisae_barra_estado.php";
                    // echo "Tu dirección IP es: {$_SERVER['REMOTE_ADDR']}";
                    ?>
                    <!--Barra de estado fin-->
                    <div align="center" style="background-color: #004669" >
                        <h2 id='p_fieldset_autenticacion_2'>
                            Asignación de estudiantes para Auditoria
                        </h2>
                    </div>
                    <div class="art-postcontent">
                        <div align="center">
                            <form id="asignacion_estudiantes" name="asignacion_estudiantes">
                                <br>

                                <div>
                                    <?php
                                    include "sivisae_filtro.php";
                                    // echo "Tu dirección IP es: {$_SERVER['REMOTE_ADDR']}";
                                    ?>
                                    <div align="right">
                                        <button  title="Asignar estudiantes mediante excel" id="btn_cargar" class="boton_ex"></button>    
                                        <button title="Asignar estudiantes" id="btn_asignar1" class="boton_a btn"></button>    
                                        <br>
                                    </div>
                                </div>
                                <br>
                                <div id="dynElement" >
                                </div>
                                <label id="cant_est" class="lbl"></label>
                                <div id="list_estudiantes" >
                                </div>
                                <br><br>
                                <label id="cant_est" class="lbl"></label><div class="btn" align="right" id="btn-asignar2">
                                    <button title="Asignar estudiantes" id="btn_asignar2" class="boton_a">Cargar</button>    
                                    <br>
                                </div>
                                <input type="hidden" id="selec" name="selec" />
                            </form>    

                            <div id="popup_asignar">
                                <span class="button_cerrar b-close"></span>
                                <div align="center" style="background-color: #004669" >
                                    <h2 id='p_fieldset_autenticacion_2'>
                                        Asignar estudiantes
                                    </h2>
                                </div>
                                <div  class="art-postcontent">
                                    <div align="center">
                                        <div style="background-color: #E8E8E8">
                                            <table style="width: 400px">
                                                <tr>
                                                    <td><label for="auditor_sel">Auditor (*):</label></td>
                                                    <td>
                                                        <select data-placeholder="Seleccione un Auditor"  name='auditor_sel' id='auditor_sel' style="width: 200px;" tabindex='2'>";
                                                            <option value=''></option>
                                                            <?php
                                                            $auditor_c = $consulta->auditores();
                                                            while ($row1 = mysql_fetch_array($auditor_c)) {
                                                                $aud_id = $row1[0];
                                                                $aud_nombre = ucwords(strtolower($row1[1]));
                                                                $gen = ucwords(strtolower($row1[2]));
                                                                echo "<option value='$aud_id'>";
                                                                echo $aud_nombre . " - " . $gen;
                                                                echo "</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2">
                                                        <p><input class="submit_fieldset_autenticacion" type="submit" onclick="return asignar()" value="Asignar estudiantes"/></p>
                                                        <div align="center" id="result"></div>
                                                        <div id="carg" align="center"></div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="popup_cargar">
                                <span class="button_cerrar b-close"></span>
                                <div align="center" style="background-color: #004669" >
                                    <h2 id='p_fieldset_autenticacion_2'>
                                        Asignar estudiantes
                                    </h2>
                                </div>
                                <div  class="art-postcontent">

                                    <div align="center">
                                        <div style="background-color: #E8E8E8">
                                        </div>
                                        <form id="cargar_asignacion" name="cargar_asignacion"> 
                                            <table>
                                                <tr>
                                                    <td colspan="2">
                                                        Para realizar cargue mediante archivo plano descargue el modelo <a href="<?php echo RUTA_PPAL . "modelos/modelo_cargue.csv" ?>">Aquí</a> ó si prefiere <br>
                                                        puede crear un archivo csv(separado por comas) con la siguiente estructura:<br><br>
                                                        <b>CEDULA ESTUDIANTE, CEDULA AUDITOR, CODIGO PERIODO</b><br>
                                                    </td>
                                                </tr>
                                                <tr><td>
                                                        <br>
                                                        <label for="archivo">Archivo:</label>
                                                        <input type="file" name="archivo" id="archivo" /></td>
                                                </tr>
                                                <tr><td colspan="2" align="center">
                                                        <br/>
                                                        <p><input class="submit_fieldset_autenticacion" type="submit" onclick="return cargarAsignar()" value="Cargar"/></p></td>
                                                </tr>
                                                <div id="spinner" align="center" style="display:none;">
                                                    <img id="img-spinner" width="50" height="50" src="template/imagenes/generales/loading.gif" alt="Loading"/>
                                                </div>
                                                <tr>
                                                    <td colspan="2">
                                                        <div id="lee">

                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr><td colspan="2" align="center" class="cerrar" style="display: none">
                                                        <br/>
                                                        <p><input class="submit_fieldset_autenticacion" id="cerrar_p_cargar" type="button" onclick="CerrarPopup(2)" value="Cerrar"/></p></td>
                                                </tr>
                                            </table>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--aqui contenido fin-->
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
