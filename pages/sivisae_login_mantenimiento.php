<?php
session_start();
include_once '../config/sivisae_class.php';

?>
<head>
    <?php
    include "../template/sivisae_link.php";
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
        if (isset($_SESSION['usuarioid'])) {
            header("Location: ".RUTA_PPAL."pages/sivisae_home.php");
        } else {
            session_destroy();
        }
        ?>

        <!--banner-->
        <link rel="stylesheet" href="template/banner/responsiveslides.css"/>
        <link rel="stylesheet" href="template/banner/demo.css"/>
        <script src="template/banner/jquery.min.js"></script>
        <script src="template/banner/responsiveslides.min.js"></script>
        <link href="template/banner/scroll_noticias.css" rel="stylesheet" />
        <script>
            // You can also use "$(window).load(function() {"
            $(function() {

                // Slideshow 1
                $("#slider1").responsiveSlides({
                    speed: 800
                });
            });
        </script>
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

    </head>

    <body onload="nobackbutton();">

    <?php
    include "../template/sivisae_head.php";
    ?>

        <!--Aqui contenido inicio-->
    <main>
        <div>
            <div>
                <!--aqui contenido incio-->
                <div id="banner">
                    <ul class="rslides" id="slider1">
                        <li><img width="100%" height="100%" src="template/imagenes/banner/sivisae_fondo_1.png" /></li>
                        <li><img width="100%" height="100%" src="template/imagenes/banner/sivisae_fondo_2.png" /></li>
                        <li><img width="100%" height="100%" src="template/imagenes/banner/sivisae_fondo_3.png" /></li>
                    </ul>
                </div>

                <div id="marco_autenticacion">
                    <form  name="login" method="post" action="src/loginCB.php">
                        <fieldset id='fieldset_autenticacion'>
                            <p id='p_fieldset_autenticacion'><label for="usuario"><strong>Usuario</strong></label></p>
                            <p style="text-align: center">
                                <input  maxlength="50" required="required" title="Por favor ingrese su usuario. El usuario debe ser mínimo de seis caracteres" autocomplete="off" class="input_fieldset_autenticacion" type="text" id="usuario" name="usuario" value=""/></p> 
                            <p id='p_fieldset_autenticacion'><label for="password"><strong>Contraseña</strong></label></p>
                            <p style="text-align: center">
                                <input  maxlength="45" required="required" title="Por favor ingrese su contraseña" autocomplete="off" class="input_fieldset_autenticacion" type="password" id="password" name="password" value = ""/></p> 
                            <p><input class="submit_fieldset_autenticacion" type="submit" value="Ingresar"/></p>
                            <p id='p_fieldset_autenticacion'><label><a class="input_fieldset_autenticacion" href="pages/sivisae_recordar_contrasena.php">¿Olvidó su contraseña?</a></label></p>
                        </fieldset>
                    </form>
                </div> <!-- end login -->
                <!--aqui contenido fin-->
            </div>
        </div>
    </main>
    <!--Aqui contenido fin-->
    <?php
    include "../template/sivisae_footer.php";
    ?>
    </body>
    <?php
}
?>
