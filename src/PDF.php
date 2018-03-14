<?php

require('./FPDF/fpdf.php');

class PDF extends FPDF {

    function getDia($timestamp = 0) {
        $timestamp = $timestamp == 0 ? time() : $timestamp;
        $dias = array('Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado');
        return $dias[date("w", $timestamp)];
    }

    function getMes($timestamp = 0) {
        $timestamp = $timestamp == 0 ? time() : $timestamp;
        $meses = array('', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
        return $meses[date("n", $timestamp)];
    }

    function Header() {
        $this->Image('../template/imagenes/unad/banner_visae.jpg', 50, 0);
        $this->Image('../template/imagenes/unad/logoUNAD2015.png', 0, 5, -200, -203);
        $this->Ln(30);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 7);
        $this->Image('../template/imagenes/unad/auditores_se.jpg', 75, 277, -600, -583);
        $this->Cell(0, 10, 'P' . chr(225) . 'gina ' . $this->PageNo(), 0, 0, 'R');
    }

    function Justify($text, $w, $h, $tipo) {
        if ($tipo !== 'z') {
            $this->SetFont("Arial", $tipo);
        } else {
            $this->SetFont("Arial", '');
        }
        $tab_paragraphe = explode("\n", $text);
        $nb_paragraphe = count($tab_paragraphe);
        $j = 0;

        while ($j < $nb_paragraphe) {

            $paragraphe = $tab_paragraphe[$j];
            $tab_mot = explode(' ', $paragraphe);
            $nb_mot = count($tab_mot);

            // Handle strings longer than paragraph width
            $k = 0;
            $l = 0;
            while ($k < $nb_mot) {

                $len_mot = strlen($tab_mot[$k]);
                if ($len_mot < ($w - 5)) {
                    $tab_mot2[$l] = $tab_mot[$k];
                    $l++;
                } else {
                    $m = 0;
                    $chaine_lettre = '';
                    while ($m < $len_mot) {

                        $lettre = substr($tab_mot[$k], $m, 1);
                        $len_chaine_lettre = $this->GetStringWidth($chaine_lettre . $lettre);

                        if ($len_chaine_lettre > ($w - 7)) {
                            $tab_mot2[$l] = $chaine_lettre . '-';
                            $chaine_lettre = $lettre;
                            $l++;
                        } else {
                            $chaine_lettre .= $lettre;
                        }
                        $m++;
                    }
                    if ($chaine_lettre) {
                        $tab_mot2[$l] = $chaine_lettre;
                        $l++;
                    }
                }
                $k++;
            }

            // Justified lines
            $nb_mot = count($tab_mot2);
            $i = 0;
            $ligne = '';
            while ($i < $nb_mot) {

                $mot = $tab_mot2[$i];
                $len_ligne = $this->GetStringWidth($ligne . ' ' . $mot);

                if ($len_ligne > ($w - 5)) {

                    $len_ligne = $this->GetStringWidth($ligne);
                    $nb_carac = strlen($ligne);
                    $ecart = (($w - 2) - $len_ligne) / $nb_carac;
                    $this->_out(sprintf('BT %.3F Tc ET', $ecart * $this->k));
                    $this->MultiCell($w, $h, $ligne);
                    $ligne = $mot;
                } else {

                    if ($ligne) {
                        $ligne .= ' ' . $mot;
                    } else {
                        $ligne = $mot;
                    }
                }
                $i++;
            }

            // Last line
            $this->_out('BT 0 Tc ET');
            $this->MultiCell($w, $h, $ligne);
            $tab_mot = '';
            $tab_mot2 = '';
            $j++;
        }
    }

}
