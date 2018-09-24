<?php
	session_start();
	include_once('conexion.php');
	$id_tecnico=$HTTP_GET_VARS["id_tecnico"];
	$operacion=$HTTP_GET_VARS["operacion"];
	
	$conexion_bd = getConnection();
	
	$sql="select nombre,empresa,ciudad from tecnicos where identificacion='$id_tecnico'";
	$result = pg_query($sql);
	$rows=pg_numrows($result);
	
	if($rows < 1){//el tecnico no existe en la base de datos
		//es improbable que pase..
		//echo "<script>Modalbox.hide();return false</script>";
		return;
	}
	
	$nombre_tecnico_ingreso = pg_result($result,0,0);
	$empresa_tecnico=pg_result($result,0,1);
	$ciudad_tecnico=pg_result($result,0,2);	


	$sql="select id,nombre from empresas order by nombre ASC";
	$result = pg_query($sql);
	$rows=pg_numrows($result);
	
	
?>

<html>
<head>
</head>
<body>
<h2>Modificar Informacion de Tecnicos</h2>
<table>
<tr>
<td>Identificacion</td><td><input type="text" value="<? echo $id_tecnico; ?>" id="id_tecnico_ingreso"></td>
</tr>
<tr>
<td>Nombre</td><td><input type="text" value="<? echo $nombre_tecnico_ingreso; ?>" id="nombre_tecnico"></td>
</tr>

<tr>
<td>Ciudad</td><td>
<select id="ciudad">
	<option value="MEDELLIN" <? if($ciudad_tecnico=='MEDELLIN') echo "selected"; ?>>MEDELLIN</option>
	<option value="ARMENIA" <? if($ciudad_tecnico=='ARMENIA') echo "selected"; ?>>ARMENIA</option>
	<option value="BARRANCABERMEJA" <? if($ciudad_tecnico=='BARRANCABERMEJA') echo "selected"; ?>>BARRANCABERMEJA</option>
	<option value="BARRANQUILLA" <? if($ciudad_tecnico=='BARRANQUILLA') echo "selected"; ?>>BARRANQUILLA</option>
	<option value="BOGOTA" <? if($ciudad_tecnico=='BOGOTA') echo "selected"; ?>>BOGOTA</option>
	<option value="BUCARAMANGA" <? if($ciudad_tecnico=='BUCARAMANGA') echo "selected"; ?>>BUCARAMANGA</option>
	<option value="BUGA" <? if($ciudad_tecnico=='BUGA') echo "selected"; ?>>BUGA</option>
	<option value="CALI" <? if($ciudad_tecnico=='CALI') echo "selected"; ?>>CALI</option>
	<option value="CARTAGENA" <? if($ciudad_tecnico=='CARTAGENA') echo "selected"; ?>>CARTAGENA</option>
	<option value="CUCUTA" <? if($ciudad_tecnico=='CUCUTA') echo "selected"; ?>>CUCUTA</option>
	<option value="IBAGE" <? if($ciudad_tecnico=='IBAGE') echo "selected"; ?>>IBAGE</option>
	<option value="MANIZALES" <? if($ciudad_tecnico=='MANIZALES') echo "selected"; ?>>MANIZALES</option>
	<option value="NEIVA" <? if($ciudad_tecnico=='NEIVA') echo "selected"; ?>>NEIVA</option>
	<option value="PASTO" <? if($ciudad_tecnico=='PASTO') echo "selected"; ?>>PASTO</option>
	<option value="PEREIRA" <? if($ciudad_tecnico=='PEREIRA') echo "selected"; ?>>PEREIRA</option>
	<option value="POPAYAN" <? if($ciudad_tecnico=='POPAYAN') echo "selected"; ?>>POPAYAN</option>
	<option value="SANTAMARTA" <? if($ciudad_tecnico=='SANTAMARTA') echo "selected"; ?>>SANTAMARTA</option>
	<option value="TUNJA" <? if($ciudad_tecnico=='TUNJA') echo "selected"; ?>>TUNJA</option>
	<option value="PALMIRA" <? if($ciudad_tecnico=='PALMIRA') echo "selected"; ?>>PALMIRA</option>
	<option value="VILLAVICENCIO" <? if($ciudad_tecnico=='VILLAVICENCIO') echo "selected"; ?>>VILLAVICENCIO</option>
</select>
</td>
</tr>

<tr>
<td>Empresa</td><td>

<select id="empresa_ingreso">
	
<?
	for($i=0;$i<$rows;$i++){
		$id = pg_result($result,$i,0);
		$nombre = pg_result($result,$i,1);
		$selected="";
		if($id==$empresa_tecnico){
			$selected=" selected ";
		}
		echo "<option value='$id' $selected>$nombre</option>";
	}
?>

</select>
<!--a href="javascript:mostrarFormaIngreso();">Ingresar empresa</a-->
</td>
</tr>
<!--tr>
<td colspan="2">
<div id="divIngresoEmpresa" style="position:absolute;visibility:hidden;">
Empresa:<input type="text" id="nueva_empresa"><input type="button" name="nueva" value="Ingresar" onclick="javascript:ingresarEmpresa();">
</div>
</td>
</tr-->
</table>

<input type="button" name="Ingresar" value="Modificar" onclick="javascript:doModificarTecnico('<? echo $id_tecnico; ?>');">
<input type="button" name="cancelar" value="Cancelar" onclick="javascript:Modalbox.hide();return false;">
<!--a href="#" title="close" onclick="javascript:Modalbox.hide();return false;">close me</a>
<a href="#" title="close" onclick="javascript:hi();">Hi man!!!!</a-->
<script language="JavaScript">
 document.getElementById("nombre_tecnico").focus();
</script>
</body>
</html>
