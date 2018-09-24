<?php 
	session_start();
	include_once('conexion.php');
	$conexion_bd = getConnection();
	
	$operacion=$_GET["operacion"];

	if($operacion!=""){//hay una accion a realizar
	
		if($operacion=="editar"){
			$id=$_GET["id"];
			
			$sql="select zona,correos,con_copia from zonas_de_correo where id=$id";
			$result = pg_query($sql);
        		$rows=pg_numrows($result);
			if($rows>0){
				$zona=pg_result($result,$i,0);
                		$correos=pg_result($result,$i,1);
                		$copia=pg_result($result,$i,2);

			}else{//hay problemas, retorno al listado de contratistas
				$msg = "No se encontro en la base de datos el contratista con id: $id";
				header("Location: ./mod_contratistas_actividades.php?msg=$msg");
				return;
			}
			

		}else if($operacion=="insertarContratista"){
			$zona=$_GET["zona"];
			$correos=$_GET["correos"];
			$conCopia=$_GET["conCopia"];

			$sql="insert into zonas_de_correo (zona,correos,con_copia) values ('$zona','$correos','$conCopia')";
			
			$result = pg_query($sql);

			$msg = "Operacion realizada con exito.";
                        header("Location: ./mod_contratistas_actividades.php?msg=$msg");
			return;

		}else if($operacion=="editarContratista"){
			$zona=$_GET["zona"];
                        $correos=$_GET["correos"];
                        $conCopia=$_GET["conCopia"];
			$id=$_GET["id"];

			$sql="update zonas_de_correo set zona='$zona',correos='$correos',con_copia='$conCopia' where id=$id";
			
			$result = pg_query($sql);
			$msg = "Operacion realizada con exito.";
                        header("Location: ./mod_contratistas_actividades.php?msg=$msg");

			return;
                }else if($operacion=="eliminar"){
			$id=$_GET["id"];

			$sql="delete from zonas_de_correo where id=$id";
			$result = pg_query($sql);
			
			$msg = "Operacion realizada con exito.";
                        header("Location: ./mod_contratistas_actividades.php?msg=$msg");
                        
                        return;

		}
	
	}

	$sql="select zonas,correos,con_copia,id from zonas_de_correo";

	$result = pg_query($sql);
	$rows=pg_numrows($result);

        $nombreForma="Agregar Contratista";
        $linkForma="./agregarContratistaActividades.php";


?>


<html>
<head>
<title>Contratistas de Actividades</title>
<script language="javascript">
	function editar(id){
		location.href="./agregarContratistaActividades.php?operacion=editar&id="+id;
	}
	
        function eliminar(id){
                location.href="./agregarContratistaActividades.php?operacion=eliminar&id="+id;

	}

	function guardarInformacion(){
                var zona=document.getElementById("zona").value;
                var correos=document.getElementById("correos").value;
                var conCopia=document.getElementById("conCopia").value;

                var request="&zona="+zona+"&correos="+correos+"&conCopia="+conCopia;
                location.href="./agregarContratistaActividades.php?operacion=insertarContratista"+request;
        }

        function editarInformacion(){
                var zona=document.getElementById("zona").value;
                var correos=document.getElementById("correos").value;
                var conCopia=document.getElementById("conCopia").value;
		
                var request="&zona="+zona+"&correos="+correos+"&conCopia="+conCopia;
                location.href="./agregarContratistaActividades.php?operacion=editarContratista"+request+"&id=<? echo $id; ?>";
        }

</script>

        <!-- STUFF YOU NEED FOR BEAUTYTIPS -->
<script src="./js/jquery-1.3.2.min.js" type="text/javascript" charset="utf-8"></script>
<script src="./js/jquery.hoverIntent.minified.js" type="text/javascript" charset="utf-8"></script>
<script src="./js/jquery.bgiframe.min.js" type="text/javascript" charset="utf-8"></script>
      <!--[if IE]><script src="./js/excanvas.js" type="text/javascript" charset="utf-8"></script><![endif]-->
<script src="./js/jquery.bt.min.js" type="text/javascript" charset="utf-8"></script>
        <!-- /STUFF -->
        
        <!-- cool easing stuff for animations -->
<script src="./js/jquery.easing.1.3.js" type="text/javascript" charset="utf-8"></script>
        <!-- /easing -->
        
        <!-- just for demo -->
  <!--[if IE]><link href="demofiles/demo-ie-fix.css" rel="stylesheet" type="text/css"><![endif]-->
  <!-- /demo stuff -->

</head>
<body>
<? include 'header.php'; ?>
<center><h2>Agregar/Editar Contratistas Quejas</h2></center>
<br>
<form action="javascript:<? if($operacion=="editar"){echo "editar";}else{echo "guardar";} ?>Informacion();">
<table align="center" align="center">
<tbody> 
<tr bgcolor="black">
<td align="center"><font color="white"><b>Campo</b></font></td>
<td align="center"><font color="white"><b>Valor</b></font></td>
</tr>
<tr>
<td align="center" bgcolor="#f5f5f5"><b>Zona</b></td>
<td align="center">

<input type="text" id="zona" value="<? echo $zona; ?>" size="30" title="Zona de correo." class="alt-target">
</td>
</tr>
<tr>
<td align="center" bgcolor="#f5f5f5"><b>Correos Destinatarios</b></td>
<td align="center">

<textarea id="correos" rows="3" cols="38" title="Correos electronicos de los destinatarios.Los correos deben ir separados por el <font color='red'>caracter coma (,)</font>" class="alt-target"><? echo $correos; ?></textarea>

</td>
</tr>
<tr>
<td align="center" bgcolor="#f5f5f5"><b>Correos Con Copia</b></td>
<td align="center"><input type="text" id="conCopia" value="<? echo $copia; ?>" size="30" title="Correos electronicos a las personas que se desea copiar el correo." class="alt-target"></td>
</tr>
</tbody></table>
<br>

<center><input value="Guardar" type="submit">&nbsp;<input value="Cancelar" type="button" onclick="javascript:location.href='./mod_contratistas_actividades.php';"></center>

</form>
<br>
<a href="javascript:disableHelp();">Disable Help</a>
<script language="javascript">
$('#zona,#correos,#conCopia').bt({
  cornerRadius: 10,
  strokeWidth: 4,
  strokeStyle: 'red',
  padding: 20,
  cssStyles: {color: '#111111', fontWeight: 'bold'},
  fill: 'rgba(254, 254, 254, .9)',
  trigger: ['focus', 'blur'],
  positions: ['right'],
  showTip: function(box){
    var $content = $('.bt-content', box).hide(); /* hide the content until after the animation */
    var $canvas = $('canvas', box).hide(); /* hide the canvas for a moment */
    var origWidth = $canvas[0].width; /* jQuery's .width() doesn't work on canvas element */
    var origHeight = $canvas[0].height;
    $(box).show(); /* show the wrapper, however elements inside (canvas, content) are now hidden */
    $canvas
      .css({width: origWidth * .25, height: origHeight * .25, left: origWidth * .25, top: origHeight * .25, opacity: .1 })
      .show()
      .animate({width: origWidth, height: origHeight, left: 0, top: 0, opacity: 1}, 400, 'easeOutBounce',
        function(){$content.show()} /* show the content when animation is done */
        );
  },
hideTip: function(box, callback){
    var $content = $('.bt-content', box).hide();
    var $canvas = $('canvas', box);
    var origWidth = $canvas[0].width;
    var origHeight = $canvas[0].height;
    $canvas
      .animate({width: origWidth * .5, height: origHeight * .5, left: origWidth * .25, top: origHeight * .25, opacity: 0}, 400, 'swing', callback); /* callback */
  },
 /* other options */
  shrinkToFit: true,
  width: "250px", 
  hoverIntentOpts: {
    interval: 0,
    timeout: 0
  }
});
//$('#jefeGrupo').bt({});
</script>
<script language="javascript">
function disableHelp() {
        $(document).ready(function(){
            $("#zona,#correos,#conCopia").bt({
			trigger: [''],
			showTip: function(box){ var $content = $('.bt-content', box).hide(); }
		});
        });
    }

</script>
</body>
</html>
