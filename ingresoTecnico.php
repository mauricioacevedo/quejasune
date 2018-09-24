<?php
	session_start();
	include_once('conexion.php');
	$id_tecnico=$HTTP_GET_VARS["id_tecnico"];
	$operacion=$HTTP_GET_VARS["operacion"];
	
	$conexion_bd = getConnection();
	
	$sql="select id,nombre from empresas order by nombre ASC";
	$result = pg_query($sql);
	$rows=pg_numrows($result);
	
?>

<html>
<head>
<link rel="stylesheet" href="javascript/quejas.css" type="text/css" />
</head>
<body>
<h2>Ingreso de Tecnicos</h2>
<p>El tecnico <font color="red"><b><? echo $id_tecnico; ?></b></font> no existe en nuestros registros, por favor hacer el ingreso de la informacion. </p>
<table>
<tr>
<td>Identificacion</td><td><input type="text" value="<? echo $id_tecnico; ?>" id="id_tecnico_ingreso"></td>
</tr>
<tr>
<td>Nombre</td><td><input type="text" value="" id="nombre_tecnico"></td>
</tr>

<tr>
<td>Ciudad</td><td>
<select id="ciudad">
	<option value="MEDELLIN">MEDELLIN</option>
	<option value="ARMENIA">ARMENIA</option>
	<option value="BARRANCABERMEJA">BARRANCABERMEJA</option>
	<option value="BARRANQUILLA">BARRANQUILLA</option>
	<option value="BOGOTA">BOGOTA</option>
	<option value="BUCARAMANGA">BUCARAMANGA</option>
	<option value="BUGA">BUGA</option>
	<option value="CALI">CALI</option>
	<option value="CARTAGENA">CARTAGENA</option>
	<option value="CUCUTA">CUCUTA</option>
	<option value="IBAGE">IBAGE</option>
	<option value="MANIZALES">MANIZALES</option>
	<option value="NEIVA">NEIVA</option>
	<option value="PASTO">PASTO</option>
	<option value="PEREIRA">PEREIRA</option>
	<option value="POPAYAN">POPAYAN</option>
	<option value="SANTAMARTA">SANTAMARTA</option>
	<option value="TUNJA">TUNJA</option>
	<option value="PALMIRA">PALMIRA</option>
	<option value="VILLAVICENCIO">VILLAVICENCIO</option>
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
		echo "<option value='$id'>$nombre</option>";
	}
?>

</select>
<!--a href="javascript:mostrarFormaIngreso();">Ingresar empresa</a-->
</td>
</tr>
<!--tr>
<td colspan="2">
<div id="divIngresoEmpresa" style="position:absolute;visibility:hidden;">
Empresa:<input type="text" id="nueva_empresa"><input type="button" class="btnpurple" name="nueva" value="Ingresar" onclick="javascript:ingresarEmpresa();">
</div>
</td>
</tr-->
</table>

<input type="button" class="btnpurple" name="Ingresar" value="Ingresar" onclick="javascript:ingresarTecnico();">
<input type="button" class="btnpurple" name="cancelar" value="Cancelar" onclick="javascript:Modalbox.hide();return false;">
<!--a href="#" title="close" onclick="javascript:Modalbox.hide();return false;">close me</a>
<a href="#" title="close" onclick="javascript:hi();">Hi man!!!!</a-->
<script language="JavaScript">
 document.getElementById("nombre_tecnico").focus();
</script>
</body>
</html>
