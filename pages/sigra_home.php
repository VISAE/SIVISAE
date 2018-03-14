<?php
session_start();
include_once '../config/sigra_class.php';
$consulta = new sigra_consultas();

//Se consultan las noticias
$resNoticia = $consulta->consultarNoticias();
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
            include "../template/sigra_link_home.php";
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

        </head>

        <body onload="nobackbutton();">
            <!--Encabezado - Inicio-->
            <?php include "../template/sigra_head_home.php"; ?>
            <!--Encabezado - Fin-->



        <main>
            <!--aqui contenido incio-->
            <div >
                <!--Menu - Inicio-->
                <?php include "sigra_menu.php"; ?>
                <!--Menu - Fin-->
                <!--Barra de estado inicio-->
                <?php include "sigra_barra_estado.php"; ?>
                <!--Barra de estado fin-->
                <div align="center">
                    <h2 id='p_fieldset_autenticacion'>
                        Bienvenido al Sistema de Información del Graduado Unadista - SIGRA
                    </h2>
                </div>
                <div class="art-postcontent">
                    <div align="center">
                        <table width="100%" cellpadding="0" cellspacing="1">
                            <tr>
                                <td width="80%" class="news_head">
                                    <div class="calendario_news_head_text" >
                                        <i>Noticias</i>
                                    </div>
                                </td>
                                <td width="20%" class="calendario_head">
                                    <div class="calendario_news_head_text">
                                        <i>Calendario</i>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td width="80%"  class="news_content">

                                    <div id="noticias">
                                        <ul>
                                            <?php
                                            $cont = 0;
                                            $ban_color = 1;
                                            $color = '#FBE2CF';
                                            while ($fila = mysql_fetch_array($resNoticia)) {
                                                $cont++;
                                                ?>
                                                <div style="background: <?php echo $color; ?>">
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
                                                </div>
                                                <?php
                                                if ($ban_color == 1) {
                                                    $ban_color = 2;
                                                    $color = '#F5EFEA';
                                                } else {
                                                    $ban_color = 1;
                                                    $color = '#FBE2CF';
                                                }
                                            }

                                            if ($cont <= 0) {
                                                echo 'No hay noticias.';
                                            }
                                            ?>
                                        </ul>
                                    </div>
                                </td>
                                <td width="20%" class="calendario_content">
                                    <div align="right">
                                        <iframe src="pages/sigra_calendario.php" height="500" width="310" scrolling="no" frameborder="0"></iframe>
                                    </div>
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
        include "../template/sigra_footer_home.php";
        ?>
        </body>
        <?php
    }
}
$consulta->destruir();
?>
