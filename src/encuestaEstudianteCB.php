<?php

/**
 * Description of encuestaEstudianteCB
 *
 * @author Andrés Méndez
 */
session_start();
include_once '../config/sivisae_class.php';
$consulta = new sivisae_consultas();
$sintilde = explode(',', SIN_TILDES);
$tildes = explode(',', TILDES);

$doc_estudiante = $_POST['est_id'];
$pa_estudiante = $_POST['pa_id'];


if (isset($doc_estudiante) && isset($pa_estudiante)) {
// Se buscan la ultima evaluacion realizada por el estudiante
    $encuesta = $consulta->evaluacionInducccionEstudiante($doc_estudiante, $pa_estudiante);

    if (count($encuesta) <= 0) {
        echo 'No existen resultados de encuesta para este estudiante';
    } else {
        echo "
        <br><br>
        <table id='tb_estudiantes' class='tg' style='table-layout: fixed; width=300px'>
				<thead>
                                        <tr>
                                                <th>No.</th>
                                                <th>PREGUNTA</th>
						<th>RESPUESTA</th>
					</tr>
				</thead>
                        <tbody>
                    ";
        $cont = 1;

        $observaciones = "Sin observaciones";
        $sugerencias = "Sin sugerencias";

        while ($row = mysql_fetch_array($encuesta)) {
            $pregunta = ucwords(preg_replace($sintilde, $tildes, $row[6]));
            $respuesta = ucwords(preg_replace($sintilde, $tildes, $row[7]));
            $fecha = ucwords(preg_replace($sintilde, $tildes, $row[1]));

            if ($cont == 1) {
                $observaciones = (ucwords(preg_replace($sintilde, $tildes, $row[3])) <> "") ? ucwords(preg_replace($sintilde, $tildes, $row[3])) : 'Sin observaciones';
                $sugerencias = (ucwords(preg_replace($sintilde, $tildes, $row[4])) <> "") ? ucwords(preg_replace($sintilde, $tildes, $row[4])) : 'Sin sugerencias';
            }


            echo "<tr>"
            . "<td>$cont</td>"
            . "<td>$pregunta</td>"
            . "<td>$respuesta</td>";
            $cont++;
        }

        echo "     </tbody>
                    </table>";

        if ($cont == 1) {
            echo "<strong>No hay precargada información sobre la encuesta de este estudiante.</strong><br>";
        }

        $mensaje = (date('G') < 12) ? 'Buenos días' : 'Buenas tardes';
        
        echo "<strong>Observaciones:</strong> " . $observaciones . "<br>";
        echo "<strong>Sugerencias:</strong> " . $sugerencias;
    }
}
$consulta->destruir();
?>


