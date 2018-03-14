<div id="cssmenu" class="align-center">
    <ul>
        <?php
        $usuarioid = $_SESSION['usuarioid'];
        if ($_SESSION['perfilid'] !== '0') {
            $menu = $consulta->menu($usuarioid);
            while ($row = mysql_fetch_array($menu)) {
                $menuid = $row[0];
                $descripcion = $row[1];

                echo "<li class='has-sub' ><a href='#'><span>$descripcion</span></a>"
                . "<ul>";
                $opciones = $consulta->opciones($usuarioid, $menuid);
                while ($row1 = mysql_fetch_array($opciones)) {
                    $opcion = $row1[0];
                    $url = $row1[1];
                    $id_opcion = $row1[2];

                    if ($id_opcion == 5) {
                        echo "<li class='last' ><a href='" . RUTA_PPAL . $url . "&op=" . $id_opcion . "'><span >$opcion</span></a></li>";
                    } else {
                        echo "<li class='last' ><a href='" . RUTA_PPAL . $url . "?op=" . $id_opcion . "'><span >$opcion</span></a></li>";
                    }
                }
                echo '</ul></li>';
            }
        }
        ?>
    </ul>
</div>

