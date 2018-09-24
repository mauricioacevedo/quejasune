<?php
	session_start();
	require_once 'Phpexcel/Classes/PHPExcel/IOFactory.php';
	require_once 'Phpexcel/Classes/PHPExcel.php';

	ob_end_flush();
	ini_set("output_buffering", "0");
	ob_implicit_flush(true);

	include_once('conexion.php');
	//include("excelwriter.inc.php");

	function microtime_float(){
		list($useg, $seg) = explode(" ", microtime());
		return ((float)$useg + (float)$seg);
	}
	

	function latin1($txt) {
		//$encoding = mb_detect_encoding($txt, 'ASCII,UTF-8,ISO-8859-1');
		//if ($encoding == "UTF-8") {
			$txt = utf8_decode($txt);
		//}
 		return $txt;
	}
	
	//echo latin1("hiyaaaa");	

	$tiempo_inicio = microtime_float();

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
		//$name_file="/var/www/html/quejasune/tmp/Libro9xx.xls";
		return;
	}

        $nombreForma="Informacion Siebel 2";
        $linkForma="./informacion_siebel2.php";

	?>
	
	<html><body>
        <? include 'header.php'; ?>

	<h3>Procesando archivo: <? echo $name_file2;?></h3>

	
	<?
	
	$objReader = new PHPExcel_Reader_Excel5();
        //$objReader =PHPExcel_IOFactory::createReader($inputFileType); 
        $objReader->setLoadSheetsOnly("Hoja1");
        
	$objPHPExcel =$objReader->load($name_file);

	$objWorksheet = $objPHPExcel->getActiveSheet();

        $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
        $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'

        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g. 5

	//ciclo para validar que los campos del archivo que se trata de ingresar sean los que se esperan

	$arreglo = array("Grupo","Nº de SS","Pedido","Descripción","Cuenta","Idetificación cuenta","Causa","Subcausa","Apertura","Ciudad de Servicio","Dirección de servicio","Estado","Departamento de Servicio","Departamento queja","Contador Reapertura","Segmento","Fuente","Propietario","Fecha atención");
	//echo "<br>";
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
				//echo "valor columna: ,$valorColumna, valor array: ,$valorArray,<br>";
				echo "El archivo $name_file2 no corresponde al documento esperado, revisar las columnas que deben tener este encabezado:";
				echo "<br>".$arreglo."<br>";
				return;
			}
			//echo "valor array: $valorColumna, valor array: $valorArray<br>";
			$i_array++;
        }	
	
	echo "<br>Archivo con formato correcto.";

	//si llego hasta aca es porque el archivo tiene el formato correcto!

	$conexion_bd = getConnection();
	$encoding = pg_client_encoding($conexion_bd);
	echo "<br>[conectado a la base de datos] ENCODING: $encoding";
	$sql="insert into informacion_siebel (grupo,n_ss,pedido,descripcion,cuenta,identificacion,causa,subcausa,apertura,ciudad_servicio,direccion_servicio,estado,departamento_servicio,departamento_queja,contador_reapertura,segmento_siebel,fuente,propietario,fecha_atencion,id_file) values (";


	//echo "<a href='./carga.php?file=$name_file'>Continuar</a>&nbsp;<a href='./carga_actividades.php'>Cancelar</a>";


	//si hizo la carga con exito, ingreso informacion del archivo leido en la tabla..


	//obtengo el id nuevo:

	$sql3="select nextval('sec_files')";
	$result = pg_query($sql3);

	$rows=pg_numrows($result);

	if($rows>0){
		$fileiddb=pg_result($result,0,0);
	} else{
		echo "<br><h2>Ocurrio un error a nivel de base de datos: no se pudo obtener identificador para nuevo file</h2>";
                reutrn;
	}


	$sql3="insert into info_files(filename,id) values ('$name_file2',$fileiddb)";
	$result = pg_query($sql3);
	
	//$oid = pg_last_oid($result);
	//obtengo el identificador del ultimo registro ingresado, esto para generar la relacion entre las tablas de actividades y archivos

	//$sql3="select id from info_files where oid=$oid";
	//$result = pg_query($sql3);

	//$rows=pg_numrows($result);
	
	$id_file=$fileiddb;

	/*
	if($rows>0){
		$id_file=pg_result($result,0,0);
	}else{
		echo "<br><h2>Ocurrio un error a nivel de base de datos: no se obtuvieron registros con el OID: $oid</h2>";
		reutrn;
	}*/

        //echo '<table border="1">' . "\n";
	$sql2=$sql;
	$separator="";


	//echo "1";

	//se comienza en la fila 2 porque se supone que el encabezado esta en la fila 1.
      for ($row = 2; $row <= $highestRow; ++$row) {
                //echo '<tr>' . "\n";
		$sql2=$sql;
		$separator="";
                for ($col = 0; $col < $longitud_encabezado; ++$col) {
		
			//}else {
				//echo "[1]";
				$valor=$objWorksheet->getCellByColumnAndRow($col, $row)->getFormattedValue();

				//echo "[2]";
				$valor=latin1($valor);
	                        //echo '<td>&nbsp;' . $valor . '</td>' . "\n";
				//echo "[3]";
				$sql2=$sql2."$separator'$valor'";
        		//}
			if($col==1){
                        	echo "<BR>INSERTANDO SS $valor";
                        }
			$separator=",";
                }

                //echo '</tr>' . "\n";
		$sql2=$sql2.",$id_file)";
		//echo "$sql2";
		$result = pg_query($sql2);

		if ($result === false) {
    			$error= pg_last_error($conexion_bd);
			//echo pg_last_error($dbconn);
			echo "  ---  $error";
		}
		
        }
	

	echo "Termino inserts, inician validaciones!!!!<br>\n";
        //echo '</table>' . "\n";

	//en este momento ya tengo toda la informacion del archivo en una tabla de la db

	//normalizacion
	//Cuando la columna pedido esta vacia:
		//1. Si el registro es de manizales o quindio(DEPARTAMENTO DE QUEJA), se coloca en pedido 66666666
		//2. Si el registro es de bogota(DEPARTAMENTO DE QUEJA), se coloca 77777777
		//3. Queja tipo actividad: si en el campo descripcion aparece QUEJAS GESTION OPERATIVA, buscar el numero de actividad que aparece como 1-xxxxx y colocarlo en el campo pedido. Para este ultimo punto, solo lo hacemos despues de haber validado los 2 primeros puntos.
		//4. eliminar la fuente tipo carta
		//5. si el pedido continua vacio colocar 88888888
		//al parecer si despues de estos 3 puntos quedan pedidos vacios eso lo debe atender dimas.
		//6. actualizar los registros tipo actividad
		//7. con las areas de trabajo que quedan vacias colocar el contratista con cundinamarca y caldas
	//1.
	$sql_update="update informacion_siebel set pedido='66666666' where departamento_queja in ('CALDAS','QUINDIO') and id_file=$id_file";
	$result = pg_query($sql_update);
	echo "[validacion1 end]";	

	//2.

	//$sql_update2b="update informacion_siebel set pedido=substring(descripcion from '([0-9]{8})' ) where pedido='' and departamento_queja in ('BOGOTA DC','CUNDIM/CA') and id_file=$id_file";
        //$result = pg_query($sql_update2b);

       //2b.
	 $sql_update2="update informacion_siebel set pedido='77777777' where departamento_queja in ('BOGOTA DC','CUNDIM/CA') and pedido=''  and id_file=$id_file";
        $result = pg_query($sql_update2);


	echo "[validacion3 end]";	
	
	//3. para la tercera normalizacion, primero debo obtener los registros, sacar la actividad y ponerla en el campo pedido
	$sql_update3="update informacion_siebel set pedido=substring(descripcion from '([0-9]{1}\-[a-zA-Z0-9]{7})' ) where pedido='' and id_file=$id_file";
	$result = pg_query($sql_update3);

	echo "[validacion4 end]";	
	//colocar en el campo departamento queja cundinamarca para todo lo que diga bogota.


	//4. eliminar fuentes de tipo carta:

	$sql_update5 ="delete from informacion_siebel where id_file=$id_file and fuente = 'Carta' or pedido='99999' or fuente = 'Web'";

	//$result = pg_query($sql_update5);

        //5. actualizar en 88888888 los pedidos vacios.
        $sql_update6="update informacion_siebel set pedido='88888888' where (pedido='' or pedido is NULL) and departamento_queja not in ('BOGOTA DC','CUNDIM/CA') and id_file=$id_file";
        $result = pg_query($sql_update6);
	
	echo "[validacion5 end]";	

	//en esta altura deberia hacerse la ejecucion de la aplicacion para el correo.
	//$id_file -- el id del archivo
	//$name_file  -- ruta actual del archivo

	//busco la contraseña para el envio de correo desde el buzon..
	$user=$_SESSION['user'];

        $sql="select pwd_fenix from usuarios where login='$user'";

        $result = pg_query($sql);
        $rows=pg_numrows($result);
        $pwd_fenix="NULL";
        if($rows>0){//usuario existe
                $pwd_fenix=pg_result($result,0,0);

        }
	
	echo "<br>Se va a ejecutar comando en Fenix: <br>\n";
	
	$cmd = "/usr/java/java/bin/java -jar /var/www/html/quejasune/java/QuejasUne.jar InformacionSiebel 10.120.53.129 fenixune $user $pwd_fenix $id_file /var/www/html/quejasune/java/fileConfig.xml";
	
	echo $cmd;

	$proc = popen("$cmd", 'r');
	    while (!feof($proc)) {
		echo " ".fread($proc, 4096);
		//echo "[".date("h24:i:s")."] ".fread($proc, 4096);
	    }


	//$output = shell_exec($cmd);
	
	//echo "[END]";

	$tiempo_medio = microtime_float();

	echo "<br>Tiempo Consulta Fenix: ".($tiempo_medio - $tiempo_inicio)."<br>\n\r";
	
	//6A. debo actualizar en la fecha_cita todos los valores que aparezcan con el literal "null"..

	$sql="update informacion_siebel set fecha_cita='1000-01-01' where fecha_cita='null' and id_file=$id_file";
	$result = pg_query($sql);

	$sql="update informacion_siebel set fecha_cita=to_char(date(fecha_cita), 'DD/MM/YYYY') where id_file=$id_file";
        $result = pg_query($sql);


	//6B. actualizar el departamento
	$sql_update4="update informacion_siebel set departamento_queja='CUNDINAMARCA' where departamento_queja ilike '%BOGOTA%' and id_file=$id_file";
        $result = pg_query($sql_update4);

	$sql_update4="update informacion_siebel set area_trabajo='MAN',concepto_id='Abierto' where departamento_queja ilike '%CALDAS%' and id_file=$id_file";
        $result = pg_query($sql_update4);


	$sql_update4="update informacion_siebel set area_trabajo='ARM',contratista='ARMENIA' where departamento_queja ilike '%QUINDIO%' and id_file=$id_file";
        $result = pg_query($sql_update4);


	$sql_update4="update informacion_siebel set area_trabajo='ATL',contratista='ATL' where departamento_queja ilike '%ATLANTICO%' and id_file=$id_file";
        $result = pg_query($sql_update4);

	$sql_update4="update informacion_siebel set area_trabajo='CALI',contratista='CALI' where departamento_queja ilike '%VDEL CAUCA%' and id_file=$id_file";
        //$result = pg_query($sql_update4);

	$sql_update4="update informacion_siebel set area_trabajo='BUC',contratista='BUC' where departamento_queja ilike '%SANTANDER%' and id_file=$id_file";
        $result = pg_query($sql_update4);

	$sql_update4="update informacion_siebel set area_trabajo='CUC',contratista='CUC' where departamento_queja ilike '%NSANTANDER%' and id_file=$id_file";
        $result = pg_query($sql_update4);


	 $sql_update4="update informacion_siebel set area_trabajo='CAR',contratista='CAR' where departamento_queja ilike '%BOLIVAR%' and id_file=$id_file";
        $result = pg_query($sql_update4);



	//21-08-2013 modificado.
	$sql_update4="update informacion_siebel set contratista='RETEN-TE' where cola ilike '%RETEN-TE%' and departamento_queja ilike '%ANTIOQUIA%' and id_file=$id_file";
        $result = pg_query($sql_update4);

	//21-08-2013 modificado.
	$sql_update4="update informacion_siebel set contratista='RETEN-TE' where cola ilike '%RETEN-PI%' and departamento_queja ilike '%ANTIOQUIA%' and id_file=$id_file";-
        $result = pg_query($sql_update4);



	//6. actualizar los tipo actividad
        $sql="select id from informacion_siebel where pedido ~ '[0-9]{1}\-[a-zA-Z0-9]{7}' and id_file=$id_file and departamento_queja='ANTIOQUIA';";

         $result = pg_query($sql);
         $rows=pg_numrows($result);

        for($i=0;$i<$rows;$i++){
                $id_registro=pg_result($result,$i,0);

                //$sql2="update informacion_siebel set concepto_id='ACTIVIDAD',contratista='Giovani Rodriguez' where id=$id_registro";
                $sql2="update informacion_siebel set concepto_id='ACTIVIDAD',contratista='MED' where id=$id_registro";
                //echo "$sql2 <br>";
                pg_query($sql2);
        }

	//7. llenar las areas de trabajo vacias de cundinamarca y caldas

	$sql="update informacion_siebel set area_trabajo=contratista,concepto_id='Abierto' where departamento_queja in ('BOGOTA DC','CUNDIM/CA','CALDAS','CUNDINAMARCA') and id_file=$id_file and (area_trabajo='' or area_trabajo is NULL);";

         $result = pg_query($sql);
         $rows=pg_numrows($result);


	$sql_update4="update informacion_siebel set area_trabajo='ARM',contratista='ARMENIA',concepto_id='Abierto' where departamento_queja ilike '%QUINDIO%' and id_file=$id_file";
        $result = pg_query($sql_update4);



	$filename="./documentos/exporte-siebel.xls";
	$fh = fopen($filename, 'w') or die("can't open file");
	fclose($fh);

	if (file_exists($filename)) {//borro el archivo
		unlink($filename);
	}

	$phpExcel = new PHPExcel();
	$sheet = $phpExcel->getActiveSheet();
	$sheet->setTitle("Sabana-QuejasUne");
	
	$sheet->setCellValue("A1", "Grupo");
	$sheet->setCellValue("B1", "Nº de SS");
	$sheet->setCellValue("C1", "Pedido");
	$sheet->setCellValue("D1", "Descripción");
	$sheet->setCellValue("E1", "Cuenta");
	$sheet->setCellValue("F1", "Idetificación cuenta");
	$sheet->setCellValue("G1", "Causa");
	$sheet->setCellValue("H1", "Subcausa");
	$sheet->setCellValue("I1", "Apertura");
	$sheet->setCellValue("J1", "Ciudad de Servicio");
	$sheet->setCellValue("K1", "Dirección de servicio");
	$sheet->setCellValue("L1", "Estado");
	$sheet->setCellValue("M1", "Departamento de Servicio");
	$sheet->setCellValue("N1", "Departamento queja");
	$sheet->setCellValue("O1", "Contador Reapertura");
	$sheet->setCellValue("P1", "Segmento Siebel");
	$sheet->setCellValue("Q1", "Fecha Entrega Trabajo");
	$sheet->setCellValue("R1", "Area_trabajo");
	$sheet->setCellValue("S1", "Contratista");
	$sheet->setCellValue("T1", "Concepto_pedido");
	$sheet->setCellValue("U1", "Fecha_Entrega");
	$sheet->setCellValue("V1", "Direccion");
	$sheet->setCellValue("W1", "Cola");
	
	//$sql="select grupo,n_ss,pedido,descripcion,cuenta,identificacion,causa,subcausa,to_char(date(apertura), 'DD/MM/YYYY'),ciudad_servicio,direccion_servicio,estado,departamento_servicio,departamento_queja,contador_reapertura,segmento_siebel,fecha_atencion,area_trabajo,contratista,concepto_id,to_char(date(fecha_cita), 'DD/MM/YYYY'), direccion,cola from informacion_siebel where id_file=$id_file";

	//se cambia el 31-03-2014: algunos registros tienen problema con la fecha de apertura..
	//$sql="select grupo,n_ss,pedido,descripcion,cuenta,identificacion,causa,subcausa,to_char(date(apertura), 'DD/MM/YYYY'),ciudad_servicio,direccion_servicio,estado,departamento_servicio,departamento_queja,contador_reapertura,segmento_siebel,fecha_atencion,area_trabajo,contratista,concepto_id,fecha_cita, direccion,cola from informacion_siebel where id_file=$id_file";
	$sql="select grupo,n_ss,pedido,descripcion,cuenta,identificacion,causa,subcausa,apertura,ciudad_servicio,direccion_servicio,estado,departamento_servicio,departamento_queja,contador_reapertura,segmento_siebel,fecha_atencion,area_trabajo,contratista,concepto_id,fecha_cita, direccion,cola from informacion_siebel where id_file=$id_file";

	
	echo "[SQL]".$sql;

	$result = pg_query($sql);
	$rows=pg_numrows($result);

	for($i=0;$i<$rows;$i++){
		$grupo=pg_result($result,$i,0);
		$num_ss=pg_result($result,$i,1);
		$pedido=pg_result($result,$i,2);
		$descripcion=pg_result($result,$i,3);
		$cuenta=pg_result($result,$i,4);
		$identificacion=pg_result($result,$i,5);
		$causa=pg_result($result,$i,6);
		$subcausa=pg_result($result,$i,7);

		$apertura=pg_result($result,$i,8);

		$ciudad_servicio=pg_result($result,$i,9);
		$direccion_servicio=pg_result($result,$i,10);
		$estado=pg_result($result,$i,11);
		$departamento_servicio=pg_result($result,$i,12);
		$departamento_queja=pg_result($result,$i,13);
		$contador_reapertura=pg_result($result,$i,14);
		$segmento_siebel=pg_result($result,$i,15);
		$fecha_atencion=pg_result($result,$i,16);
		$area_trabajo=pg_result($result,$i,17);
		$contratista=pg_result($result,$i,18);
		$concepto_id=pg_result($result,$i,19);
		$fecha_cita=pg_result($result,$i,20);

		//$temp=split(" ",$fecha_cita,2);
		//$fecha_cita=$temp[0];

		$direccion=pg_result($result,$i,21);
		$cola=pg_result($result,$i,22);
		
		//$myArr=array($grupo,$num_ss,$pedido,$descripcion,$cuenta,$identificacion,$causa,$subcausa,$apertura,$ciudad_servicio,$direccion_servicio,$estado,$departamento_servicio,$departamento_queja,$contador_reapertura,$segmento_siebel,$fecha_atencion,$area_trabajo,$contratista,$concepto_id,$fecha_cita,$direccion,$cola);
		
		$sheet->setCellValue("A".($i+2),utf8_encode($grupo));
		$sheet->setCellValue("B".($i+2),utf8_encode($num_ss));
		$sheet->setCellValue("C".($i+2),utf8_encode($pedido));
		$sheet->setCellValue("D".($i+2),utf8_encode($descripcion));
		$sheet->setCellValue("E".($i+2),utf8_encode($cuenta));
		$sheet->setCellValue("F".($i+2),utf8_encode($identificacion));
		$sheet->setCellValue("G".($i+2),utf8_encode($causa));
		$sheet->setCellValue("H".($i+2),utf8_encode($subcausa));
		$sheet->setCellValue("I".($i+2),utf8_encode($apertura));
		$sheet->setCellValue("J".($i+2),utf8_encode($ciudad_servicio));
		$sheet->setCellValue("K".($i+2),utf8_encode($direccion_servicio));
		$sheet->setCellValue("L".($i+2),utf8_encode($estado));
		$sheet->setCellValue("M".($i+2),utf8_encode($departamento_servicio));
		$sheet->setCellValue("N".($i+2),utf8_encode($departamento_queja));
		$sheet->setCellValue("O".($i+2),utf8_encode($contador_reapertura));
		$sheet->setCellValue("P".($i+2),utf8_encode($segmento_siebel));
		$sheet->setCellValue("Q".($i+2),utf8_encode($fecha_atencion));
		$sheet->setCellValue("R".($i+2),utf8_encode($area_trabajo));
		$sheet->setCellValue("S".($i+2),utf8_encode($contratista));
		$sheet->setCellValue("T".($i+2),utf8_encode($concepto_id));
		$sheet->setCellValue("U".($i+2),utf8_encode($fecha_cita));
		$sheet->setCellValue("V".($i+2),utf8_encode($direccion));
		$sheet->setCellValue("W".($i+2),utf8_encode($cola));


	}
	$tiempo = $tiempo_fin - $tiempo_inicio;
        echo "<br><br>Tiempo empleado: " . ($tiempo_fin - $tiempo_inicio);

	$objWriter = PHPExcel_IOFactory::createWriter($phpExcel, "Excel5");
        $objWriter->save($filename);



	echo "<br> Descargar archivo: <a href='$filename'>Archivo Siebel-Fenix</a>";
	echo "<script language='javascript'>location.href='$filename';</script>";

	return;

	//POR AHORA ESTO NO SE NECESITA ACA
	//$cmd = "/usr/java/java/bin/java -jar /var/www/html/quejasune/java/QuejasUne.jar CorreoQuejasCriticas $id_file $name_file /var/www/html/quejasune/java/fileConfig.xml $user $pwd_correo";

	//echo $cmd;

	//$output = shell_exec($cmd);
	//echo "Salida del proceso en shell: $output";
	echo "<p><h3>Se hizo la carga del archivo $name_file2 con exito.</h3></p>";
?>
