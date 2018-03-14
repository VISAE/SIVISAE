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
            $_SESSION['opc_ve'] = "class='boton_ver_encuesta'";


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
            <script src="js/introLoader/jquery.introLoader.js" type="text/javascript" language="javascript"></script>
            <script src="js/introLoader/spin.min.js" type="text/javascript" language="javascript"></script>


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
        });

        function nobackbutton() {
            window.location.hash = "no-back-button";
            window.location.hash = "Again-No-back-button" //chrome
            window.onhashchange = function () {
                window.location.hash = "no-back-button";
            }
        }

        function listaEstudiantes() {
            var form = document.gestion_auditores;
            var dataString = $(form).serialize();
            $.ajax({
                type: 'POST',
                url: 'src/reporte_egresadosCB.php',
                data: dataString,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#list_estudiantes').html(data);
                    var aud = $("#auditor").val();
                    var reg = $("#registros").val();
                    var search = $("#buscar").val();
                    var periodo = $("#periodo").val();
                    var escuela = $("#escuela").val();
                    var programa = $("#programa").val();

                    $("#list_estudiantes").on("click", ".pagination a", function (e) {
                        e.preventDefault();
                        startLoad();
                        var page = $(this).attr("data-page"); //get page number from link
                        $("#list_estudiantes").load("src/reporte_egresadosCB.php", {
                            "page": page,
                            "auditor": aud,
                            "registros": reg,
                            "buscar": search,
                            "periodo": periodo,
                            "escuela": escuela,
                            "programa": programa
                        },
                        function () { //get content from PHP page 
                            stopLoad();
                        });
                    });
                }
            });
            return false;
        }
        function crearReporte(tipo) {
            
            document.getElementById("tipo_r").value = tipo;
            var form = document.gestion_auditores;
            var dataS = $(form).serialize();
            $.ajax({
                type: 'POST',
                url: 'src/reporte_egresados_excel.php?p=1',
                data: dataS,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    swal({
                        title: '¡Descargue su documento!',
                        text: "<a href='" + data + "' id='pdf' class='botones'>AQUÍ</a>",
                        type: 'success',
                        html: true,
                        confirmButtonColor: '#004669',
                        confirmButtonText: 'Aceptar'
                    });

                }
            });
            return false;
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
                            REPORTE DE EGRESADOS
                        </h2>
                    </div>
                    <div class="art-postcontent">
                        <div id="dynElement" >
                        </div>
                        <div align="center">
                            <form id="gestion_auditores" name="gestion_auditores">
                                <br>
                                <?php
                                include "sivisae_filtro_egresados.php";
                                // echo "Tu dirección IP es: {$_SERVER['REMOTE_ADDR']}";
                                ?>
                                <div id="list_estudiantes">
                                </div>
                                <input style="width: 180px;" id="tipo_r" name="tipo_r" type="hidden" maxlength="30" />
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
