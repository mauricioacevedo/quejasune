<?php 
	session_start();
        include_once('conexion.php');
        $conexion_bd = getConnection();
        $user=$_SESSION['user'];
        $username=$_SESSION['username'];
	
	 if($user==""){//no hay usuario logueado, devolver a pantalla inicial.
                echo "<script>location.href='./index.php';</script>";
                return;
        }

	$operacion=$_GET["operacion"];

	if($operacion=="cambiar"){
		$pwd_correo=$_GET["pwd_correo"];
		$pwd_fenix=$_GET["pwd_fenix"];

		$sql="update usuarios set pwd_correo='$pwd_correo',pwd_fenix='$pwd_fenix' where login='$user'";
		$result = pg_query($sql);
		
		echo "<script>alert('Contraseñas cambiadas con exito!');";
                echo "location.href='./inicio.php';</script>";
		return;
	}

        $sql="select pwd_correo,pwd_fenix from usuarios where login='$user'";

        $result = pg_query($sql);
        $rows=pg_numrows($result);

        if($rows>0){//usuario existe, obtengo contraseñas
                $pwd_correo=pg_result($result,0,0);
		$pwd_fenix=pg_result($result,0,1);
	}
	
        $nombreForma="Cambio Contraseñas";
        $linkForma="./editarConstrasenas.php";
?>


<html>
<head>

<script language="javascript">

	function cambiarConstrasenas(){
		
		var pwd_correo=document.getElementById("pwd_correo").value;
		var pwd_fenix=document.getElementById("pwd_fenix").value;
		
		location.href="./editarConstrasenas.php?operacion=cambiar&pwd_correo="+pwd_correo+"&pwd_fenix="+pwd_fenix;

	}

</script>
</head>
<body>
<? include 'header.php'; ?>

<center><h2>Modificar Contraseñas</h2></center>
<form name="contrasenas" action="javascript:cambiarConstrasenas();">
<table align="center">
<tr>
<td><b>Usuario:</b></td><td><? echo "$username (<font color='blue'>$user</font>)"; ?></td>
</tr>
<tr>
<td><b>Contraseña Correo:</b></td><td><input type="password" value="<? echo $pwd_correo;?>" id="pwd_correo"></td>
</tr>
<tr>
<td><b>Contraseña Fenix:</b></td><td><input type="password" value="<? echo $pwd_fenix;?>" id="pwd_fenix"></td>
</tr>
</table>
<center><input type="submit" value="Aceptar">
</form>
</body>
</html>
