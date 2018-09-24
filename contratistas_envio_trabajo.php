<?php 
	session_start();
	include_once('conexion.php');

	$msg=$_GET["msg"];

	$conexion_bd = getConnection();

	$sql="select contratista, label,id from envio_trabajo_contratistas";

	$result = pg_query($sql);
	$rows=pg_numrows($result);

        $nombreForma="Contratistas Envio Trabajo";
        $linkForma="./contratistas_envio_trabajo.php";
?>


<html>
<head>
<title>Contratistas y Grupos de Trabajo Quejas</title>
<script language="javascript">
	function editar(id){
		location.href="./agregarContratistaEnvioTrabajo.php?operacion=editar&id="+id;
	}
	
        function eliminar(id,nombre){
		if(confirm("Esta seguro que desea eliminar el contratista "+nombre+"?")){
	                location.href="./agregarContratistaEnvioTrabajo.php?operacion=eliminar&id="+id;
			return;
		}
        }

</script>
</head>
<body>
<? include 'header.php'; ?>
<center><h2>Contratistas y Grupos de Trabajo Quejas</h2></center>
<br>
<center><h3><font color="red"><b><? echo $msg; ?></b></font></h3></center>
<br>
<center><a href="./agregarContratistaEnvioTrabajo.php">Agregar Contratista</a></center>
<br>
<form action="" method="post">
<table align="center" align="center">
<tbody> 
<tr bgcolor="red">
<td align="center"><font color="white"><b>Contratista</b></font></td>
<td align="center"><font color="white"><b>Label</b></font></td>
<td align="center"><font color="white"><b>Opciones</b></font></td>
</tr>

<?
$j=0;

	for($i=0;$i<$rows;$i++){
		$nombre=pg_result($result,$i,0);
		$label=pg_result($result,$i,1);
		$id=pg_result($result,$i,2);
		if($j % 2 == 0){
			$color="#f5f5f5";
		}else{
			$color="";
		}
		$j++;

		echo "<tr bgcolor='$color'>";
		echo "<td>$nombre</td>";
		echo "<td>$label</td>";
		echo "<td><a href='javascript:editar(\"$id\");'>Editar</a> - <a href='javascript:eliminar(\"$id\",\"$nombre\");'>Eliminar</a></td>";
		echo "</tr>";
	}

?>

</tbody></table>
<br>

</form>
</body>
</html>
