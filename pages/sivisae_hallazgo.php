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
            $respuesta = $consulta->perfiles();
            $sedes = $consulta->traerSedes();
            ?>

            <!--contenedor-->
            <link rel="stylesheet" type="text/css" href="template/css/estilo_index.css">
            <link rel="stylesheet" type="text/css" href="js/Chosen1.4/chosen.css">
            <link rel="stylesheet" type="text/css" href="js/Chosen1.4/chosen.min.css">
            <link type="text/css" rel="stylesheet" href="js/qtip/jquery.qtip.css" />
            <script src="js/sweetalert-master/dist/sweetalert-dev.js" type="text/javascript" languaje="javascript"></script>
            <link rel="stylesheet" href="js/sweetalert-master/dist/sweetalert.css">
            <link rel="stylesheet" href="template/popup/style.min.css">
            <script src="js/popup/bpopup.js" type="text/javascript"></script>
            <link rel="stylesheet" type="text/css" href="template/popup/estilo_popup.css">
            <link rel="stylesheet" type="text/css" href="template/grilla/estilo_grilla.css">
            <script src="js/Chosen1.4/chosen.jquery.js" type="text/javascript" language="javascript"></script>
            <script src="js/Chosen1.4/chosen.jquery.min.js" type="text/javascript" language="javascript"></script>

            <script type="text/javascript" src="js/qtip/jquery.qtip.js"></script>

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
                    window.location.hash = "Again-No-back-button" //chrome
                    window.onhashchange = function () {
                        window.location.hash = "no-back-button";
                    }
                }

                function listaEstudiantes() {

                    if ($('#periodo').val() != '') {
                        var form = document.estudiantes_asignados;
                        var dataString = $(form).serialize();
                        $.ajax({
                            type: 'POST',
                            url: 'src/hallazgosCB.php',
                            data: dataString,
                            beforeSend: function () {
                                $("#spinner").show();
                            },
                            success: function (data) {
                                $('#list_estudiantes').html(data);
                                $("#spinner").hide();
                                var aud = $("#auditor").val();
                                var reg = $("#registros").val();
                                var search = $("#buscar").val();
                                var periodo = $("#periodo").val();
                                var escuela = $("#escuela").val();
                                var programa = $("#programa").val();

                                $("#list_estudiantes").on("click", ".pagination a", function (e) {
                                    e.preventDefault();
                                    $("#spinner").show();
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
                                        $("#spinner").hide();
                                    });
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
                        //                e.preventDefault();
                        //                $('#popup_caracterizacion').bPopup({
                        //            content:'iframe', //'ajax', 'iframe' or 'image'
                        //            contentContainer:'.content',
                        //            loadUrl:'http://www.google.com' //Uses jQuery.load()
                        //        });
                    });
                }

            </script>
            <!--scripts de funcionalidad - fin-->

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
                    <!--opciones fin-->


                    <!--listado de usuarios inicio-->

                    <div align="center" style="background-color: #004669">
                        <h2 id='p_fieldset_autenticacion_2'>
                            OBSERVACIONES SEGUIMIENTO ACADÉMICO 
                        </h2>
                    </div>
                    <div class="art-postcontent">
                        <div align="center">
                            <form id="estudiantes_asignados" name="estudiantes_asignados">
                                <br>
                                <?php
                                include "sivisae_filtro_observaciones.php";
                                // echo "Tu dirección IP es: {$_SERVER['REMOTE_ADDR']}";
                                ?>
                                <div id="list_estudiantes">
                                    <div id="spinner" align="center" style="display:none;">
                                        <img id="img-spinner" width="50" height="50" src="template/imagenes/generales/loading.gif" alt="Loading"/>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

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
