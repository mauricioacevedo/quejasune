<?php 
	session_start();
	include_once('conexion.php');
	
	$conexion_bd = getConnection();

	$sql="select zona,correos,con_copia from zonas_de_correo";

	//$result = pg_query($sql);
	//$rows=pg_numrows($result);

	//datos generales de la forma actual
        $the_matrix=$_SESSION["the_matrix"];
        $counterForm=$_SESSION["counter_form"];

	$nombreForma="Correos de Actividades";
	$linkForma="./carga_actividades.php";
	

?>


<html>
<body>
<? include 'header.php'; ?>
<center><h2>Correos de Actividades</h2></center>

<form enctype="multipart/form-data" action="carga_actividades_2.php" method="post">
<input name="MAX_FILE_SIZE" value="300000000" type="hidden">
<table align="center">
<tbody> 
<tr>
<td align="center" bgcolor="#ff0000"><font color="#ffffff"><b>Archivo :</b></font></td><td align="left"><input name="userfile" style="background-color: rgb(255, 255, 160);"  size="25" type="file"></td>
</tr>

</tbody></table>
<br>
<center><input value="Upload" type="submit"></center>

</form>
<br>
<center><a href="./mod_contratistas_actividades.php">Modificar Contratistas</a></center>
</body>
</html>
