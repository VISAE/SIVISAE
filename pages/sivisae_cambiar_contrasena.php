<?php
session_start();
include_once '../config/sivisae_class.php';
$param = $_GET["p"];
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
            //sesion iniciada, se hace el proceso
            include "../template/sivisae_link.php";
            ?>

            <!--contenedor-->
            <link rel="stylesheet" type="text/css" href="template/css/estilo_index.css">

            <!--bloqueo hacia atrás-->
            <script>
                function nobackbutton() {
                    window.location.hash = "no-back-button";
                    window.location.hash = "Again-No-back-button" //chrome
                    window.onhashchange = function() {
                        window.location.hash = "no-back-button";
                    }
                }
            </script>

            <!--scripts de funcionalidad - inicio-->
            <script type="text/javascript">
                function submitForm() {
                    $("#spinner").show();
                    var form = document.cambio_pass;
                    var dataString = $(form).serialize();
                    $.ajax({
                        type: 'POST',
                        url: 'src/cambiar_contrasenaCB.php',
                        data: dataString,
                        success: function(data) {
                            limpiaForm(form);
                            $('#result').html(data);
                            $("#spinner").hide();
                            setTimeout('redireccionar()', 3000);
                        }
                    });
                    return false;
                }

                function limpiaForm(miForm) {
                    // recorremos todos los campos que tiene el formulario
                    $(':input', miForm).each(function() {
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
                            this.selectedIndex = -1;
                    });

                }

                function redireccionar() {
                    window.location = "<?php echo RUTA_PPAL ?>";
                }

            </script>

        </head>

        <body onload="nobackbutton();">

            <?php
            //Encabezado
            include "../template/sivisae_head.php";
            ?>


        <main>
            <div>
                <!--aqui contenido incio-->
                <div align="center">
                    <div class="">
                        <div align="center">
                            <h2 id='p_fieldset_autenticacion'>
                                Cambio de Contrase&ntilde;a
                            </h2>
                        </div>
                    </div>
                    </br>
                    <div class="art-postcontent">

                        <div align="center">

                            <form id="cambio_pass" name="cambio_pass" onsubmit="return submitForm()" method="post" action="src/cambiar_contrasenaCB.php">

                                <div class="art-postcontent">
                                    <!--<div align="center">-->
                                    <table width="50%">
                                        <tr>
                                            <td>
                                                <p><label class="input_fieldset_autenticacion" >
                                                        Contraseña anterior:
                                                    </label></p>
                                            </td>
                                            <td>
                                                <p><input minlength=6 maxlength="45" required="required" title="Por favor ingrese su contraseña" autocomplete="off" class="input_fieldset_autenticacion" type="password" id="password_old" name="password_old" value = ""/></p> 
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p> <label class="input_fieldset_autenticacion" >
                                                        Contraseña nueva:
                                                    </label></p>
                                            </td>
                                            <td>
                                                <p><input minlength=6 maxlength="45" required="required" title="Por favor ingrese su contraseña" autocomplete="off" class="input_fieldset_autenticacion" type="password" id="password_new" name="password_new" value = ""/></p> 
                                            </td>
                                        </tr>
                                        <tr>
                                            <?php
                                            if ($param == '1') {
                                                ?>
                                                <td colspan="2">
                                                    <p><input class="submit_fieldset_autenticacion" type="submit" value="Cambiar"/></p>
                                                </td>
                                                <?php
                                            } else if ($param == '0') {
                                                ?>
                                                <td>
                                                    <p><input class="submit_fieldset_autenticacion" type="submit" value="Cambiar"/></p>
                                                </td>
                                                <td>
                                                    <a href="<?php echo RUTA_PPAL . 'pages/sivisae_home.php'; ?>">
                                                        <p><input class="submit_fieldset_autenticacion" type="button" value="volver"/></p>
                                                    </a>
                                                </td>
                                                <?php
                                            }
                                            ?>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <div align="center" id="result"></div>
                                                <div id="spinner" align="center" style="display:none;">
                                                    <img id="img-spinner" width="50" height="50" src="template/imagenes/generales/loading.gif" alt="Loading"/>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                    <br>
                                    <br>
                                    <p align="center" id='p_fieldset_autenticacion'>Para una mejor navegación en el sitio se recomienda utilizar los navegadores</p>
                                    <a href="http://www.google.com/intl/es-419/chrome/" target="_blank" title="Descargar Google Chrome"> <img src="template/imagenes/generales/chrome.png" width="60" height="60"></img></a> 
                                    <a href="https://www.mozilla.org/es-ES/firefox/new/" target="_blank" title="Descargar Mozilla Firefox"> <img src="template/imagenes/generales/firefox.png" width="60" height="60"></img></a> 
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
                <!--aqui contenido fin-->
            </div>
        </main>



        <?php
        //Pie de pagina
        include "../template/sivisae_footer.php";
        ?>
        </body>
        <?php
    }
}
?>
