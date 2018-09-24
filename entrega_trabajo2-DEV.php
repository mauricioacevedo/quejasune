<?php
	session_start();
	require_once 'Phpexcel/Classes/PHPExcel/IOFactory.php';
	require_once 'Phpexcel/Classes/PHPExcel.php';

	include_once('conexion.php');
	//include("excelwriter.inc.php");


	function latin1($txt) {
		//$encoding = mb_detect_encoding($txt, 'ASCII,UTF-8,ISO-8859-1');
		//if ($encoding == "UTF-8") {
			$txt = utf8_decode($txt);
		//}
 		return $txt;
	}
	
	//echo latin1("hiyaaaa");	

	$name_file2 = basename($_FILES['userfile']['name']);
	$name_file = $_FILES['userfile']['tmp_name'];

	$pathy=getcwd();

	$uploadfile="$pathy/tmp/$name_file2";	

	if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)){
	   // echo "File is valid, and was successfully uploaded.\n";
        	//echo "se cargo el archivo";
		$name_file=$uploadfile;
	}else{
	   	echo "Problema con la carga del archivo: Archivo muy grande o problemas locales de disco.";
		$name_file="/var/www/html/quejasune/tmp/ENVIO_TRABAJO.xls";
		//return;
	}
	
	//$name_file="/var/www/html/quejasune/tmp/plantilla-general.xls";

        $nombreForma="Entrega Trabajo 2";
        $linkForma="./entrega_trabajo2.php";

	?>
	
	<html><body>
        <? include 'header.php'; ?>

	<h3>Procesando archivo: <? echo $name_file2;?></h3>

	<!--el programa deja de funcionar si se le mete un archivo de office 2007 (xlsx)-->
	
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

	$arreglo = array("Grupo","Nº de SS y/o Radicado","Pedido","Descripción y/o Hipervínculo","Cuenta","Idetificación cuenta","Causa","Subcausa","Apertura","Fecha Radicado","Dirección de servicio","Estado","Departamento queja","Contador Reapertura","Segmento Siebel","Fecha Entrega Trabajo","Area_trabajo","Contratista","Concepto_pedido","Direccion","Cola","Fuente","Sistema de Información","Revisor","Según Ingreso","Fecha inicial Entrega","Semana","Mes","AREA-REGIONAL");

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
				echo "<br>Formato de columnas correcto:<br>".$arreglo."<br>";
				return;
			}
			//echo "valor array: $valorColumna, valor array: $valorArray<br>";
			$i_array++;
        }	
	
	echo "<br>Archivo con formato correcto.";

	//si llego hasta aca es porque el archivo tiene el formato correcto!

	$conexion_bd = getConnection();

	$sql="insert into registros_envio_trabajo (grupo,ss_radicado,pedido,descripcion,cuenta,idetificacion_cuenta,causa,subcausa,apertura,fecha_radicado,direccion_servicio,estado,departamento_queja,contador_reapertura,segmento_siebel,fecha_entrega_trabajo,area_trabajo,contratista,concepto_pedido,direccion,cola,fuente,sistema_de_informacion,revisor,segun_ingreso,fecha_inicial_entrega,semana,mes,area_regional,id_file) values (";

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
		$flag="off";
                for ($col = 0; $col < $longitud_encabezado; ++$col) {
	
			$valor=$objWorksheet->getCellByColumnAndRow($col, $row)->getFormattedValue();
			if($col==8){
				$excelDate=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				$displayDate = PHPExcel_Style_NumberFormat::toFormattedString($excelDate, 'DD/MM/YYYY');
				//$date = date('Y-m-d',PHPExcel_Shared_Date::ExcelToPHP($objWorksheet->getCellByColumnAndRow($col, $row)->getValue()));  // array index 1              
   
				//$valor2=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				//echo "<br>FEcha sin formato: $date, con formato: $displayDate";
				$valor=$displayDate;
			}
			if($col!=8)
				$valor=latin1($valor);
			
			if($col==0 || $col==1){//es para controlar que no inserte registros vacios, se supone que este campo es el grupo
					       //y al parecer siempre el archivo lleva este campo
				if($valor==""){
					$flag="on";
				}
				
			}

	                 //echo '<td>&nbsp;' . $valor . '</td>' . "\n";
			$sql2=$sql2."$separator'$valor'";
        		
			$separator=",";
                }

                //echo '</tr>' . "\n";
		$sql2=$sql2.",$id_file)";
		

		//echo "[SQL]:  $sql2";

		if($flag=="on"){
			echo "<br>No se inserta SQL: $sql2 --- REGISTRO EN BLANCO!";
		} else {
			$result = pg_query($sql2);
		}
        }
	
	echo "SALIO DEL CICLO PRINCIPAL";
	//busco la contraseña para el envio de correo desde el buzon..
	$user=$_SESSION['user'];

        $sql="select pwd_correo from usuarios where login='$user'";

	echo " SQL password: $sql";	
        $result = pg_query($sql);
        $rows=pg_numrows($result);
        $pwd_correo="NULL";
        if($rows>0){//usuario existe
                $pwd_correo=pg_result($result,0,0);
        }
	
	//$user="dariasa";
	//$pwd_correo="d9anam99une*1234";
	
	$sql="select distinct contratista from registros_envio_trabajo where id_file=$id_file";

	echo " SQL DISTINCT: $sql";	

	$result = pg_query($sql);
	$rows=pg_numrows($result);

	//$actualpath=getcwd();

	//echo "<br><br>ACTUAL PATH: $actualpath $rows";

	for($j=0;$j<$rows;$j++){
		$contratista=pg_result($result,$j,0);

                if($contratista==""){
                        echo "<p>REGISTROS EN BLANCO </p>";
                        continue;
                }


		//esto se requiere para validar si el contratista si existe o no en la tabla
		$sql_consulta_contratistas="select correos_para,correos_copia,label from envio_trabajo_contratistas where contratista='$contratista'";
		//echo "<br>".$sql_consulta_contratistas."<br>";
		$result_contratistas = pg_query($sql_consulta_contratistas);
		
		$rowsc=pg_numrows($result_contratistas);

		if($rowsc<=0){//el contratista no existe en la base de datos para envio de trabajo
			echo "<p>El contratista $contratista no existe en la tabla para envio de trabajo. </p>";
			continue;
		}

		//echo "el contratista es: $contratista\n\r";

		$contratista=utf8_encode($contratista);
		
		$contratista2 = str_replace(" ", "-", $contratista);
		
		echo "con1: $contratista - con2: $contratista2";
		
		$filename="$pathy/documentos/envio-trabajo-$contratista2.xls";

		echo "archivo path: $filename";

		$fh = fopen($filename, 'w') or die("can't open file");

	        if (file_exists($filename)) {//borro el archivo
                	unlink($filename);
		}
	
		$phpExcel = new PHPExcel();
	        $sheet = $phpExcel->getActiveSheet();
        	$sheet->setTitle("Trabajo $contratista");

		$sheet->setCellValue("A1", "Grupo");
		$sheet->setCellValue("B1", "Nº de SS y/o Radicado");
		$sheet->setCellValue("C1", "Pedido");
		$sheet->setCellValue("D1", "Descripción y/o Hipervínculo");
		$sheet->setCellValue("E1", "Cuenta");
		$sheet->setCellValue("F1", "Idetificación cuenta");
		$sheet->setCellValue("G1", "Causa");
		$sheet->setCellValue("H1", "Subcausa");
		$sheet->setCellValue("I1", "Apertura");
		$sheet->setCellValue("J1", "Fecha Radicado");
		$sheet->setCellValue("K1", "Dirección de servicio");
		$sheet->setCellValue("L1", "Estado");
		$sheet->setCellValue("M1", "Departamento queja");
		$sheet->setCellValue("N1", "Contador Reapertura");
		$sheet->setCellValue("O1", "Segmento Siebel");
		$sheet->setCellValue("P1", "Fecha Entrega Trabajo");
		$sheet->setCellValue("Q1", "Area_trabajo");
		$sheet->setCellValue("R1", "Contratista");
		$sheet->setCellValue("S1", "Concepto_pedido");
		$sheet->setCellValue("T1", "Direccion");
		$sheet->setCellValue("U1", "Cola");
		$sheet->setCellValue("V1", "Fuente");
		$sheet->setCellValue("W1", "Sistema de Información");
		$sheet->setCellValue("X1", "Revisor");
		$sheet->setCellValue("Y1", "Según Ingreso");
		$sheet->setCellValue("Z1", "Fecha inicial Entrega");
		$sheet->setCellValue("AA1", "Semana");
		$sheet->setCellValue("AB1", "Mes");
		$sheet->setCellValue("AC1", "AREA-REGIONAL");

		
		//$sql2="select grupo,n_ss,pedido,descripcion,cuenta,identificacion,causa,subcausa,telefono_trabajo,apertura,ciudad_servicio,direccion_servicio,estado,departamento_servicio,departamento_queja,contador_reapertura,segmento_siebel,fecha_entrega_trabajo,area_trabajo,contratista,concepto_pedido,fecha_entrega, direccion,cola from envio_trabajo where id_file=$id_file and contratista='$contratista'";
		
		$sql2="select grupo,ss_radicado,pedido,descripcion,cuenta,idetificacion_cuenta,causa,subcausa,apertura,fecha_radicado,direccion_servicio,estado,departamento_queja,contador_reapertura,segmento_siebel,fecha_entrega_trabajo,area_trabajo,contratista,concepto_pedido,direccion,cola,fuente,sistema_de_informacion,revisor,segun_ingreso,fecha_inicial_entrega,semana,mes,area_regional from registros_envio_trabajo where id_file=$id_file and contratista='$contratista'";

		$result2 = pg_query($sql2);
		$rows2=pg_numrows($result2);

		//echo "<br>SQL$sql2 COUNTER: $rows2 >>";

		for($i=0;$i<$rows2;$i++){

			$grupo=pg_result($result2,$i,0);
			$ss_radicado=pg_result($result2,$i,1);
			$pedido=pg_result($result2,$i,2);
			$descripcion=pg_result($result2,$i,3);
			$cuenta=pg_result($result2,$i,4);
			$idetificacion_cuenta=pg_result($result2,$i,5);
			$causa=pg_result($result2,$i,6);
			$subcausa=pg_result($result2,$i,7);
			$apertura=pg_result($result2,$i,8);
			$fecha_radicado=pg_result($result2,$i,9);
			$direccion_servicio=pg_result($result2,$i,10);
			$estado=pg_result($result2,$i,11);
			$departamento_queja=pg_result($result2,$i,12);
			$contador_reapertura=pg_result($result2,$i,13);
			$segmento_siebel=pg_result($result2,$i,14);
			$fecha_entrega_trabajo=pg_result($result2,$i,15);
			$area_trabajo=pg_result($result2,$i,16);
			$contratista=pg_result($result2,$i,17);
			$concepto_pedido=pg_result($result2,$i,18);
			$direccion=pg_result($result2,$i,19);
			$cola=pg_result($result2,$i,20);
			$fuente=pg_result($result2,$i,21);
			$sistema_de_informacion=pg_result($result2,$i,22);
			$revisor=pg_result($result2,$i,23);
			$segun_ingreso=pg_result($result2,$i,24);
			$fecha_inicial_entrega=pg_result($result2,$i,25);
			$semana=pg_result($result2,$i,26);
			$mes=pg_result($result2,$i,27);
			$area_regional=pg_result($result2,$i,28);
			
			$sheet->setCellValue("A".($i+2),utf8_encode($grupo));
			$sheet->setCellValue("B".($i+2),utf8_encode($ss_radicado));
			$sheet->setCellValue("C".($i+2),utf8_encode($pedido));

                        $pos = strripos($descripcion, ".msg");
			
			if($pos==false){
				$pos = strripos($descripcion, ".pdf");
			}
			
			if($pos==false){
                                $pos = strripos($descripcion, ".doc");
                        }

			if($pos==false){
                                $pos = strripos($descripcion, ".png");
                        }

                        if($pos!=false){//consiguio la pocision
				
                                //$descripcion="//vjurado7/Radicados Siebel/$descripcion hola";
				$sheet->setCellValue("D".($i+2),utf8_encode($descripcion));
				$sheet->getCell("D".($i+2))->getHyperlink($descripcion)->setUrl('//OGARZON3/RADICADOS/'.$descripcion);
                        }else{
				$sheet->setCellValue("D".($i+2),utf8_encode($descripcion));
			}
			
			$sheet->setCellValue("E".($i+2),utf8_encode($cuenta));
			$sheet->setCellValue("F".($i+2),utf8_encode($idetificacion_cuenta));
			$sheet->setCellValue("G".($i+2),utf8_encode($causa));
			$sheet->setCellValue("H".($i+2),utf8_encode($subcausa));
			$sheet->setCellValue("I".($i+2),utf8_encode($apertura));
			$sheet->setCellValue("J".($i+2),utf8_encode($fecha_radicado));
			$sheet->setCellValue("K".($i+2),utf8_encode($direccion_servicio));
			$sheet->setCellValue("L".($i+2),utf8_encode($estado));
			$sheet->setCellValue("M".($i+2),utf8_encode($departamento_queja));
			$sheet->setCellValue("N".($i+2),utf8_encode($contador_reapertura));
			$sheet->setCellValue("O".($i+2),utf8_encode($segmento_siebel));
			$sheet->setCellValue("P".($i+2),utf8_encode($fecha_entrega_trabajo));
			$sheet->setCellValue("Q".($i+2),utf8_encode($area_trabajo));
			$sheet->setCellValue("R".($i+2),utf8_encode($contratista));
			$sheet->setCellValue("S".($i+2),utf8_encode($concepto_pedido));
			$sheet->setCellValue("T".($i+2),utf8_encode($direccion));
			$sheet->setCellValue("U".($i+2),utf8_encode($cola));
			$sheet->setCellValue("V".($i+2),utf8_encode($fuente));
			$sheet->setCellValue("W".($i+2),utf8_encode($sistema_de_informacion));
			$sheet->setCellValue("X".($i+2),utf8_encode($revisor));
			$sheet->setCellValue("Y".($i+2),utf8_encode($segun_ingreso));
			$sheet->setCellValue("Z".($i+2),utf8_encode($fecha_inicial_entrega));
			$sheet->setCellValue("AA".($i+2),utf8_encode($semana));
			$sheet->setCellValue("AB".($i+2),utf8_encode($mes));
			$sheet->setCellValue("AC".($i+2),utf8_encode($area_regional));
		}
		
		$objWriter = PHPExcel_IOFactory::createWriter($phpExcel, "Excel5");
	        $objWriter->save($filename);


		fclose($fh);

		echo "<p>Enviando Trabajo para $contratista, $rows2 registros, ";
		$cmd = "/usr/java/java/bin/java -jar /var/www/html/quejasune/java/QuejasUne.jar EnvioTrabajo $filename $user $pwd_correo /var/www/html/quejasune/java/fileConfig.xml \"$contratista\"";
		echo "COMANDO: $cmd";

        //echo $cmd;
        echo $output = shell_exec($cmd);
        echo "</p>";

        }
        

	return;

	//POR AHORA ESTO NO SE NECESITA ACA
	//$cmd = "/usr/java/java/bin/java -jar /var/www/html/quejasune/java/QuejasUne.jar CorreoQuejasCriticas $id_file $name_file /var/www/html/quejasune/java/fileConfig.xml $user $pwd_correo";

	//echo $cmd;

	//$output = shell_exec($cmd);
	//echo "Salida del proceso en shell: $output";
	echo "<p><h3>Se hizo la carga del archivo $name_file2 con exito.</h3></p>";

?>
