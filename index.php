<?php
if (isset($_GET["param"])) {
    $mantenimiento = 0;
} else {
    $mantenimiento = 1;
}

$mantenimiento = 0;

if ($mantenimiento == 1) {
    ?>
    <html>
        <body bgcolor="#000000">
            <div align="center">
                <img src="http://sivisae.unad.edu.co/template/imagenes/generales/mantenimiento_sivisae.jpg" width="80%" height="90%"/>
            </div>
            <div align="center">
                </br>
                <font color="#FFFFFF">ESTE SITIO ESTÁ EN MANTENIMIENTO </br></font>

            </div>
        </body>
    </html>
    <?php
} else {
    ?>
    <script language="JavaScript" type="text/JavaScript">
        window.location = "pages/sivisae_login.php";
        //window.location = "pages/sivisae_login_mantenimiento.php";
    </script>
    <?php
}
?>

