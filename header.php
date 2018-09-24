<table width="100%">
<tr><td colspan="3">
<center><img src="img/banner.png" height="141" width="888"></center>
</td></tr>

<tr><td align="left"><nav><?

        $the_matrix=$_SESSION["the_matrix"];
        $counterForm=$_SESSION["counter_form"];

	$size=count($the_matrix);

	if($counterForm=="" or $nombreForma=="Opciones"){
		//echo "COUNTER=0;<br>";
		$counterForm=0;
	}
	if($the_matrix=="" or $nombreForma=="Opciones"){
		//echo "MATRIX EMPTY;<br>";
		$the_matrix=array();
	}

	$counter2=$counterForm;
	for($i=0;$i<$counterForm;$i++){
		$inForm=$the_matrix[$i];
		$nameForm=$inForm["nombreForma"];
		$linki=$inForm["linkForma"];
		
		//echo "$sep<a href='$linki' style='color: red;text-decoration:none;'>$nameForm</a>";
		//$sep="|";
		//echo "nameForm: $nameForm, nombreForma: $nombreForma<br>";
		if($nameForm==$nombreForma){//esta pantalla ya esta en la matrix
			//debo eliminar de la matriz todos los elementos de aca en adelante..
		 	$matrix_reload=array();
			for($j=0;$j<$i;$j++){
				$inForm1=$the_matrix[$j];
				$nameForm2=$inForm1["nombreForma"];
				$linki2=$inForm1["linkForma"];

				//echo "nombre forma: $nameForm2, linkforma: $linki2<br>";


				$matrix_reload[$j]=$inForm1;
			}
			
			$the_matrix=$matrix_reload;
			$counterForm=$i;
			$i=$counter2+1;
			break;
		}
		echo "$sep<a href='$linki' style='color: red;text-decoration:none;'>$nameForm</a>";
                $sep="|";
               // echo "nameForm: $nameForm, nombreForma: $nombreForma<br>";

	
	}
		
		echo "$sep<font color='red'><b>$nombreForma</b></font>";

		if($the_matrix=="" or $nombreForma=="Opciones") $the_matrix=array();
		
		$inForm=array();
		$inForm["nombreForma"]=$nombreForma;
		$inForm["linkForma"]=$linkForma;
		$the_matrix[$counterForm]=$inForm;
		$counterForm=$counterForm+1;
	
		$_SESSION["the_matrix"]= $the_matrix;
		$_SESSION["counter_form"]=$counterForm;

?></nav></td><td align="right"><span><b>Usuario: <font color='blue'><? echo $_SESSION["username"]; ?></font></b></span></td></tr>
</table>
<br>
