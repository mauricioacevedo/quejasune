<?php 
	session_start();
	include_once('conexion.php');
	$conexion_bd = getConnection();

	$sql="select zona,correos,con_copia from zonas_de_correo";

	//$result = pg_query($sql);
	//$rows=pg_numrows($result);

        $nombreForma="Informacion Siebel";
        $linkForma="./informacion_siebel.php";	

?>


<html>
<body>
<? include 'header.php'; ?>

<center><h2>Informacion Señor UNE</h2></center>

<form enctype="multipart/form-data" action="organizar_info_senorune2.php" method="post">
<input name="MAX_FILE_SIZE" value="300000000" type="hidden">
<table align="center">
<tbody> 
<tr>
<td align="center" bgcolor="#ff0000"><font color="#ffffff"><b>Seleccione archivo de excel de Siebel:</b></font></td><td align="left"><input name="userfile" style="background-color: rgb(255, 255, 160);"  size="25" type="file"></td>
</tr>

</tbody></table>
<br>
<center><input value="Cargar" type="submit"></center>

</form>


</body>
</html>
