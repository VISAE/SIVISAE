<?php
session_start();

$manteniemiento = 1;

if ($manteniemiento == 0) {
    ?>
    <div align="center">
        <img src="https://sivisae.unad.edu.co/sie/template/imagenes/generales/mantenimiento_induccion.jpg" width="70%" height="80%"/>
    </div>
    <div align="center">
        <p style="font-weight:normal;color:#000000;letter-spacing:1pt;word-spacing:2pt;font-size:21px;text-align:center;font-family:verdana, sans-serif;line-height:1;">
            Volveremos a las 12:00:01 del 26-Ago-2015.
        </p>
    </div>
    <?php
    exit;
}


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
            window.location = "sie_notifica.php?e=X01";
        </script>

        <?php
    } else {
        //Navegadores compatibles
        // Se valida inicio de sesion


        include "../template/sivisae_link_home.php";
        ?>

        <!--contenedor-->
        <link rel="stylesheet" type="text/css" href="template/css/estilo_index.css">
        <link rel="stylesheet" type="text/css" href="template/grilla/estilo_grilla.css">
        <link rel="stylesheet" type="text/css" href="template/css/estilo_directorio.css">

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

        <script type='text/javascript'>
            $(window).load(function () {
                $(document).ready(function () {
                    if ($('#name_directorio_nacional_consejeria')) {
                        $('#id_directorio_nacional_consejeria area').each(function () {
                            var id = $(this).attr('id');
                            $(this).mouseover(function () {
                                $('#overlay' + id).show();

                            });

                            $(this).mouseout(function () {
                                var id = $(this).attr('id');
                                $('#overlay' + id).hide();
                            });

                        });
                    }
                });
            });

        </script>


    </head>

    <body onload="nobackbutton();">
        <!--Encabezado - Inicio-->
        <?php //include "../template/sivisae_head_home.php"; ?>
        <!--Encabezado - Fin-->

        <main>
            <!--aqui contenido incio-->
            <div >
                <div align="center">
                    <table width="80%" height="500" border="0">
                        <tr>
                            <td width="50%">
                                <div align="left">
                                    <div align="left" id="overlayZCAR"><img id="" src="template/imagenes/mapa_directorio/mapa-sedes-caribe.jpg" border="0" width="300" height="400" orgWidth="300" orgHeight="400" /></div>
                                    <div align="left" id="overlayZCORI"><img id="" src="template/imagenes/mapa_directorio/mapa-sedes-oriente.jpg" border="0" width="300" height="400" orgWidth="300" orgHeight="400" /></div>
                                    <div align="left" id="overlayZBOY"><img id="" src="template/imagenes/mapa_directorio/mapa-sedes-boyaca.jpg" border="0" width="300" height="400" orgWidth="300" orgHeight="400" /></div>
                                    <div align="left" id="overlayZCBC"><img id="" src="template/imagenes/mapa_directorio/mapa-sedes-cundinamarca.jpg" border="0" width="300" height="400" orgWidth="300" orgHeight="400" /></div>
                                    <div align="left" id="overlayZCSUR"><img id="" src="template/imagenes/mapa_directorio/mapa-sedes-centro-sur.jpg" border="0" width="300" height="400" orgWidth="300" orgHeight="400" /></div>
                                    <div align="left" id="overlayZOCC"><img id="" src="template/imagenes/mapa_directorio/mapa-sedes-occidente.jpg" border="0" width="300" height="400" orgWidth="300" orgHeight="400" /></div>
                                    <div align="left" id="overlayZSUR"><img id="" src="template/imagenes/mapa_directorio/mapa-sedes-sur.jpg" border="0" width="300" height="400" orgWidth="300" orgHeight="400" /></div>
                                    <div align="left" id="overlayZAO"><img id="" src="template/imagenes/mapa_directorio/mapa-sedes-amazonia.jpg" border="0" width="300" height="400" orgWidth="300" orgHeight="400" /></div>

                                    <img id="id_directorio_nacional_consejeria" src="template/imagenes/mapa_directorio/mapa-sedes.jpg" border="0" width="300" height="400" orgWidth="300" orgHeight="400" alt="" />

                                </div>
                            </td>

                            <td width="50%">
                                <div align="center" id="etiquetas">
                                    <img id="id_directorio_nacional_consejeria" 
                                         src="template/imagenes/mapa_directorio/etiquetas.jpg" border="0" width="400" height="500" orgWidth="400" orgHeight="500" 
                                         usemap="#name_directorio_nacional_consejeria" alt="" />
                                    <map name="name_directorio_nacional_consejeria" id="id_directorio_nacional_consejeria">
                                        <area id="ZCAR" alt="Zona Caribe" title="Zona Caribe" href="../sivisae/pages/sivisae_directorio_consejeria.php?z=4" shape="rect" coords="0,84,400,122" style="outline:none;" target="_self"     />
                                        <area id="ZOCC" alt="Zona Occidente" title="Zona Occidente" href="../sivisae/pages/sivisae_directorio_consejeria.php?z=6" shape="rect" coords="0,121,400,160" style="outline:none;" target="_self"     />
                                        <area id="ZCORI" alt="Zona Centro Oriente" title="Zona Centro Oriente" href="../sivisae/pages/sivisae_directorio_consejeria.php?z=2" shape="rect" coords="0,159,400,199" style="outline:none;" target="_self"     />
                                        <area id="ZBOY" alt="Zona Centro Boyacá" title="Zona Centro Boyacá" href="../sivisae/pages/sivisae_directorio_consejeria.php?z=9" shape="rect" coords="0,197,400,237" style="outline:none;" target="_self"     />
                                        <area id="ZCBC" alt="Zona Centro Bogota y Cundinamarca" title="Zona Centro Bogota y Cundinamarca" href="../sivisae/pages/sivisae_directorio_consejeria.php?z=1" shape="rect" coords="0,236,400,274" style="outline:none;" target="_self"     />
                                        <area id="ZCSUR" alt="Zona Centro Sur" title="Zona Centro Sur" href="../sivisae/pages/sivisae_directorio_consejeria.php?z=3" shape="rect" coords="0,272,400,312" style="outline:none;" target="_self"     />
                                        <area id="ZSUR" alt="Zona Sur" title="Zona Sur" href="../sivisae/pages/sivisae_directorio_consejeria.php?z=5" shape="rect" coords="0,311,400,349" style="outline:none;" target="_self"     />
                                        <area id="ZAO" alt="Zona Amazonia y Orinoquia" title="Zona Amazonia y Orinoquia" href="../sivisae/pages/sivisae_directorio_consejeria.php?z=7" shape="rect" coords="0,349,400,390" style="outline:none;" target="_self"     />
                                    </map>
                                </div>                            
                            </td>
                        </tr>
                    </table>
                </div>

                <?php
                if (isset($_GET["z"])) {
                    $zona = $_GET["z"];
                    //Buscar los centros de la zona
                    $centros = $consulta->traerCentrosDirectorio($zona);
                    $cont = 0;
                    while ($row = mysql_fetch_array($centros)) {
                        $cont++;
                        if ($cont == 1) {
                            ?>
                            <div align="center" style="background-color: #004669" >
                                <h2 id='p_fieldset_autenticacion_2'>
                                    ZONA <?php echo $row[7]; ?>
                                </h2>
                            </div>
                            <table border="1" class="tg" style="table-layout: fixed;" width="100%">
                                <thead>
                                    <tr>
                                        <th>
                                            CENTRO
                                        </th>
                                        <th>
                                            DIRECTOR
                                        </th>
                                        <th>
                                            CONSEJERÍA
                                        </th>
                                    </tr>
                                </thead>
                            <?php }
                            ?>
                            <tr>
                                <td>
                                    <?php echo $row[1] . '<br> <b>Dirección: </b>' . $row[2] . '<br> <b>Teléfono: </b>' . $row[3] . '<br> <b>Correo: </b>' . $row[6]; ?>
                                </td>
                                <td>
                                    <?php echo $row[5] . '<br> <b>Correo: </b>' . $row[4]; ?>
                                </td>
                                <td>
                                    <?php
                                    $consejerosCentro = $consulta->consultarConsejerosCentro($row[0]);
                                    $contC = 0;
                                    while ($rowC = mysql_fetch_array($consejerosCentro)) {
                                        $contC++;
                                        $atencion = "";
                                        if ($rowC[3] != "") {
                                            $atencion.="<b>Lunes:</b> " . $rowC[3];
                                        }
                                        if ($rowC[4] != "") {
                                            $atencion.=" <b>Martes:</b> " . $rowC[4];
                                        }
                                        if ($rowC[5] != "") {
                                            $atencion.=" <b>Miércoles:</b> " . $rowC[5];
                                        }
                                        if ($rowC[6] != "") {
                                            $atencion.=" <b>Jueves:</b> " . $rowC[6];
                                        }
                                        if ($rowC[7] != "") {
                                            $atencion.=" <b>Viernes:</b> " . $rowC[7];
                                        }
                                        if ($rowC[8] != "") {
                                            $atencion.=" <b>Sábado:</b> " . $rowC[8];
                                        }
                                        if ($atencion == "") {
                                            $atencion.="El consejero no tiene horarios de atención en chat. Contáctelo vía correo.";
                                        }
                                        echo '<br>' . $rowC[0] . '<br> <b>Skype: </b>' . $rowC[2] . '<br> <b>Correo: </b>' . $rowC[1] . '<br> <b>Atención: </b>' . $atencion . '<br>';
                                    }
                                    if ($contC == 0) {
                                        echo '<br> <b>Correo: </b>' . $row[4];
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php
                        }
                        ?></table><?php
                }
                ?>
            </div>
            <!--aqui contenido fin-->
        </main>

        <?php
        //Pie de pagina
        //include "../template/sivisae_footer_home.php";
        ?>




    </body>
    <?php
}

$consulta->destruir();
?>
