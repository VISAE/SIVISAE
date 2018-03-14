<div align="right">
    <table>
        <tr>
            <td><?php if ($_SESSION['perfilid'] !== '0') { ?>
                <p id='p_autenticacion'> <?php echo $_SESSION["nom"]; ?> <strong>(<?php echo $_SESSION["login"]; ?>)</strong> || <?php echo $_SESSION["perfil"]; ?> || <?php echo $_SESSION["sede"]; ?></p>
                <?php } else { ?>
                    <p id='p_autenticacion'> <?php echo $_SESSION["nom"]; ?> <strong>(<?php echo $_SESSION["login"]; ?>)</strong> || <?php echo "GRADUADO"; ?></p>
                <?php } ?>
            </td>
            <td align="center">
                <img src="template/imagenes/generales/avatar.png" width="40" height="40"></img> 
            </td>
            <td><a href="pages/sivisae_home.php"><img src="template/imagenes/generales/home.png" width="25" height="25"></img></a></td>
            <td><a href="pages/sivisae_ayuda.php"><img src="template/imagenes/generales/ayuda.png" width="25" height="25"></img></a></td>
            <td><a href="pages/sivisae_acerca_de.php"><img src="template/imagenes/generales/acerca_de.png" width="25" height="25"></img></a></td>
            <td><a href="pages/sivisae_logout.php"><img src="template/imagenes/generales/salir.png" width="25" height="25"></img></a></td>
        </tr>
    </table>
</div>