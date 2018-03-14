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

                function listaGraduados() {
//                    var form = document.form_eventos;
//                    var dataString = $('#filtros').find("select, input:hidden").serialize();
                    var dataString = {
                                "accion": "listado",
                                "buscar": $("#buscar").val(),
                                "cead": $("#cead").val(),
                                "zona": $("#zona").val(),
                                "programa": $("#programa").val(),
                                "escuela": $("#escuela").val(),
                                "registros": $("#registros").val()
                            };
                    $.ajax({
                        type: 'POST',
                        url: 'src/actualizacion_datosCB.php',
//                        data: "accion=listado",
                        data: dataString,
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
//                            alert(data);
                            $('#list_graduados').html(data);
//alert(data);
                            stopLoad();
                            
                            $("#list_graduados").on("click", ".pagination a", function (e) {
                                startLoad();
                                e.preventDefault();
                                var page = $(this).attr("data-page"); //get page number from link
                                $("#list_graduados").load("src/actualizacion_datosCB.php", {
                                    "accion":"listado",
                                    "page": page,
                                    "buscar": $("#buscar").val(),
                                    "cead": $("#cead").val(),
                                    "zona": $("#zona").val(),
                                    "programa": $("#programa").val(),
                                    "escuela": $("#escuela").val(),
                                    "registros": $("#registros").val()
                                },
                                function () { //get content from PHP page
                                    stopLoad();
                                });
                                document.getElementById('p_fieldset_autenticacion_2').scrollIntoView(true);
                            });
                        }

                    });
                    return false;
                }
                
                function startLoad() {
                    $('#list_graduados').hide();
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
                    $('#list_graduados').show();
                    var loader = $('#dynElement').data('introLoader');
                    loader.stop();
                }
                function send_mail(doc){
                    var dataString = {
                                "accion": "mail",
                                "doc": doc
                            };
                        $.ajax({
                            type: "POST",
                            url: "src/actualizacion_datosCB.php",
                            data: dataString,
                            dataType: "JSON",
                            beforeSend: function () {
                                
                            },
                            success: function (data) {
                                if(data['data']){
                                    swal({
                                        title: '¡Invitación enviada!',
                                        text: '',
                                        type: 'success',
                                        confirmButtonColor: '#004669',
                                        confirmButtonText: 'Aceptar'
                                    });
                                }else {
                                    swal({
                                        title: '¡Ocurrio un error al enviar el correo!',
                                        text: '',
                                        type: 'error',
                                        confirmButtonColor: '#004669',
                                        confirmButtonText: 'Aceptar'
                                    });
                                }
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
                            <!--opciones inicio-->
                            <!--listado de inicio-->

                            <div align="center" style="background-color: #004669">
                                <h2 id='p_fieldset_autenticacion_2'>CONTROL GRADUADOS</h2>
                            </div>
                            <br/>                    
                            <div>
                                <div id="filtros">
                                    <input type="hidden" id="accion" value="listado"/>
                                    <?php include "sigra_filtro.php"; ?>
                                </div>
                                <br/>
                                <div id="dynElement" >  </div>
                                <div align="center" id="list_graduados">


                                </div>
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
