<?php
	session_start();
	include_once('conexion.php');
	$pedido=$HTTP_GET_VARS["pedido"];
	//$operacion=$HTTP_GET_VARS["operacion"];
	
	//$fechaIni=date("Y")."-".date("m")."-".date("d")." ".date("H").":".date("i").":".date("s");
	//$_SESSION['initTime']=$fechaIni;		
	$conexion_bd = getConnection();
	
	$sql="select numero_queja,(select nombre from tecnicos where identificacion=id_tecnico),accion,asesor,to_char(fecha,'yyyy-mm-dd hh24:mi:ss'),observaciones,id from registros where date(fecha) - date(current_timestamp) <=1 and numero_queja='$pedido' order by fecha DESC";
	$result = pg_query($sql);
	$rows=pg_numrows($result);
	
	
?>

<html>
<head>
<link rel="stylesheet" href="javascript/quejas.css" type="text/css" />
</head>
<body>
<h2>Registro de Ingresos para el Numero de Queja <font color="red"> <? echo $pedido; ?></font></h2>
<table width="100%" align="center">
<tr bgcolor="Black">
<!--td align='center' ><font color="White"><b>Numero de Queja</b></font></td-->
<td align='center'><font color="White"><b>Tecnico</b></font></td>
<td align='center'><font color="White"><b>Accion</b></font></td>
<td align='center'><font color="White"><b>Asesor</b></font></td>
<td align='center'><font color="White"><b>Fecha</b></font></td>
<td align='center'><font color="White"><b>Observaciones</b></font></td>
</tr>

<?
  $j=1;
  $bg="#CCCCCC";

for($i=0;$i<$rows;$i++){
	$j=$j+1;
	$click="";
	$pedido = pg_result($result,$i,0);
	$tecnico = pg_result($result,$i,1);
	$accion = pg_result($result,$i,2);
	$asesor = pg_result($result,$i,3);
	$fecha = pg_result($result,$i,4);
	$observaciones = pg_result($result,$i,5);

	if($observaciones!=""){
		$click="Click!";
	}

	$id=pg_result($result,$i,6);
  	if( $j % 2 == 0 ){ $bg="#EFEFEF";}
	else { $bg="#FFFFFF";}

	echo "<tr bgcolor='".$bg."'>";
	echo "<!--td align='center'>$pedido</td-->";
	echo "<td align='center'>$tecnico</td>";
	echo "<td align='center'>$accion</td>";
	echo "<td align='center'>$asesor</td>";
	echo "<td align='center'>$fecha</td>";
	echo "<td align='center' onclick='javascript:mostrarObservacion(\"$id\");'><div id='div$id' style='position:absolute;visibility:hidden;'>$observaciones</div>$click</td>";
	echo "</tr>";

}
?>

</table>
<br>
<center><input type="button" class="btnpurple" name="cancelar" value="Cerrar" onclick="javascript:Modalbox.hide();return false;"></center>
</body>
</html>
