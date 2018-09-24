<html>
<head>
<script language='javascript'>

function login(){
	var usuario=document.getElementById("usuario").value;
	var contrasena=document.getElementById("contrasena").value;
	
	contrasena=contrasena.replace("+","%2B");
	
	var url="./inicio.php?user="+usuario+"&pwd="+contrasena;
        location.href=url;

}

</script>
<link rel="stylesheet" href="javascript/quejas.css" type="text/css" />

</head>
<body>
<center><img src="img/banner.png" height="141" width="888"></center>
<br>
<center><h2>Aplicacion de Quejas</h2></center>

<br>
<br>
<form action="javascript:login();">
<table align='center'>
<tr>
<td>Usuario:</td><td><input type='text' name='usuario' id='usuario' size='15'></td>
</tr>
<tr>
<td>Contraseña:</td><td><input type='password' name='contrasena' id='contrasena' size='15'></td>
</tr>
<tr>
<td colspan="2" align='center'><input type='button' class="btnpurple" value='Ingresar' onclick='javascript:login();'></td>
</tr>
</table>
</form>
</body>
</html>
