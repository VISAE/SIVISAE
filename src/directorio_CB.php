<?php
session_start();
include '../config/sivisae_class.php';
$sintilde = explode(',', SIN_TILDES);
$tildes = explode(',', TILDES);
$cedula = $_POST['documento_buscar'];
$tipo_per = $_POST['tipo_per'];

$consultar = new sivisae_consultas();

if ($tipo_per === "e") {
    $data_busqueda = $consultar->buscarEstudianteDirectorio($cedula);
    if (mysql_num_rows($data_busqueda) > 0) {
        $cont = 0;
        echo "
        <div style='height: 400px; overflow-y: scroll;'>";
        while ($row = mysql_fetch_array($data_busqueda)) {
            $cont = 1;
            $id_est = $row[0];
            $cedula_est = $row[1];
            $nombre_est = $row[2];
            $correo_est = $row[3];
            $institucional_est = $row[4];
            $skype_est = $row[5];
            $fecha_nacimiento_est = $row[6];
            $genero_est = $row[7];
            $estado_civil_est = $row[8];
            $telefono_est = $row[9];
            $centro_est = $row[10];


// Se crea la tabla de datos básicos

            if ($cont > 0) {
                echo "
        <br>
        <table id='tb_grilla' border='1' class='tg' style='undefined;table-layout: fixed; width=100%'>
				<thead>
					<tr>
						<th>DOCUMENTO</th>
						<th>NOMBRE</th>
						<th>CORREO</th>
					</tr>
                                </thead>
                                <tbody>
                                        <tr>
                                                <td>$cedula_est</td>
                                                <td>$nombre_est</td>
                                                <td>$correo_est</td>
                                        </tr>
                                </tbody>
                                <thead>
                                                <th>CORREO INSTITUCIONAL</th>
						<th>FECHA NACIMIENTO</th>
                                                <th>GÉNERO</th>
                                </thead>
                                <tbody>
                                        <tr>
                                                <td>$institucional_est</td>
                                                <td>$fecha_nacimiento_est</td>
                                                <td>$genero_est</td>
                                                     
                                        </tr>
				</tbody>
                                <thead>
                                                <th>ESTADO CIVIL</th>
                                                <th>TELÉFONO</th>
                                                <th>CENTRO</th>
                                </thead>
                                <tbody>
                                        <tr>
                                                <td>$estado_civil_est</td>
                                                <td>$telefono_est</td>
                                                <td>$centro_est</td>    
                                        </tr>
                                </tbody>
        </table>";

                $data_matriculas = $consultar->consultarMatriculasEstudianteDirectorio($id_est);

                echo "<br>
        <table id='tb_grilla' border='1' class='tg' style='undefined;table-layout: fixed; width=100%'>
				<thead>
					<tr>
						<th>PERIODO</th>
						<th>PROGRAMA</th>
						<th>TIPO ESTUDIANTE</th>
                                                <th>NO. DE MATRICULAS</th>
					</tr>
				</thead>
                        <tbody>
                    ";
                while ($row = mysql_fetch_array($data_matriculas)) {
                    $id_estudiante = $row[0];
                    $periodo_est = $row[1];
                    $programa_est = $row[2];
                    $tipo_estudiante = $row[3];
                    $numero_matriculas = $row[4];

                    // Nomenclatura del tipo estudiante
                    if ($tipo_estudiante == 'H') {
                        if ($numero_matriculas == 1) {
                            $tipo_estudiante = 'Homologación';
                        } else {
                            $tipo_estudiante = 'Antiguo';
                        }
                    } else {
                        $tipo_estudiante = 'Nuevo';
                    }

                    echo "<tr>"
                    . "<td>$periodo_est</td>"
                    . "<td>$programa_est</td>"
                    . "<td>$tipo_estudiante</td>"
                    . "<td>$numero_matriculas</td>"
                    . "</tr>";
                }

                echo "     </tbody>
                    </table>";

                $data_asignaciones = $consultar->consultarAsignacionesEstudianteDirectorio($id_est);

                echo "<br>
        <table id='tb_grilla' border='1' class='tg' style='undefined;table-layout: fixed; width=100%'>
				<thead>
					<tr>
						<th>AUDITOR</th>
						<th>CORREO</th>
						<th>TELÉFONO</th>
                                                <th>CELULAR</th>
                                                <th>CENTRO</th>
                                                <th>PERIODO ACADEMICO</th>
					</tr>
				</thead>
                        <tbody>
                    ";
                $contAsg = 0;
                while ($row = mysql_fetch_array($data_asignaciones)) {
                    $contAsg = 1;
                    $nombre_aud = $row[0];
                    $correo_aud = $row[1];
                    $telefono_aud = $row[2];
                    $celular_aud = $row[3];
                    $centro_aud = $row[4];
                    $periodo_aud = $row[5];

                    echo "<tr>"
                    . "<td>$nombre_aud</td>"
                    . "<td>$correo_aud</td>"
                    . "<td>$telefono_aud</td>"
                    . "<td>$celular_aud</td>"
                    . "<td>$centro_aud</td>"
                    . "<td>$periodo_aud</td>"
                    . "</tr>";
                }

                if ($contAsg == 0) {
                    echo "<tr>"
                    . "<td colspan='6'>El estudiante no tiene auditor Asignado</td>"
                    . "</tr>";
                }

                echo "     </tbody>
                    </table> 
                    <br>
                    <h2 style='BORDER-BOTTOM: #ccc 1px solid; MARGIN: 0px 0px 0.5em; COLOR: #000;'></h2>";
            } else {
                echo 'No existen coincidencias para el documento ingresado';
            }
        }
        echo "</div>";
    } else {
        echo "No se encontraron coincidencias.";
    }
} else {
    $tutores = $consultar->buscarFuncDirectorio($cedula, $tipo_per);
    $tp = array("t"=>"E-MMEDIADOR(es)", "a"=>"AUDITOR(es)", "c"=>"CONSEJERO(s)");
    if (mysql_num_rows($tutores) > 0) {
        echo "
            <div style='height: 300px; overflow-y: scroll;'>
            <br>
            <table  class='tg' style='table-layout: fixed; width:100%'>
                <tr>
                    <th colspan='3'>".$tp[$tipo_per]." ENCONTRADO(s)</th>
                </tr>
                <tr>
                    <th>Nombre</th>
                    <th>Teléfono</th>
                    <th>Correo</th>
                </tr>
            ";
        while ($not = mysql_fetch_array($tutores)) {
            $nombre = ucwords(preg_replace($sintilde, $tildes, $not[0]));
            $telf = $not[1];
            $mail = $not[2];
            echo "
                <tr>
                    <td align='center'>$nombre</td>
                    <td align='center'>$telf</td>
                    <td align='center'>$mail</td>
                </tr>";
        }
        echo "

            </table>";
    } else {
        echo "No se encontraron coincidencias.";
    }
}
?>