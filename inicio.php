<?php
	session_start();
	include_once('conexion.php');
        $conexion_bd = getConnection();
	

	$user=$_SESSION['user'];

	if($user==""){//si no hay usuario en session es usuario nuevo, de lo contrario ya esta logueado

		$user=$_GET['user'];
		$pwd=$HTTP_GET_VARS["pwd"];
		
	        $sql="select nombre from usuarios where login='$user'";

        	$result = pg_query($sql);
	        $rows=pg_numrows($result);

		if($rows>0){//usuario existe, actualizo la contraseña de red y dejo seguir
			$nombre=pg_result($result,0,0);
			$sql="update usuarios set pwd_correo='$pwd' where login='$user'";
			//echo $sql;
			$result = pg_query($sql);
			//guardo el usuario en la session, para poderlo utilizar mas tarde
			$_SESSION['user']=$user;
			$_SESSION['username']=$nombre;
		} else {//el usuario no existe en el sistema, lo devuelvo a la pantalla inicial.
			echo "<script>location.href='./index.php?msg=Usuario Invalido';</script>";
			return;
		}
	}

        //$the_matrix=$_SESSION["the_matrix"];
        //$counterForm=$_SESSION["counter_form"];

        $nombreForma="Opciones";
        $linkForma="./inicio.php";


?>

<html>
<head>
<link rel="stylesheet" href="javascript/quejas.css" type="text/css" />
</head>
<body>
<? include 'header.php'; ?>

<center><h2>Opciones de Aplicacion</h2></center>
<!--p align="center"><a href='./carga_actividades.php'>Correos de Actividades</a></p-->
<p align="center"><a href='./quejas_criticas.php'>Correos de Quejas Criticas</a></p>
<p align="center"><a href='./informacion_siebel.php'>Sabana Quejas</a></p>
<p align="center"><a href="./MacroQuejas.xls">Archivo de Macros de Quejas</p>
<p align="center"><a href='./entrega_trabajo.php'>Envio de Trabajo</a></p>
<p align="center"><a href='./organizar_info_senorune.php'>Informacion Señor UNE</a></p>
<p align="center"><a href="./editarConstrasenas.php">Modificar contraseñas</p>
</body>
</html>
