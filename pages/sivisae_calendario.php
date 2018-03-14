<?php
session_start();
include_once '../config/sivisae_class.php';
$consulta = new sivisae_consultas();
//Se consultan los eventos
$resEvento = $consulta->consultarEventos();
?>

<!DOCTYPE html>
<html>
    <head lang="en">
        <meta charset="UTF-8">
        <script type="text/javascript" src="../js/calendario/jquery-1.11.0.min.js"></script>
        <script type="text/javascript" src="../js/calendario/jquery.e-calendar.js"></script>
        <script type="text/javascript" src="../js/calendario/index.js"></script>
        <link rel="stylesheet" href="../template/calendario/jquery.e-calendar.css"/>


        <?php
        //Se arma la data del calendario
        $script1 = "
            <script>
            $.fn.eCalendar.defaults = {
                            weekDays: ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'],
                            months: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                            textArrows: {previous: '<', next: '>'},
                            eventTitle: 'Agenda de Eventos',
                            url: '',
                            events: [";
        $cont = 0;
        $script2 = '';
        while ($fila = mysql_fetch_array($resEvento)) {
            if ($cont != 0) {
                $script2 = $script2 . ' , ';
            }
            $script2 = $script2 . "{title: '" . $fila[1] . "', description: '" . $fila[2] . "', datetime: new Date(" . $fila[3] . ", " . $fila[4] . ", " . $fila[5] . ", " . $fila[6] . ")}";
            $cont = 1;
        }
        $script1 = $script1 . $script2;
        $script1 = $script1 . " ] }; </script>";

        echo $script1;
        ?>

    </head>
    <body>
        <div id="calendar"></div>
    </body>
</html>
<?php
$consulta->destruir();
?>