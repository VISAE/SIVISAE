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
            include "../template/sivisae_link_home.php";
            ?>

            <!--contenedor-->
            <link rel="stylesheet" type="text/css" href="template/css/estilo_index.css">
            <link rel="stylesheet" type="text/css" href="template/grilla/estilo_grilla.css">
            <!--bloqueo hacia atrás-->
            <script>
                function nobackbutton() {
                    window.location.hash = "no-back-button";
                    window.location.hash = "Again-No-back-button" //chrome
                    window.onhashchange = function () {
                        window.location.hash = "no-back-button";
                    }
                }
            </script>

            <style>
                .tab a{ background-color: #004669; font-weight: bold; text-decoration:none; color: #FFFFFF; display:block; float:left; width:100px; height:30px; text-align:center; border:1px solid #FFFFFF;}
                .clear{ clear:both;}
                #primero{ width:100%; height:520px; border: 2px solid #004669;}
                #segundo{ width:100%; height:250px; border: 2px solid #004669;}
            </style>

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
                    <?php include "sivisae_barra_estado.php"; ?>
                    <!--Barra de estado fin-->
                    <div align="center">
                        <h2 id='p_fieldset_autenticacion'>
                            Repositorio SIVISAE
                        </h2>
                    </div>
                    <div class="tab">
                        <a href="#primero">2016</a><a href="#segundo">Instructivos</a><div class="clear"></div>
                    </div>
                    <div class="contenedor">
                        <div id="primero">
                            <br>
                            <div align="center">
                                <table id="tb_estudiantes" class="tg" style="table-layout: fixed; width: 70%;">
                                    <tr>
                                        <th style="width: 20%;">Eje</th>
                                        <th>Documento</th>
                                    </tr>
                                    <tr>
                                        <th rowspan="2">Caracterización</th>
                                        <td><strong>Informe Caracterización 16-2</strong> - <a href="../sivisae/repositorio/2016/Informe_de_Caracterizacion_16_2.pdf" target="_blank" class='botones_descargar'>Descargar</a> </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Informe Caracterización 16-1</strong> - <a href="../sivisae/repositorio/2016/Informe_de_Caracterizacion_16_1.pdf" target="_blank" class='botones_descargar'>Descargar</a> </td>
                                    </tr>
                                    <tr>
                                        <th rowspan="10">Inducción</th>
                                        <td><strong>Informe Inducción 16-2</strong> - <a href="#" class='botones_descargar'>Próximamente</a> </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Informe Inducción 16-1</strong> - <a href="../sivisae/repositorio/2016/Informe_de_Induccion_16_1.pdf" target="_blank" class='botones_descargar'>Descargar</a> </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Programa de acogida e inducción UNADISTA</strong> - <a href="../sivisae/repositorio/2016/PROGRAMA_DE_ACOGIDA_E_INDUCCION_UNAD_2015.pdf" target="_blank" class='botones_descargar'>Descargar</a> </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Paso 1. Generar acta de matrícula</strong> - <a href="../sivisae/repositorio/2016/Paso_1_Generar_acta_de_matricula.pdf" target="_blank" class='botones_descargar'>Descargar</a> </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Paso 2. Generar usuario y contraseña</strong> - <a href="../sivisae/repositorio/2016/Paso_2_Generar_usuario_y_contrasena.pdf" target="_blank" class='botones_descargar'>Descargar</a> </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Paso 3. Realizar la prueba de caracterización</strong> - <a href="../sivisae/repositorio/2016/Paso_3_Realizar_la_prueba_de_caracterizacion.pdf" target="_blank" class='botones_descargar'>Descargar</a> </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Paso 4. Activar el Correo Institucional</strong> - <a href="../sivisae/repositorio/2016/Paso_4_Activar_el_Correo_Institucional.pdf" target="_blank" class='botones_descargar'>Descargar</a> </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Paso 5. Actualizar el perfil en campus virtual</strong> - <a href="../sivisae/repositorio/2016/Paso_5_Actualizar_el_perfil_en_campus_virtual.pdf" target="_blank" class='botones_descargar'>Descargar</a> </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Paso 6. Revisar la caja de herramientas</strong> - <a href="../sivisae/repositorio/2016/Paso_6_Revisar_la_caja_de_herramientas.pdf" target="_blank" class='botones_descargar'>Descargar</a> </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Paso 7. Curso de Entrenamiento</strong> - <a href="../sivisae/repositorio/2016/Paso_7_Curso_de_Entrenamiento.pdf" target="_blank" class='botones_descargar'>Descargar</a> </td>
                                    </tr>
                                    <tr>
                                        <th rowspan="1">Atención</th>
                                        <td><strong>Documento de atención al Aspirante y Estudiante UNADISTA</strong> - <a href="../sivisae/repositorio/2016/Atencion_y_Orientacion_del_Aspirante_y_Estudiante.pdf" target="_blank" class='botones_descargar'>Descargar</a> </td>
                                    </tr>
                                    <tr>
                                        <th rowspan="2">Consejería</th>
                                        <td><strong>Propuesta Re-Significación Consejero Académico Unadista</strong> - <a href="../sivisae/repositorio/2016/Propuesta_resignificacion_Consejero.pdf" target="_blank" class='botones_descargar'>Descargar</a> </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Plan de acción pedagógico contextualizado - PAPC</strong> - <a href="../sivisae/repositorio/2016/Plan_de_Accion_Pedagogico_Contextualizado.pdf" target="_blank" class='botones_descargar'>Descargar</a> </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div id="segundo">
                            <br>
                            <table width="100%">
                                <tr>
                                    <td>
                                        <p align="center" id='p_fieldset_autenticacion'> 
                                            <img src="template/imagenes/generales/ayuda_banner.png" width="100" height="100"></img> 
                                        </p>
                                    </td>
                                    <td>
                                        <p align="center" id='p_fieldset_autenticacion'><?php echo 'Instructivo Acceso Prueba de Caracterización'; ?> <a href="../sivisae/repositorio/instructivos/instructivo_prueba_caracterizacion.pdf" target="_blank">Abrir</a></p>
                                        <p align="center" id='p_fieldset_autenticacion'><?php echo 'Link de Acceso para la Evaluación de Inducción Presencial'; ?> <a href="https://sivisae.unad.edu.co/induccion/induccion_evaluacion_presencial.php" target="_blank">Abrir</a></p>
                                        <p align="center" id='p_fieldset_autenticacion'><?php echo 'Link de Acceso para la Evaluación de Inducción Virtual'; ?> <a href="https://sivisae.unad.edu.co/induccion/induccion_evaluacion_virtual.php" target="_blank">Abrir</a></p>
                                        <p align="center" id='p_fieldset_autenticacion'><?php echo 'Software Estudiantes NEE'; ?> <a href="../sivisae/repositorio/instructivos/software_estudiantes_nee.pdf" target="_blank">Abrir</a></p>
                                        <p align="center" id='p_fieldset_autenticacion'><?php echo 'Instructivo Chat'; ?> <a href="../sivisae/repositorio/instructivos/instructivo_chat.pdf" target="_blank">Descargar</a></p>
                                        <p align="center" id='p_fieldset_autenticacion'><?php echo 'Instructivo Acceso Nodos Virtuales'; ?> <a href="../sivisae/repositorio/instructivos/instructivo_nodos.pdf" target="_blank">Descargar</a></p>
                                    </td>
                                </tr>
                            </table>                        
                        </div>
                    </div>
                </div>
                <!--aqui contenido fin-->
            </main>

            <?php
            //Pie de pagina
            include "../template/sivisae_footer_home.php";
            ?>
            <script language="javascript">
        <!--
                $(function (activar_pestanya) {
                    var tabContainerssup = $('div.contenedor > div');

                    $('div.tab a').click(function () {
                        tabContainerssup.hide().filter(this.hash).show();

                        return false;
                    }).filter(':first').click();
                });
        //-->
            </script>
        </body>
        <?php
    }
}
$consulta->destruir();
?>
