<?php
session_start();
include_once '../config/sivisae_class.php';
$consulta = new sivisae_consultas();
$respuesta = $consulta->auditores();
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
            include "../template/sivisae_link_home.php";
            ?>

            <!--contenedor-->
            <link rel="stylesheet" type="text/css" href="template/css/estilo_index.css">
            <link rel="stylesheet" type="text/css" href="js/dataTable/media/css/jquery.dataTables.css">

        <!--<script type="text/javascript" language="javascript" src="../../media/js/jquery.js"></script>-->
            <script type="text/javascript" language="javascript" src="js/dataTable/media/js/jquery.dataTables.js"></script>
            <!--<script type="text/javascript" language="javascript" src="../js/resources/syntax/shCore.js"></script>-->
        <!--<script type="text/javascript" language="javascript" src="../resources/demo.js"></script>-->
            <script src="js/jquery.introLoader.js" type="text/javascript" language="javascript"></script>
            <script src="js/spin.min.js" type="text/javascript" language="javascript"></script>
            <style type="text/css" class="init">


                .contents{
                    margin: 20px;
                    padding: 20px;
                    list-style: none;
                    background: #323232;
                    border: 1px solid #ddd;
                    border-radius: 5px;
                }
                .contents li{
                    margin-bottom: 10px;
                }
                .loading-div{
                    position: absolute;
                    /*	top: 0;
                            left: 0;*/
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.56);
                    z-index: 999;
                    display:none;
                }
                /*.loading-div img {
                        margin-top: 20%;
                        margin-left: 50%;
                }*/

                /* Pagination style */
                .pagination{margin:0;padding:0;}
                .pagination li{
                    display: inline;
                    padding: 6px 10px 6px 10px;
                    border: 1px solid #ddd;
                    margin-right: -1px;
                    font: 15px/20px Arial, Helvetica, sans-serif;
                    background: #FFFFFF;
                    box-shadow: inset 1px 1px 5px #F4F4F4;
                }
                .pagination li a{
                    text-decoration:none;
                    color: rgb(89, 141, 235);
                }
                .pagination li.first {
                    border-radius: 5px 0px 0px 5px;
                }
                .pagination li.last {
                    border-radius: 0px 5px 5px 0px;
                }
                .pagination li:hover{
                    background: #CFF;
                }
                .pagination li.active{
                    background: #F0F0F0;
                    color: #333;
                }

                .tg  {border-collapse:collapse;border-spacing:0;border-color:#BBBBBB;border-width: 1px; border-style: solid;}
                .tg td{font-family:Arial, sans-serif;font-size:14px;padding:3px 30px;border-style:solid;border-width:0px;overflow:hidden;word-break:normal;border-color:#999;border-bottom-width:1px;border-bottom-color:#BBBBBB;color:#444;background-color:#F7FDFA;}
                .tg th{font-family:Arial, sans-serif;font-size:14px;font-weight:bold;padding:10px 5px;border-style:solid;border-width:0px;overflow:hidden;word-break:normal;border-color:#999;color:#fff;background-color:#004669;}
                .tg .tg-zt1t{font-family:Tahoma, Geneva, sans-serif !important;;background-color:#212170;color:#ffffff;text-align:center}
                .tg .tg-rwn8{background-color:#212170;color:#ffffff;text-align:center}
            </style>

            <script type="text/javascript" language="javascript">
                function listaEstudiantes() {
                    startLoad();
                    var form = document.estudiantes_asignados;
                    var dataString = $(form).serialize();
                    $.ajax({
                        type: 'POST',
                        url: 'src/asignacion_estudiantes.php',
                        data: dataString,
                        beforeSend: function () {
                            startLoad();
                            //$("#cargando").show();
                            //$("#list_estudiante").html("<br>Procesando, espere por favor... <br> <img src='template/imagenes/generales/sevwr7.jpg' />");
                        },
                        success: function (data) {
                            stopLoad();
                            $('#list_estudiantes').html(data);
                            $('#tb_estudiantes').DataTable();
                        }
                    });
                    return false;
                }
                function startLoad() {
                    $('table').hide();
                    $("#dynElement").introLoader({
                        animation: {
                            name: 'simpleLoader',
                            options: {
                                stop: false,
                                fixed: false,
                                exitFx: 'fadeOut',
                                ease: "linear",
                                style: 'light',
                                delayBefore: 500,
                            }
                        }
                    });
                }
                function stopLoad() {
                    $('table').show();
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
            </script>

        </head>

        <body onload="nobackbutton();
                        listaEstudiantes();">
            <!--Encabezado - Inicio-->
            <?php include "../template/sivisae_head_home.php"; ?>
            <!--Encabezado - Fin-->


            <!--Menu - Inicio-->
            <?php include "sivisae_menu.php"; ?>
            <!--Menu - Fin-->
            <main>
                <!--aqui contenido incio-->
                <div >

                    <br>
                    <!--Barra de estado inicio-->
                    <?php include "sivisae_barra_estado.php"; ?>
                    <!--Barra de estado fin-->
                    <div align="center" style="background-color: #004669" >
                        <h2 id='p_fieldset_autenticacion_2'>
                            Estudiantes asignados
                        </h2>
                    </div>
                    <div class="art-postcontent">
                        <div align="center">
                            <form id="estudiantes_asignados" name="estudiantes_asignados">
                                <br>
                                <div >
                                    <select name='auditor' id='auditor' data-placeholder='Seleccione un auditor...' onchange="listaEstudiantes()" style="width: 180px;" tabindex='2'>";
                                        <option value=''>Seleccione...</option>
                                        <option value=''>Todos</option>
                                        <?php
                                        while ($row = mysql_fetch_array($respuesta)) {
                                            $sbHtML2 = "<option value='$row[0]'>";
                                            $sbHtML2.= ucwords(strtolower($row[1]));
                                            $sbHtML2.="</option>";
                                        }
                                        echo $sbHtML2;
                                        ?>
                                    </select>
                                </div>
                                <br>
                                <div id="dynElement" style="height: 100px">
                                </div>
                                <div id="list_estudiantes" >
        <!--                                    <p><img id="cargando" src="template/imagenes/generales/cargando.gif" style="display: none" /></p>-->
                                </div>
                                <br><br>
                            </form>                    
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
