<head>
    <?php
    include_once '../config/sivisae_class.php';
    // Códigos de Tranasacción
    $cod_error = $_GET["e"];
    if ($cod_error == 'X01') {
        $titulo_error = 'Navegador No compatible';
        $detalle_error = 'Su navegador no es compatible con algunos complementos con el SIVISAE. Recomendamos descargue un navegador actualizado (Chrome, Firefox) e intente nuevamente. ';
    }
    if ($cod_error == 'X02') {
        $titulo_error = 'Contenido Protegido';
        $detalle_error = 'Es necesario que inicie sesión.';
    }
    if ($cod_error == 'X03') {
        $titulo_error = 'Credenciales Incorrectas';
        $detalle_error = 'Su usuario o contraseña son incorrectas, verifiquelas he intente nuevamente.';
    }
    if ($cod_error == 'X04') {
        $titulo_error = 'Cuenta Expir&oacute;';
        $detalle_error = 'Su clave a vencido, por favor cambiela para que le sea renovada.';
    }
    if ($cod_error == 'X05') {
        $titulo_error = 'Usuario Inactivo';
        $detalle_error = 'Su usuario se encuentra inactivo, por favor comuniquese con el administrador del SIVISAE.';
    }
    if ($cod_error == 'X06') {
        $titulo_error = 'SIVISAE Cerrado';
        $detalle_error = 'El Sistema de Información del Estudiante Unadista se encuentra cerrado.';
    }
    if ($cod_error == 'X07') {
        $titulo_error = 'SIVISAE en Mantenimiento';
        $detalle_error = 'El Sistema de Información del Estudiante Unadista se encuentra en mantenimiento.';
    }
    if ($cod_error == 'C01') {
        $titulo_error = 'Cambio Realizado';
        $detalle_error = 'Sera redireccionado a la pagina de inicio.';
    }
    if ($cod_error == '') {
        $titulo_error = 'Código no Definido';
        $detalle_error = 'Acceso restringido, contacte al administrador del sistema.';
    }
    //Navegadores compatibles
    include "../template/sivisae_link.php";
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
    <script type="text/javascript">
        function redireccionar() {
            window.location = "<?php echo RUTA_PPAL; ?>";
        }
        setTimeout("redireccionar()", 5000); //tiempo expresado en milisegundos
    </script>
</head>

<body onload="nobackbutton();">

    <?php
    //Encabezado
    include "../template/sivisae_head.php";
    ?>


<main>
    <div>
        <!--aqui contenido inicio-->
        <div align="center">
            <div class="">
                <div align="center">
                    <h2 id='p_fieldset_autenticacion'>
                        <?php echo $titulo_error; ?>
                    </h2>
                </div>
            </div>
            </br>
            <div class="art-postcontent">
                <div align="center">
                    <table width="80%">
                        <tr>
                            <td>
                                <p align="center" id='p_fieldset_autenticacion'> 
                                    <?php
                                    if ($cod_error === 'C01') {
                                        ?>
                                        <img src="template/imagenes/generales/ok.png" width="100" height="100"></img> 
                                        <?php
                                    } else {
                                        ?>
                                        <img src="template/imagenes/generales/error.png" width="100" height="100"></img> 
                                        <?php
                                    }
                                    ?>
                                </p>
                            </td>
                            <td>
                                <p align="center" id='p_fieldset_autenticacion'><?php echo $detalle_error; ?></p>
                            </td>
                        </tr>
                    </table>
                    <br>
                    <p align="center" id='p_fieldset_autenticacion'>Para una mejor navegación en el sitio se recomienda utilizar los navegadores</p>
                    <a href="http://www.google.com/intl/es-419/chrome/" target="_blank" title="Descargar Google Chrome"> <img src="template/imagenes/generales/chrome.png" width="60" height="60"></img></a> 
                    <a href="https://www.mozilla.org/es-ES/firefox/new/" target="_blank" title="Descargar Mozilla Firefox"> <img src="template/imagenes/generales/firefox.png" width="60" height="60"></img></a> 
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




