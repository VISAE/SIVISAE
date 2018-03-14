<head>
    <?php
    $browser = getenv("HTTP_USER_AGENT");
    include_once '../config/sivisae_class.php';
    if (preg_match("/MSIE/i", "$browser")) {
        //Navegadores no compatibles
        ?>
        <script language="JavaScript" type="text/JavaScript">
            window.location = "sivisae_notifica.php?e=X01";
        </script>

        <?php
    } else {
        //Navegadores compatibles
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

        <script type="text/javascript">
            function submitForm() {
                $("#spinner").show();
                $('#btn_submit').attr("disabled", true);
                var form = document.recordar_pass;
                var dataString = $(form).serialize();
                $.ajax({
                    type: 'POST',
                    url: 'src/recordar_contrasenaCB.php',
                    data: dataString,
                    success: function(data) {
                        $('#btn_submit').attr("disabled", false);
                        if (data === '0')
                        {
                            $("#spinner").hide();
                            $('#result').html('El usuario no existe');
                        } else
                        {
                            limpiaForm(form);
                            $('#result').html(data);
                            $("#spinner").hide();
                            setTimeout('redireccionar()', 2000);
                        }
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
                    // los selects le ponemos el indice a -
                    else if (tag === 'select')
                        this.selectedIndex = -1;
                });
            }

            function redireccionar() {
                window.location = "<?php echo RUTA_PPAL; ?>";
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
                            Recordar Contrase&ntilde;a
                        </h2>
                    </div>
                </div>
                </br>
                <div class="art-postcontent">

                    <div align="center">
                        <form id="cambio_pass" name="recordar_pass" method="post" onsubmit="return submitForm()" action="src/recordar_contrasenaCB.php">

                            <div class="art-postcontent">
                                <!--<div align="center">-->
                                <table width="50%">
                                    <tr>
                                        <td>
                                            <p><label class="input_fieldset_autenticacion" for="usuario_cambio">
                                                    Usuario:
                                                </label></p>
                                        </td>
                                        <td>
                                            <p><input minlength=6 maxlength="50" required="required" title="Por favor ingrese su usuario. El usuario debe ser mínimo de seis caracteres" autocomplete="off" class="input_fieldset_autenticacion" type="text" id="usuario" name="usuario" value=""/>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td colspan="2">
                                            <p><input id="btn_submit" name="btn_submit" class="submit_fieldset_autenticacion" type="submit" value="Recuperar" /></p>
                                            <div align="center" id="result"></div>
                                            <div id="spinner" align="center" style="display:none;">
                                                <img id="img-spinner" width="50" height="50" src="template/imagenes/generales/loading.gif" alt="Loading"/>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
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
?>

