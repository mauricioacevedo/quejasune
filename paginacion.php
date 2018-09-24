<?
if (!isset($NumeroTotalRows)) {
	echo '<h1>ERROR: Falta la variable NumeroTotalRows</h1>';
	exit;
}

$registrosPagina = $HTTP_GET_VARS["txtRegistrosPagina"];
// echo "registrosPagina: $registrosPagina<br>";
if (isset($registrosPagina)) {
	if (is_numeric($registrosPagina)) {
	 	if ($registrosPagina <= 0) $registrosPagina = 100;
		if ($registrosPagina > $NumeroTotalRows) $registrosPagina = $NumeroTotalRows;
	 } else {
	 	$registrosPagina = 100;
	 }
} else {
	$registrosPagina = 100;
}

$paginaActual = $HTTP_GET_VARS["pagina"];
if (!isset($paginaActual)) $paginaActual = 1;
//echo 'pagina actual '.$paginaActual;


$pagina =$HTTP_GET_VARS["pagina"];
#echo 'pagina  '.$pagina;
if ($pagina == "") $pagina = 1; 

if ($registrosPagina != 0 || $registrosPagina != "0")
	$cantidadPaginas = ceil($NumeroTotalRows / $registrosPagina);
if ($paginaActual > $cantidadPaginas) $paginaActual = $cantidadPaginas;

//$comando = $HTTP_GET_VARS["cmdComando"];
//if (isset($comando)) {
//	if ($comando == ">") $paginaActual++;
//	if ($comando == "<") {
//		if ($paginaActual > 1) $paginaActual--;
//	}
//	if ($comando == "<<") {
//		$paginaActual = 1;
//	}
//	if ($comando == ">>") {
//		$paginaActual = $cantidadPaginas;
//	}
//	
//}

$offset = ($paginaActual - 1) * $registrosPagina;
if ($offset < 0) $offset = 0;
#echo "offset es: $offset";
//echo "LA PAgina ACtual es : $paginaActual";
?>

<form name="frmPaginacion">
<input type="hidden" name="paginaActual" value="<? echo $paginaActual ?>">
<?php
foreach (array_keys($HTTP_GET_VARS) as $key) {
	if (!in_array($key, array("paginaActual", "cmdComando", "txtRegistrosPagina", "cmdRegistrosPagina", "cboIrPagina")))
		echo "<input type=\"hidden\" name=\"$key\" value=\"".$HTTP_GET_VARS[$key]."\">\n";
}
?>
<table bgcolor="#FFFFFF" width="100%">

  <tbody>
    <tr>
      <td colspan="6">Encontrados <b><? echo $NumeroTotalRows ?></b> Registros. </td>
    </tr>
    <tr>
      <? 
	   	$styleAtras = $paginaActual == 1?"disabled":""; 
		$styleAdelante = $paginaActual == $cantidadPaginas?"disabled":""; 
	  ?>
	  <td> <input type="button" name="cmdComando" value="<<" onclick="javascript:irAPagina('1',document.frmPaginacion.txtRegistrosPagina.value);" <? echo $styleAtras; ?>  class="btnpurple"> </td>
      <td> <input type="button" name="cmdComando" value="<" onclick="javascript:irAPagina('<? echo ($paginaActual -1);  ?>',document.frmPaginacion.txtRegistrosPagina.value);"  <? echo $styleAtras; ?>  class="btnpurple"> </td>
      <td> <input type="button" name="cmdComando" value=">" onclick="javascript:irAPagina('<? echo ($paginaActual +1);  ?>',document.frmPaginacion.txtRegistrosPagina.value);"  <? echo $styleAdelante; ?>  class="btnpurple"> </td>
      <td> <input type="button" name="cmdComando" value=">>" onclick="javascript:irAPagina('<? echo $cantidadPaginas;  ?>',document.frmPaginacion.txtRegistrosPagina.value);"  <? echo $styleAdelante; ?>  class="btnpurple"> </td>
      <td> 
	  	Registros/P&aacute;gina:
			<input type="text" name="txtRegistrosPagina" size="3" value="<? echo $registrosPagina ?>" style="background-color: rgb(255, 255, 160);">
			<input type="button" name="cmdRegistrosPagina" value="Aceptar" onclick="javascript:irAPagina('<? echo $paginaActual; ?>',document.frmPaginacion.txtRegistrosPagina.value);" class="btnpurple">
	  </td>
	  <td> Ir a la P&aacute;gina: 
	  	<select name="cboIrPagina" onchange="javascript:irAPagina(document.frmPaginacion.cboIrPagina.value,document.frmPaginacion.txtRegistrosPagina.value);">
			<?
			for ($i = 1; $i <= $cantidadPaginas; $i++) {
				$selected = ($paginaActual == $i)?" selected":"";
				echo "<option$selected>$i</option>\n";
			}
			?>
		</select> 
	  </td>
    </tr>
  </tbody>
</table>
</form>
