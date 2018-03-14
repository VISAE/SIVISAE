<?php

include_once './excel/PHPExcel.php';
/**
 * @function: generarReporte($titulo, $columnas, $data, $nombre_archivo, $descripcion)
 * @access: public
 * @description: Metodo generico para la elaboración de reportes en Excel
 * @param: String $titulo -> Encabezado del excel
 * @param: array $columnas -> Array con los Titulos de las columnas
 * @param: array $data -> Array con la informacion a mostrar en el Excel
 * @param: String $nombre_archivo -> Nombre del archivo
 * @param: String $descripcion -> Breve descripcion del reporte
 * @return: string $ruta_archivo -> Devuelve la ruta del archivo para que pueda ser descargado 
 * @author: Cristian Camilo Patiño
 */
function generarReporte($titulo, $columnas, $data, $nombre_archivo, $descripcion) {
    $todayh = getdate(); //monday week begin reconvert
$d = $todayh['mday'];
$mo = $todayh['mon'];
$y = $todayh['year'];
$h = $todayh['hours'];
$mi = $todayh['minutes'];

    $ruta_archivo = "../tmp/".$nombre_archivo." $d-$mo-$y $h$mi".".xlsx";
    $export = new PHPExcel();
    $export->
            getProperties()
            ->setCreator("UNAD - VISAE")
            ->setLastModifiedBy("UNAD - VISAE")
            ->setTitle($descripcion)
            ->setSubject($descripcion)
            ->setDescription($descripcion)
            ->setKeywords($descripcion)
            ->setCategory("reportes");
    $tituloReporte = $titulo;
    $titulosColumnas = $columnas;
    $cant_col = count($titulosColumnas);
    $export->setActiveSheetIndex(0)
            ->mergeCells('A1:' . chr(65+$cant_col).'1');
    $export->setActiveSheetIndex(0)
            ->setCellValue('A1', $tituloReporte); // Titulo del reporte

    $j = 0;
    for ($i = 65; $i <= 65 + $cant_col-1; $i++) {
        $col = chr($i);
        $export->setActiveSheetIndex(0)
                ->setCellValue($col . '3', $titulosColumnas[$j]);  //Titulo de las columnas
        $j++;
    }

    $i = 4; //Numero de fila donde se va a comenzar a rellenar
    foreach ($data as $fila) {
        $j = 0;
    for ($k = 65; $k <= 65 + $cant_col-1; $k++) {
        $col = chr($k);
        $export->setActiveSheetIndex(0)
                ->setCellValue($col . $i, ucwords($fila[$j]));  //Titulo de las columnas
        $j++;
    }
        $i++;
    }
    //Colocar ancho de las columnas de forma automática con base al contenido de cada una 
    for ($i = 65; $i <= 65 + $cant_col-1; $i++) {
        $export->setActiveSheetIndex(0)->getColumnDimension(chr($i))->setAutoSize(TRUE);
    }
// Se asigna el nombre a la hoja
    $export->getActiveSheet()->setTitle("Hoja");

// Se activa la hoja para que sea la que se muestre cuando el archivo se abre
    $export->setActiveSheetIndex(0);


    $crear = PHPExcel_IOFactory::createWriter($export, 'Excel2007');
    $crear->save($ruta_archivo);
    
    return str_replace("../","",$ruta_archivo);
}
