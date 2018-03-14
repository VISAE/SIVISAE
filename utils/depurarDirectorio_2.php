<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    </head>

    <body>
        <?php
        $path = "corte_2";
        $total_ficheros = analizar_directorio($path);
        echo "Hay $total_ficheros ficheros en el directorio $path<br>";

        //------------------------------------------------------------- 
        function analizar_directorio($path) {
            $total_ficheros = 0;
            $dir = opendir($path);
            while ($elemento = readdir($dir)) {
                if ($elemento != "." && $elemento != "..") {
                    // Si es una carpeta
                    if (is_dir($path . "/" . $elemento)) {
                        // Muestro la carpeta
                        //echo("Procesando subdirectorio: " . $elemento . "<br>");
                        $total_ficheros += analizar_directorio($path . "/" . $elemento);
                        // Si es un fichero
                    } else {
                        $info = pathinfo($elemento);
                        $nuevo_nombre = $elemento;
                        // se reemplazan caracteres
                        $search = array('corte_2');
                        $replace = array('corte_1');
                        $nuevo_nombre = strtolower(str_replace($search, $replace, $nuevo_nombre));

                        //permisos
                        rename($path . "/" . $elemento, $path . "/" . $nuevo_nombre);
                        echo $nuevo_nombre . " <br> ";
                        echo $elemento . " <br>";
                        $total_ficheros++;
                        echo $total_ficheros . " <br><br>";
                    }
                }
            }
            return $total_ficheros;
        }
        ?>