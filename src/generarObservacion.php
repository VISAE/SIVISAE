<?php

include_once './PDF.php';
include_once '../config/sivisae_class.php';

//if (isset($_POST['acc'])) {
//    $segt = $_POST['seg_aud'];
//    $tp = $_POST['tp'];
//
//    echo crearObsrAcaPDF($segt, $tp);
//}

function crearObsrAcaPDF($seg_aud, $tipo) {
    $sintilde = explode(',', SIN_TILDES);
    $tildes = explode(',', TILDES);
    $consulta = new sivisae_consultas();
    $inf_aud = mysql_fetch_array($consulta->infObservacionAca($seg_aud, $tipo));
    $mpio = utf8_decode(ucwords($inf_aud[0]));
    $tutor = utf8_decode(ucwords($inf_aud[1]));
    $curso = utf8_decode(ucwords(preg_replace($sintilde, $tildes, ucwords($inf_aud[2]))));
    $t_prog = utf8_decode(ucwords($inf_aud[3]));
    $auditor = utf8_decode(ucwords($inf_aud[4]));
    $cead = ucwords($inf_aud[5]);
    $cedula_est = $inf_aud[6];
    $nom_est = ucwords($inf_aud[7]);
    $cod_curso = $inf_aud[8];
    $seg_id = $inf_aud[9];
    $programa = $inf_aud[10];


//    $obs_gen = $consulta->observacionesAcad($seg_aud, 'g');
    /*     * $acuerdo = " \"contribuir con el desarrollo "
      . "humano integral y el mejoramiento de la calidad de vida de aspirantes, estudiantes y egresados mediante "
      . "la implementación de programas, proyectos, estrategias, servicios y acciones de monitoreo, control y evaluación "
      . "del impacto para favorecer su permanencia, bienestar y satisfacción en la universidad a lo largo de su "
      . "ciclo de vida formativo\". ";* */
    $acuerdo = "";

    $todayh = getdate(); //monday week begin reconvert
    $d = $todayh['mday'];
    $m = $todayh['mon'];
    $y = $todayh['year'];
    $saludo = $tipo === 'e' ? "Apreciado Estudiante" : "Apreciado E-mediador";
    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pdf->AddPage();

    $pdf->SetFont('Arial', '', 10);
    //$pdf->Cell(0, 5, $mpio . ", " . utf8_decode($pdf->getDia()) . " $d de " . $pdf->getMes() . " de $y.", 0, 1);
    //se deja centro del auditor
    $pdf->Cell(0, 5, utf8_decode($cead) . ", " . utf8_decode($pdf->getDia()) . " $d de " . $pdf->getMes() . " de $y.", 0, 1);
    $pdf->Ln(10);
    $pdf->Cell(0, 5, $saludo, 0, 1);
    $pdf->SetFont('Arial', 'B', 13);
    $pdf->Cell(0, 5, $tipo === 'e' ? utf8_decode($nom_est) : utf8_decode($tutor), 0, 1);

    $pdf->SetFont('Arial', '', 10);
    if ($tipo === 't') {
        $pdf->Cell(0, 5, $curso, 0, 1);
    } else {
        $pdf->Cell(0, 5, $t_prog, 0, 1);
        $pdf->Cell(0, 5, utf8_decode(ucfirst(preg_replace($sintilde, $tildes, $programa))), 0, 1);
    }

    $pdf->Ln(10);
    $pdf->Cell(0, 3, utf8_decode('Asunto: Observación académica'), 0, 1);
    $pdf->Ln(10);
    $pdf->Cell(180, 5, utf8_decode('Reciba un cordial saludo, deseándole bienestar y éxitos en todas sus actividades.'), 0, 1);
    $pdf->Ln(3);
//    $pdf->MultiCell(180, 5, utf8_decode('A continuación relaciono observación evidenciada en el proceso de monitoreo de la actividad en plataforma por parte de la Auditoria de Servicios a Estudiantes.'), 0, 'J');
    $pdf->MultiCell(180, 5, utf8_decode('Para la Vicerrectoría de Servicios, Aspirantes, Estudiantes y Egresados es muy importante contribuir con el desarrollo humano integral y el mejoramiento de la calidad del proceso formativo; por lo anterior me permito relacionar observación evidenciada en el proceso de monitoreo de su actividad en plataforma por parte de la Auditoria de Servicios a Estudiantes.'), 0, 'J');
    $pdf->Ln(3);
//    $pdf->MultiCell(180, 5, utf8_decode("En conformidad con el Acuerdo 037 de 2012 del Consejo Superior Universitario, con el cual se crea la Vicerrectoría de Servicios a Aspirantes, Estudiantes y Egresados para: "), 0, 'J');
//    $pdf->Ln(2);
//    $pdf->cMargin = 25;
//    $pdf->SetFont("Arial", 'I');
//    $pdf->MultiCell(180, 5, utf8_decode($acuerdo), 0, 'J');
//    $pdf->cMargin = 0;
//    $pdf->SetFont("Arial", '');
//    $pdf->Ln(2);
//    $pdf->MultiCell(180, 5, utf8_decode("En  cumplimiento de lo anterior se ha evidenciado que: "), 0, 'J');
    $pdf->Ln(5);
    $pdf->cMargin = 10;

    if ($tipo !== 'e') {
        $observaciones = $consulta->observacionesAcad($seg_aud, $tipo);
        while ($row = mysql_fetch_array($observaciones)) {
            $pdf->SetFont("Arial", 'B');
            $pdf->MultiCell(180, 5, utf8_decode(preg_replace($sintilde, $tildes, $row[2])) . ': ', 0, 'J');
            $pdf->SetFont("Arial", '');
            $pdf->MultiCell(180, 5, utf8_decode(preg_replace($sintilde, $tildes, $row[3])) . '', 0, 'J');
            $pdf->Ln(2);
        }
    } else {
        $observaciones = $consulta->observacionesAcadEst($seg_aud);
        foreach ($observaciones as $key => $value) {
            $pdf->cMargin = 10;
//            echo "titulo: $key \n";
            if (!is_int($key)) {
                $pdf->SetFont("Arial", 'B');
                $pdf->MultiCell(180, 5, utf8_decode(preg_replace($sintilde, $tildes, $key)) . ': ', 0, 'J');
            }
            if (count($value) > 1) {
//                echo "descrp: ".$value[0]." \n";
                $pdf->SetFont("Arial", '');
                $pdf->MultiCell(180, 5, utf8_decode(preg_replace($sintilde, $tildes, $value[0])) . '', 0, 'J');
                $pdf->Ln(2);
                $pdf->MultiCell(180, 5, "Curso(s): ", 0, 'J');
                $pdf->cMargin = 15;
                for ($i = 1; count($value) > $i; $i++) {
//                    echo "curso: ".$value[$i]." \n";
//                    $pdf->SetFont("Arial", '');
//                    $pdf->Cell(0, 5, "Curso: ".utf8_decode($value[$i]), 0, 1);
                    $pdf->SetFont("Arial", 'B');
                    $pdf->MultiCell(180, 5, utf8_decode(ucfirst(preg_replace($sintilde, $tildes, $value[$i]))), 0, 'J');
//                    $pdf->Ln();
                }
            }
            $pdf->Ln(3);
        }
//        while ($row = mysql_fetch_array($observaciones)) {
//            $pdf->SetFont("Arial", 'B');
//            $pdf->MultiCell(180, 5, utf8_decode(preg_replace($sintilde, $tildes, $row[2])) . ': ', 0, 'J');
//            $pdf->SetFont("Arial", '');
//            $pdf->MultiCell(180, 5, utf8_decode(preg_replace($sintilde, $tildes, $row[3])) . '', 0, 'J');
//            $pdf->Ln(2);
//        }
    }
//    if (mysql_num_rows($obs_gen) > 0) {
//        $pdf->Ln(10);
//        $pdf->cMargin = 0;
//        $pdf->SetFont("Arial", 'B');
//        $pdf->MultiCell(180, 5, 'Comentario Final del Auditor: ', 0, 'J');
//        $pdf->cMargin = 10;
//        while ($row = mysql_fetch_array($obs_gen)) {
//            $pdf->SetFont("Arial", 'B');
//            $pdf->MultiCell(180, 5, $row[0] . ': ', 0, 'J');
//            $pdf->SetFont("Arial", '');
//            $pdf->MultiCell(180, 5, utf8_decode(preg_replace($sintilde, $tildes, $row[1])) . '', 0, 'J');
//            $pdf->Ln(2);
//        }
//    }
    $pdf->cMargin = 10;
    $pdf->SetFont("Arial", '');
    $consulta->destruir();
    //$pdf->AddPage();
    //$pdf->MultiCell(180, 5, utf8_decode("En conformidad con el Acuerdo 037 de 2012 del Consejo Superior Universitario, con el cual se crea la Vicerrectoría de Servicios a Aspirantes, Estudiantes y Egresados para: "), 0, 'J');
    $pdf->Ln(3);
    $pdf->cMargin = 25;
    $pdf->SetFont("Arial", 'I');
    $pdf->MultiCell(180, 5, utf8_decode($acuerdo), 0, 'J');
    $pdf->SetFont("Arial", '');
    $pdf->Ln(3);
    $pdf->cMargin = 0;
    $pdf->Cell(0, 5, 'Cordialmente, ', 0, 1);
    $pdf->Ln(3);
    $pdf->SetFont('Arial', 'B', 13);
    $pdf->Cell(0, 5, $auditor, 0, 1);

    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 5, 'Auditor de Servicios a los Estudiantes', 0, 1);
    $pdf->Cell(0, 5, utf8_decode('Vicerrectoría de Servicios a Aspirantes, Estudiantes y Egresados - VISAE -'), 0, 3);
    $pdf->Cell(0, 5, utf8_decode('Universidad Nacional Abierta y a Distancia - UNAD -'), 0, 1);
    $pdf->Cell(0, 5, "CEAD: " . utf8_decode($cead), 0, 1);
//---FIN FIRMA AUDITOR---//
    $persona = "";
    if ($tipo === 'e') {
        $persona = "Est";
    } else {
        $persona = "Emed";
    }

    $pdf->Output("../tmp/OA_$cedula_est" . "_$cod_curso" . "_$y$m$d" . "_S$seg_id" . "_$persona.pdf", 'F'); //Salida al navegador
    $ruta = RUTA_PPAL . "tmp/OA_$cedula_est" . "_$cod_curso" . "_$y$m$d" . "_S$seg_id" . "_$persona.pdf";
    return $ruta; //$pdf->getDia()." - ".$pdf->getMes();
//echo $ruta;
}
