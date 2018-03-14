<?php
session_start();

/*
 * 
 *   @author Ing. Andres Mendez
 * Clase para envio y recepcion de notificaciones
 */

include_once '../config/sivisae_class.php';
include './paginador.php';
$consulta = new sivisae_consultas();
$sintilde = explode(',', SIN_TILDES);
$tildes = explode(',', TILDES);
if (isset($_SESSION['usuarioid'])) {
    $usr_id = $_SESSION['usuarioid'];


    $accion = $_POST['accion'];

    if ($accion === "enviar") {
        $usr_dest_id = $_POST['func_not'];
        $tipo = $_POST['tp_notif'];
        $notif = $_POST['txt_notif'];

        $res = $consulta->crearNotificacion($usr_id, $usr_dest_id, $notif, $tipo);
        echo $res;
    }
    if ($accion === "contar") {
        $res = $consulta->getCantNotificaciones($usr_id);
        $dev;
        if ($res !== $_SESSION['notificaciones'] && $res > $_SESSION['notificaciones']) {
            if ($res > 0) {
                if ($res === 1) {
                    $dev = array(
                        "cant" => $res,
                        "msj" => "¡Tiene " . $res . " notificación sin leer!"
                    );
//                echo "$res||¡Tiene ".$res." notificación sin leer!"; 
                } else {
                    $dev = array(
                        "cant" => $res,
                        "msj" => "¡Tiene " . $res . " notificaciones sin leer!"
                    );
//                echo "$res||¡Tiene ".$res." notificaciones sin leer!"; 
                }
            } else {
                $dev = array(
                    "cant" => "-1",
                    "msj" => $res
                );
//            echo "--1||$res"; 
            }
            $_SESSION['notificaciones'] = $res;
        } else {
            $dev = array(
                "cant" => "-1",
                "msj" => $res
            );
//        echo "--1||$res||".$_SESSION['notificaciones'];
            $_SESSION['notificaciones'] = $res;
        }
        echo json_encode($dev);
    }

    if ($accion === "lista") {
        if (isset($_POST["page"])) {
            $page_number = filter_var($_POST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
            if (!is_numeric($page_number)) {
                die('N' . chr(250) . 'mero de p' . chr(225) . 'gina incorrecto!');
            } //incase of invalid page number
        } else {
            $page_number = 1; //Si no hay numero de pagina coloca 1 por defecto
        }
        $item_per_page = 10;


//Obtengo la posicion en que debe arrancar la siguiente consulta.
        $page_position = (($page_number - 1) * $item_per_page);

        $res = $consulta->getNotificaciones($usr_id, $page_position, $item_per_page);
        $get_total_rows = $consulta->getTotalNotificaciones($usr_id);

        echo "
        <table  class='tg' style='table-layout: fixed; width:100%'>
            <tr>
                <th>Envia</th>
                <th>Tipo</th>
                <th>Fecha</th>
                <th>Estado</th>
                <th></th>
            </tr>";
        while ($not = mysql_fetch_array($res)) {
            $quien = ucwords($not[0]);
            $tipo = ucfirst(preg_replace($sintilde, $tildes, $not[1]));
            $fecha = $not[2];
            $estado = $not[3];
            $not_id = $not[4];
            echo "
            <tr>
                <td align='center'>$quien</td>
                <td align='center'>$tipo</td>
                <td align='center'>$fecha</td>
                <td align='center'>$estado</td>
                <td align='center'><a class='tipo botones' not='$not_id' onclick='verNotif(\"$not_id\")'>Ver</a></td>
            </tr>";
        }
        $total_pages = ceil($get_total_rows / $item_per_page);
        echo "
           
        </table>";
        echo '<div align="center"><br><br>'
        . '<table><tr><td>Mostrando ' . $item_per_page . ' registros de ' . $get_total_rows . ' encontrados.</td>'
        . '<td>';
        echo paginate_function($item_per_page, $page_number, $get_total_rows, $total_pages);
        echo '</td>'
        . '<td> en ' . $total_pages . ' p&aacute;ginas.</td></tr></table></div>'
        . '<div id="oculto">'
        . '<input type="hidden" id="est_hid" name="est_hid[]"/>'
        . '</div>';
    }

    if ($accion === 'ver') {
        $res = $consulta->getNotificacion($_POST['notif_id']);
        $not = mysql_fetch_array($res);
        echo ucfirst(preg_replace($sintilde, $tildes, $not[0])) . "|" . $not[1] . "|" . ucfirst(preg_replace($sintilde, $tildes, $not[2])) . "|" . $not[3];
    }
    if ($accion === 'leer') {
        $res = $consulta->leerNotif($_POST['notif_id']);
        if ($res) {
            $dev = array(
                "res" => "1"
            );
            echo json_encode($dev);
        }
//    echo ucfirst(preg_replace($sintilde, $tildes, $not[0]))."|".$not[1]."|".ucfirst(preg_replace($sintilde, $tildes, $not[2]))."|".$not[3];
    }


    $consulta->destruir();
}