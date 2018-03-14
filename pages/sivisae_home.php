<?php
session_start();
include_once '../config/sivisae_class.php';
$consulta = new sivisae_consultas();

//Se consultan las noticias
$pf = $_SESSION['perfilid'];
$resNoticia = $consulta->consultarNoticias($pf);
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


            <!--bloqueo hacia atrás-->
            <script>
                function nobackbutton() {
                    window.location.hash = "no-back-button";
                    window.location.hash = "Again-No-back-button" //chrome
                    window.onhashchange = function () {
                        window.location.hash = "no-back-button";
                    }
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

            <?php
            // opcion directorio
            $class_directory = "boton_d";
            $disabled_directory = "";
            ?>

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
                <div>

                    <!--Barra de estado inicio-->
                    <?php include "sivisae_barra_estado.php"; ?>
                    <!--Barra de estado fin-->

                    <!--opciones inicio-->
                    <div align="right">
                        <button title="Directorio Unadista (Doble Click)" id="boton_directorio" onclick="activarpopupdirectorio()" <?php echo $disabled_directory; ?> class="<?php echo $class_directory; ?>"></button>    
                    </div>
                    <!--opciones fin-->


                    <div align="center">

                        <?php
                        if ($_SESSION["fecha_nac"] == $_SESSION["fecha_server"]) {
                            ?>
                            <div align="center">
                                <h2 id='p_fieldset_autenticacion'>
                                    Feliz Cumpleaños <?php echo $_SESSION["nom"]; ?>
                                </h2>
                            </div>
                            <div align="center">
                                <img src="template/imagenes/generales/cumpleanos.gif" width="100%" height="100"></img>
                            </div>

                            <?php
                        } else {
                            $navidad = 0; // Mensaje de Navidad

                            if ($navidad == 1) {
                                ?>
                                <div align="center">
                                    <h2 id='p_fieldset_autenticacion'>
                                        Para tí <?php echo $_SESSION["nom"]; ?>
                                    </h2>
                                </div>
                                <div align="center">
                                    <img src="template/imagenes/generales/navidad.gif" width="100%" height="100"></img>
                                </div>
                                <?php
                            } else {
                                ?>
                                <h2 id='p_fieldset_autenticacion'>
                                    Bienvenido al Sistema de Información de la Vicerrectoria de Servicios a Aspirantes, Estudiantes y Egresados - SIVISAE
                                </h2>
                                <?php
                            }
                        }
                        ?>



                    </div>
                    <div class="art-postcontent">
                        <div align="center">
                            <table width="100%" cellpadding="0" cellspacing="1">
                                <tr>
                                    <td width="100%" class="news_head">
                                        <div class="calendario_news_head_text" >
                                            <i>Últimas Noticias</i>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="100%"  class="news_content">
                                        <div id="noticias">
                                            <ul>
                                                <?php
                                                $cont = 0;
                                                $ban_color = 1;
                                                $color = '#F9F9F9 ';
                                                while ($fila = mysql_fetch_array($resNoticia)) {
                                                    $cont++;
                                                    ?>
                                                    <div style="background: <?php echo $color; ?>">

                                                        <table width="100%">
                                                            <tr>
                                                                <td width="70%">
                                                                    <p class="news_content_title">
                                                                        <strong><?php echo $fila[1]; ?></strong>
                                                                        <br>
                                                                        <strong class="news_content_date"><?php echo $fila[2]; ?></strong>
                                                                    </p>
                                                                    <p class="news_content_text">
                                                                        <?php echo $fila[3]; ?>
                                                                    </p>
                                                                    <?php
                                                                    if ($fila[4] != '') {
                                                                        ?>
                                                                        <a target="_blank" href="<?php echo $fila[4]; ?>">
                                                                            <p align="right"><input type="button" value="Leer Más"/></p>
                                                                        </a>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                    <br>
                                                                </td>
                                                                <td width="30%">
                                                                    <img src="<?php echo $fila[5]; ?>" width="100%" height="200"></img>
                                                                </td>
                                                            </tr>
                                                        </table>



                                                    </div>
                                                    <?php
                                                    if ($ban_color == 1) {
                                                        $ban_color = 2;
                                                        $color = '#F5EFEA';
                                                    } else {
                                                        $ban_color = 1;
                                                        $color = '#F9F9F9 ';
                                                    }
                                                }

                                                if ($cont <= 0) {
                                                    echo 'No hay noticias.';
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            </table>                        
                        </div>
                    </div>
                </div>

                <!--directorio unadista-->
                <div id="popup_directorio">
                    <span class="button_cerrar b-close"></span>
                    <div align="center" style="background-color: #004669" >
                        <h2 id='p_fieldset_autenticacion_2'>
                            Directorio Unadista
                        </h2>
                    </div>
                    <div  class="art-postcontent">
                        <div align="center">
                            <form id="directorio" name="directorio" method="post" onsubmit="return submitFormDirectorioBuscar()" action="">
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
                <!--directorio unadista fin-->

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
