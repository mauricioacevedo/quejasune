<?php
	session_start();
	require_once 'excel_reader2.php';
	require_once 'Phpexcel/Classes/PHPExcel/IOFactory.php';
	include_once('conexion.php');
	

	$name_file2 = basename($_FILES['userfile']['name']);
	$name_file = $_FILES['userfile']['tmp_name'];

	$pathy=getcwd();

	$uploadfile="$pathy/tmp/$name_file2";	

	if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)){
	   // echo "File is valid, and was successfully uploaded.\n";
        	echo "se cargo el archivo";
		$name_file=$uploadfile;
	}else{
	   	echo "Prolema con la carga del archivo: Archivo muy grande o problemas locales de disco.";
		return;
	}
	
       	$nombreForma="Quejas Criticas 2";
        $linkForma="./quejas_criticas_2.php";

        ?>

        <html><body>
        <? include 'header.php'; ?>


	<h3>Procesando archivo: <? echo $name_file2; ?></h3>
	

	<?
	$objReader = new PHPExcel_Reader_Excel5();
        //$objReader =PHPExcel_IOFactory::createReader($inputFileType); 
        $objReader->setLoadSheetsOnly( "Hoja1" );
        
	$objPHPExcel =$objReader->load($name_file);

	$objWorksheet = $objPHPExcel->getActiveSheet();

        $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
        $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'

        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g. 5

	//ciclo para validar que los campos del archivo que se trata de ingresar sean los que se esperan

	$arreglo = array("GRUPO", "N° SS", "PEDIDO", "DESCRIPCIÓN", "APERTURA", "ÁREA DE TRABAJO", "CONTRATISTA", "ESTADO", "# DÍAS", "EXPLICACION");
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

	//si llego hasta aca es porque el archivo tiene el formato correcto!

	$conexion_bd = getConnection();

	$sql="insert into quejas_criticas(grupo,numero_queja,pedido,descripcion,apertura,area_de_trabajo,contratista,estado,numero_dias,explicacion,id_file) values (";


	//echo "<a href='./carga.php?file=$name_file'>Continuar</a>&nbsp;<a href='./carga_actividades.php'>Cancelar</a>";


	//si hizo la carga con exito, ingreso informacion del archivo leido en la tabla..

	$sql3="insert into info_files(filename) values ('$name_file2')";
	$result = pg_query($sql3);

	$oid = pg_last_oid($result);

	//obtengo el identificador del ultimo registro ingresado, esto para generar la relacion entre las tablas de actividades y archivos

	$sql3="select id from info_files where oid=$oid";
	$result = pg_query($sql3);

	$rows=pg_numrows($result);

	if($rows>0){
		$id_file=pg_result($result,0,0);
	}else{
		echo "<br><h2>Ocurrio un error a nivel de base de datos: no se obtuvieron registros con el OID: $oid</h2>";
		reutrn;
	}

        //echo '<table border="1">' . "\n";
	$sql2=$sql;
	$separator="";


	//se comienza en la fila 2 porque se supone que el encabezado esta en la fila 1.

      for ($row = 2; $row <= $highestRow; ++$row) {
                //echo '<tr>' . "\n";
		$sql2=$sql;
		$separator="";
                for ($col = 0; $col < $longitud_encabezado; ++$col) {

			//if($col==($highestColumnIndex-6)){//encontro la columna de fecha
			//	$cell=$objWorksheet->getCellByColumnAndRow($col, $row);
		        //        $celli="".$cell->getColumn()."".$cell->getRow();
                	//	$objWorksheet->getStyle($celli)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2);
                	//	$date=$objWorksheet->getCellByColumnAndRow($col, $row)->getFormattedValue();
                	//	$objWorksheet->getStyle($celli)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME2);
                	//	$date=$date." ".$objWorksheet->getCellByColumnAndRow($col, $row)->getFormattedValue();
                	//	//echo "<td>".$date."</td>";
				
			//	$sql2=$sql2."$separator'$date'";
	
			//}else {
				$valor=$objWorksheet->getCellByColumnAndRow($col, $row)->getFormattedValue();
				$url=$objWorksheet->getCellByColumnAndRow($col, $row)->hasHyperlink();
				
				//$getCoordinate = $objWorksheet->getCellByColumnAndRow($col, $row)->getCoordinate();
				$value = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				//$fvalue= $objWorksheet->getCellByColumnAndRow($col, $row)->getHyperlink()->getUrl();
				//echo "coor: $getCoordinate, value: $value, fvalue: $fvalue";
				
				/*
				if($url==1){
					$url=$objWorksheet->getCellByColumnAndRow($col, $row)->getHyperlink()->getUrl();
					//$url2 = explode('\\', $url);
					//$contador=count($url2);
					//$toolt=$url2[$contador-1];
					echo "<br>tiene hipervinculo: $url ";
					$valor=$url;
					//esto se debe hacer porque el insert en la base de datos le quita un '\' del string
					$valor = str_replace("\\", "\\\\", $valor);
				}
				*/
				
				$valor=utf8_decode($valor);
				
				$array = array(".msg", ".pdf", ".jpg",".doc");
				$filed="0";
				for($i=0;$i<3;$i++){
					$valor2= strtolower($valor);
					$pos=strrpos($valor2, $array[$i]);
					if($pos===false){
					}else{
						$valor="\\\\\\\\vjurado7\\\\Radicados Siebel\\\\".$valor;
						$i=4;
						$filed="1";
						//echo "<br>$valor";
						break;
					}

				}
	                        //echo '<td>&nbsp;' . $valor . '</td>' . "\n";
				if($filed=="1"){//es una url
					$sql2=$sql2."$separator E'$valor'";
				}else{
					$sql2=$sql2."$separator'$valor'";
				}
				
        		//}
			$separator=",";
                }

                //echo '</tr>' . "\n";
		$sql2=$sql2.",$id_file)";
		$result = pg_query($sql2);
		//echo "$sql2 <br>";
        }
        //echo '</table>' . "\n";
	

	//en esta altura deberia hacerse la ejecucion de la aplicacion para el correo.
	//$id_file -- el id del archivo
	//$name_file  -- ruta actual del archivo

	//busco la contraseña para el envio de correo desde el buzon..
	$user=$_SESSION['user'];

        $sql="select pwd_correo from usuarios where login='$user'";

        $result = pg_query($sql);
        $rows=pg_numrows($result);
        $pwd_correo="NULL";
        if($rows>0){//usuario existe
                $pwd_correo=pg_result($result,0,0);

        }

	$cmd = "/usr/java/java/bin/java -jar /var/www/html/quejasune/java/QuejasUne.jar CorreoQuejasCriticas $id_file $name_file /var/www/html/quejasune/java/fileConfig.xml $user $pwd_correo";

	//$cmd="/var/www/html/quejasune/java/comando.sh $id_file $name_file /var/www/html/quejasune/java/fileConfig.xml";

	echo $cmd;

	$output = shell_exec($cmd);
	//echo "Salida del proceso en shell: $output";
	echo "<p><h3>Se hizo la carga del archivo $name_file2 con exito.</h3></p>";

?>
