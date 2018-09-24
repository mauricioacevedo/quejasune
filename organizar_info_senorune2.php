<?php
	session_start();
	require_once 'excel_reader2.php';
	require_once 'Phpexcel/Classes/PHPExcel/IOFactory.php';
	include_once('conexion.php');

        function latin1($txt) {
                //$encoding = mb_detect_encoding($txt, 'ASCII,UTF-8,ISO-8859-1');
                //if ($encoding == "UTF-8") {
                        $txt = utf8_decode($txt);
                //}
                return $txt;
        }


	

	$name_file2 = basename($_FILES['userfile']['name']);
	$name_file = $_FILES['userfile']['tmp_name'];

	$pathy=getcwd();

	$uploadfile="$pathy/tmp/$name_file2";	

	if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)){
	   // echo "File is valid, and was successfully uploaded.\n";
        	echo "se cargo el archivo";
		$name_file=$uploadfile;
	}else{
	   	echo "Problema con la carga del archivo: Archivo muy grande o problemas locales de disco.";
		return;
	}
	
       	$nombreForma="Organizar Informacion 2";
        $linkForma="./organizar_info_senorune2.php";

        ?>

        <html><body>
        <? include 'header.php'; ?>


	<h3>Procesando archivo: <? echo $name_file2; ?></h3>
	

	<?
	$objReader = new PHPExcel_Reader_Excel5();
        //$objReader =PHPExcel_IOFactory::createReader($inputFileType); 
        $objReader->setLoadSheetsOnly("GO");
        
	$objPHPExcel =$objReader->load($name_file);

	$objWorksheet = $objPHPExcel->getActiveSheet();

        $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
        $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'

        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g. 5

	//ciclo para validar que los campos del archivo que se trata de ingresar sean los que se esperan

	$arreglo = array("Nº de SS","IMPUTABILIDAD","SRUNE","Estado","Tipo","Producto","Causa","Subcausa","Cuenta","Oficina","Ciudad de Servicio","Apertura","Cerrado","Compromiso","Grupo asignación inicial","Grupo","Descripción","Fuente","Pedido","Segmento","Oferta Comercial","Contador Reapertura","Imputabilidad","Resultado","Nivel de satisfacción","Propietario","Dependencia","Fecha Respuesta","Fecha Radicado","Departamento queja","Departamento de Servicio","Dirección de servicio","Idetificación cuenta","Radicado","Fecha de cambio de estado","Fecha envío citación","","Cerrado por","Fecha atención","Última modificación","Fecha fijación edicto","Fecha desfijación edicto","Certimail","Solución","Asignado","Numero CUN","Superintendencia","Nivel Reincidencia");
	echo "<br>";
	$i_array=0;

	$longitud_encabezado=count($arreglo);

	$columna_ignorar=-1;
	
	for ($col = 0; $col < $longitud_encabezado; ++$col) {
                        $valorColumna=$objWorksheet->getCellByColumnAndRow($col, 1)->getFormattedValue();
			$valorArray=$arreglo[$i_array];
			
			if($valorColumna==$valorArray){//columna correcta, continuo
				$i_array++;
				continue;
			}else{//nombre de columna incorrecto, parar e informar..
				echo "valor columna: ,$valorColumna, valor array: ,$valorArray,<br>";
				echo "El archivo $name_file2 no corresponde al documento esperado, revisar las columnas que deben tener este encabezado:";
				echo "<br>".$arreglo."<br>";
				return;
			}
			//echo "valor array: $valorColumna, valor array: $valorArray<br>";
			$i_array++;
        }	


        $filename="./documentos/documento-senor-UNE.xls";
        $fh = fopen($filename, 'w') or die("can't open file");
        fclose($fh);

        if (file_exists($filename)) {//borro el archivo
                unlink($filename);
        }

        $phpExcel = new PHPExcel();
        $sheet = $phpExcel->getActiveSheet();
        $sheet->setTitle("Documento Organizado Senor UNE");

        $sheet->setCellValue("A1", "Grupo");
        $sheet->setCellValue("B1", "Nº de SS");
        $sheet->setCellValue("C1", "Pedido");
        $sheet->setCellValue("D1", "Descripción");
        $sheet->setCellValue("E1", "Cuenta");
        $sheet->setCellValue("F1", "Idetificación cuenta");
        $sheet->setCellValue("G1", "Causa");
        $sheet->setCellValue("H1", "Subcausa");
	$sheet->setCellValue("I1", "Nº de teléfono del trabajo");
        $sheet->setCellValue("J1", "Apertura");
        $sheet->setCellValue("K1", "Ciudad de Servicio");
        $sheet->setCellValue("L1", "Dirección de servicio");
        $sheet->setCellValue("M1", "Estado");
        $sheet->setCellValue("N1", "Departamento de Servicio");
        $sheet->setCellValue("O1", "Departamento queja");
        $sheet->setCellValue("P1", "Contador Reapertura");
        $sheet->setCellValue("Q1", "Segmento Siebel");
        $sheet->setCellValue("R1", "Fecha Entrega Trabajo");

	//se comienza en la fila 2 porque se supone que el encabezado esta en la fila 1
      
	for ($row = 2; $row <= $highestRow; $row++) {
                //echo '<tr>' . "\n";
		$sql2=$sql;
		$separator="";
		
		$final_header_array= array("19","0","65","4","7","8","6","3","NUMERO DE TELEFONO","9","61","62","13","63","53","69","68","FECHA ENTREGA TRABAJO VACIO");
		$count_final_header=count($final_header_array);
		echo "<br>iniciando ciclo central --->$count_final_header";

		
		$sheet->setCellValue("A".($row),utf8_encode($objWorksheet->getCellByColumnAndRow(15, $row)->getFormattedValue()));
                $sheet->setCellValue("B".($row),utf8_encode($objWorksheet->getCellByColumnAndRow(0, $row)->getFormattedValue()));
                $sheet->setCellValue("C".($row),utf8_encode($objWorksheet->getCellByColumnAndRow(18, $row)->getFormattedValue()));
                $sheet->setCellValue("D".($row),utf8_encode($objWorksheet->getCellByColumnAndRow(16, $row)->getFormattedValue()));
                $sheet->setCellValue("E".($row),utf8_encode($objWorksheet->getCellByColumnAndRow(8, $row)->getFormattedValue()));
                $sheet->setCellValue("F".($row),utf8_encode($objWorksheet->getCellByColumnAndRow(32, $row)->getFormattedValue()));
                $sheet->setCellValue("G".($row),utf8_encode($objWorksheet->getCellByColumnAndRow(6, $row)->getFormattedValue()));
                $sheet->setCellValue("H".($row),utf8_encode($objWorksheet->getCellByColumnAndRow(7, $row)->getFormattedValue()));
                $sheet->setCellValue("I".($row),utf8_encode(" "));
                $sheet->setCellValue("J".($row),utf8_encode($objWorksheet->getCellByColumnAndRow(11, $row)->getFormattedValue()));
                $sheet->setCellValue("K".($row),utf8_encode($objWorksheet->getCellByColumnAndRow(10, $row)->getFormattedValue()));
                $sheet->setCellValue("L".($row),utf8_encode($objWorksheet->getCellByColumnAndRow(31, $row)->getFormattedValue()));
                $sheet->setCellValue("M".($row),utf8_encode($objWorksheet->getCellByColumnAndRow(3, $row)->getFormattedValue()));
                $sheet->setCellValue("N".($row),utf8_encode($objWorksheet->getCellByColumnAndRow(30, $row)->getFormattedValue()));
                $sheet->setCellValue("O".($row),utf8_encode($objWorksheet->getCellByColumnAndRow(29, $row)->getFormattedValue()));
                $sheet->setCellValue("P".($row),utf8_encode($objWorksheet->getCellByColumnAndRow(21, $row)->getFormattedValue()));
                $sheet->setCellValue("Q".($row),utf8_encode($objWorksheet->getCellByColumnAndRow(19, $row)->getFormattedValue()));
                $sheet->setCellValue("R".($row),utf8_encode(" "));


        }
        //echo '</table>' . "\n";
	
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Cache-Control: max-age=0");



        $objWriter = PHPExcel_IOFactory::createWriter($phpExcel, "Excel5");
        $objWriter->save($filename);
        header('Location: '.$filename);

	echo "<p><h3>Se hizo la carga del archivo $name_file2 con exito.</h3></p>";

?>
