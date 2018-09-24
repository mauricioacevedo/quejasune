<?php
	session_start();
	//require_once 'Phpexcel/Classes/PHPExcel/IOFactory.php';
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
	   	echo "Problema con la carga del archivo: Archivo muy grande o problemas locales de disco.";
		return;
	}
	
        $nombreForma="Cruce Pedidos 2";
        $linkForma="./cruce_pedidos2.php";

	?>

	<html><body>
	<? include 'header.php'; ?>	
	<center><h2>Cruce de Pedidos en Fenix</h2></center>

	<h3>Procesando archivo: <? echo $name_file2; ?></h3>
	
	<?
	$conexion_bd = getConnection();
	//consulto contraseña para fenix
	$user=$_SESSION['user'];

	$sql="select pwd_fenix from usuarios where login='$user'";

        $result = pg_query($sql);
        $rows=pg_numrows($result);
	$pwd_fenix="NULL";
        if($rows>0){//usuario existe
                $pwd_fenix=pg_result($result,0,0);
			
	}

	$cmd = "/usr/java/java/bin/java -jar /var/www/html/quejasune/java/QuejasUne.jar CrucePedidosFenix $uploadfile 10.120.53.129 fenixune $user $pwd_fenix";

	//$cmd="/var/www/html/quejasune/java/comando.sh $id_file $name_file /var/www/html/quejasune/java/fileConfig.xml";

	//echo $cmd;

	$output = shell_exec($cmd);
	//echo "Salida del proceso en shell: $output";
	echo "<p><h3>Archivo con cruce de informacion generado. Para ver el archivo click <a href='./CrucePedidos.csv'>aca</a></h3></p>";

?>
