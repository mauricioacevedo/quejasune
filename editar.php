<?php 

	session_start();
	include_once('conexion.php');
	
	$conexion_bd = getConnection();
	$operacion=$HTTP_GET_VARS["operacion"];



	if($operacion=="buscarTecnico"){
		$identificacion=$HTTP_GET_VARS["identificacion"];
		$sql="select nombre,(select a.nombre from empresas a where a.id=empresa) as empresa,ciudad from tecnicos where identificacion='$identificacion'";
		$result = pg_query($sql);
	        $rows=pg_numrows($result);
		
	        if($rows<=0){
                //devolver a pagina inicial con mensaje
                	echo "NO;$identificacion;$sql";
        	} else {
          		$nombre=pg_result($result,0,0);
			$nombre_empresa=pg_result($result,0,1);
			$ciudad=pg_result($result,0,2);
			//echo $nombre_empresa;
                	echo "SI;$nombre;$identificacion;$nombre_empresa;$ciudad";
        	}
		return;

	}
	if($operacion=="ingresarTecnico"){
		$identificacion=$HTTP_GET_VARS["identificacion"];
		$nombre=$HTTP_GET_VARS["nombre"];
		$empresa=$HTTP_GET_VARS["empresa"];
		$ciudad=$HTTP_GET_VARS["ciudad"];
		$asesorEditor=$_SESSION['login'];
	
		$sql="insert into tecnicos(identificacion,nombre,empresa,ciudad,editor) values ('$identificacion','$nombre',$empresa,'$ciudad','$asesorEditor');";
	
		//echo $sql;
	
		$result = pg_query($sql);
		
		$sql="select nombre from empresas where id=$empresa";
		$result = pg_query($sql);
		$rows=pg_numrows($result);
		
		if($rows<=0){
                //devolver a pagina inicial con mensaje
                	echo "NO;$sql;ERROR";
        	} else {
          		$nombre_empresa=pg_result($result,0,0);
                	echo "SI;$nombre;$identificacion;$nombre_empresa;$ciudad";
        	}

		//echo "SI;$nombre;$identificacion;$empresa";
		return;
	}


        if($operacion=="doModificarTecnico"){
                $identificacion=$HTTP_GET_VARS["identificacion"];
                $nombre=$HTTP_GET_VARS["nombre"];
                $empresa=$HTTP_GET_VARS["empresa"];
                $ciudad=$HTTP_GET_VARS["ciudad"];
		$oldIdentificacion=$HTTP_GET_VARS["old_identificacion"];		
		$fecha=date("Y")."-".date("m")."-".date("d")." ".date("H").":".date("i").":".date("s");
		$asesorEditor=$_SESSION['login'];

		$sql="update tecnicos set identificacion='$identificacion',nombre='$nombre',empresa='$empresa',ciudad='$ciudad',fecha='$fecha',editor='$asesorEditor' where identificacion='$oldIdentificacion'";
                //$sql="insert into tecnicos(identificacion,nombre,empresa,ciudad) values ('$identificacion','$nombre',$empresa,'$ciudad');";

                $result = pg_query($sql);

                $sql="select nombre from empresas where id=$empresa";
                $result = pg_query($sql);
                $rows=pg_numrows($result);

                if($rows<=0){
                //devolver a pagina inicial con mensaje
                        echo "NO;$sql;ERROR";
                } else {
                        $nombre_empresa=pg_result($result,0,0);
                        echo "SI;$nombre;$identificacion;$nombre_empresa;$ciudad";
                }

                //echo "SI;$nombre;$identificacion;$empresa";
                return;
        }


	
	if($operacion=="ingresarEmpresa"){
		$nombre=$HTTP_GET_VARS["nombre"];
		
		$sql="insert into empresas(nombre) values ('$nombre');";
		$result = pg_query($sql);

		echo "OK;$nombre";
		return;
	}

	if($operacion=="actualizarRegistro"){
		
		$pedido=trim($HTTP_GET_VARS["pedido"]);
		$id_tecnico=trim($HTTP_GET_VARS["id_tecnico"]);
		$nombre_de_la_empresa=$HTTP_GET_VARS["nombre_de_la_empresa"];
		$login_del_asesor=trim($HTTP_GET_VARS["login_del_asesor"]);
		$observaciones=$HTTP_GET_VARS["observaciones"];
		$accion=$HTTP_GET_VARS["accion"];
		$hora_compromiso=$HTTP_GET_VARS["horaCompromiso"];
		$id=$HTTP_GET_VARS["id"];

		$_SESSION['login'] = $login_del_asesor;

		$initTime=$_SESSION['initTime'];


		$fechaFin=date("Y")."-".date("m")."-".date("d")." ".date("H").":".date("i").":".date("s");
		if($initTime==""){//le doy un valor a la fecha inicial, pero tener en cuenta que en este caso no consultaron el pedido.
			$initTime=$fechaFin;	
		}


		//antes de hacer la insercion remuevo caracteres extranos de las observaciones:
		$foreign_chars=array("%","@","'","\"","<",">");
		$observaciones=str_replace($foreign_chars,"",$observaciones);

		$sql="update registros set numero_queja='$pedido', id_tecnico='$id_tecnico',empresa='$nombre_de_la_empresa', asesor=upper('$login_del_asesor'), observaciones='$observaciones', accion='$accion', fecha_compromiso='$hora_compromiso', fecha='$fechaFin' where id=$id";		

		//echo $sql;
		//pg_send_query($conexion_bd,$sql);
		//$res = pg_get_result($conexion_bd);
		$result = pg_query($sql);
		
		//$error= pg_last_error($conexion_bd);

		//echo "--->$res";
	
		$msg="Registro actualizado con EXITO!";
		$_SESSION['initTime']="";
		header("Location: ./registros.php?msg=$msg");

		return;
	}

	if($operacion=="mostrarPlantilla"){
		$msg=$HTTP_GET_VARS["msg"];
	}

	if($operacion=="getTimestamp"){
		echo "<script>alert('gettimestamp!!!');</script>";
		$fecha=date("Y")."-".date("m")."-".date("d")." ".date("H").":".date("i").":".date("s");
		$_SESSION['initTime']=$fecha;
		echo "OK;$fecha";
                return;
        }
      if($operacion=="editarRegistro"){
                $id=$HTTP_GET_VARS["id"];

                $sql="select a.numero_queja,a.id_tecnico,(select nombre from tecnicos where identificacion=a.id_tecnico),(select ciudad from tecnicos where identificacion=a.id_tecnico) as ciudad,a.empresa,a.asesor,a.observaciones,a.accion,a.fecha_compromiso,a.duracion from registros  a where a.id=$id";
                //echo $sql;
                $result = pg_query($sql);
                $rows=pg_numrows($result);

                if($rows<1){
                        header("./registros.php?msg=Ocurrio un error con el registro que se intento editar.");
                        return;
                }

                $numero_queja=pg_result($result,0,0);
                $id_tecnico=pg_result($result,0,1);
                $nombre_tecnico=pg_result($result,0,2);
                $ciudad=pg_result($result,0,3);
                $empresa=pg_result($result,0,4);
                $asesor=pg_result($result,0,5);
                $observaciones=pg_result($result,0,6);
                $accion=pg_result($result,0,7);
                $fecha_compromiso=pg_result($result,0,8);
                $duracion=pg_result($result,0,9);

                //$_SESSION['initTime'] = date("Y")."-".date("m")."-".date("d")." ".date("H").":".date("i").":".date("s");
		
		$fecha_array=split(" ",$fecha_compromiso);
		$fecha_compromiso=$fecha_array[0];
		$hora_compromiso=$fecha_array[1]." ".$fecha_array[2];
		//echo "fecha: $fecha_compromiso, hora: $hora_compromiso";
                //echo "pedido: $pedido";

        }

?>


<HTML>
<title>Editar</title>
<HEAD>

<META http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <script type="text/javascript" src="./javascript/modalbox/lib/prototype.js"></script>
        <script type="text/javascript" src="./javascript/modalbox/lib/scriptaculous.js?load=effects"></script>

        <script type="text/javascript" src="javascript/modalbox/modalbox.js"></script>
        <link rel="stylesheet" href="javascript/modalbox/modalbox.css" type="text/css" />
	<script type="text/javascript" src="./javascript/jquery.min.js"></script>
	<script type="text/javascript" src="./javascript/jquery.blockUI.js?v2.38"></script>	

        <link rel="stylesheet" href="./css/jquery.ui.all.css">
        <script type="text/javascript" src="./css/jquery.ui.core.js"></script>
        <script type="text/javascript" src="./css/jquery.ui.widget.js"></script>
        <script type="text/javascript" src="./css/jquery.ui.datepicker.js"></script>
        <link rel="stylesheet" href="./css/demos.css">

	<link rel="stylesheet" href="javascript/quejas.css" type="text/css" />	
	
	<script>
	     jQuery.noConflict();
	</script>

        <style type="text/css" media="screen">
                html, body {
                        width: 100%;
                        height: 100%;
                }
                #MB_loading {
                        font-size: 13px;
                }
                #errmsg {
                        margin: 1em;
                        padding: 1em;
                        color: #C30;
                        background-color: #FCC;
                        border: 1px solid #F00;
                }
        </style>

<TITLE>Seguimiento de Quejas</TITLE>

<script language="javascript">

function rex(stringInput){

        var specialChars = "!$^&%*()=[]\/{}|<>?";
        for (var i = 0; i < specialChars.length; i++) {
                stringInput = stringInput.replace(new RegExp("\\" + specialChars[i], 'gi'), '');
        }
        return stringInput;
}


	function guardarDatos(){
		var pedido = document.getElementById('pedido');
		pedido = pedido.value;

		var id_tecnico = document.getElementById('id_tecnico');
		id_tecnico = id_tecnico.value;


		var nombre_de_la_empresa = document.getElementById('nombre_de_la_empresa');
		nombre_de_la_empresa = nombre_de_la_empresa.value;
		var login_del_asesor = document.getElementById('login_del_asesor');
		login_del_asesor = login_del_asesor.value;
		var observaciones = document.getElementById('observaciones');
		observaciones = observaciones.value;
		observaciones = rex(observaciones);
		var accion = document.getElementById('accion');
		accion = accion.value;
		
		var hidden=document.getElementById("initTime");
                var df=new Date().getTime() - hidden.value;
                df=new Date(df);

		var ciudad=document.getElementById("city").innerHTML;

		var horaCompromiso='';
		if(accion=="Compromisos"){
			horaCompromiso=document.getElementById("fechaCompromiso").value;
			horaCompromiso=horaCompromiso+" "+document.getElementById("horaCompromiso").value;
		}else{
			horaCompromiso="";
		}

		//validaciones sobre campos
		if(accion!="Llamada Caida"){
			if(pedido==""){
				alert("Por favor ingrese un numero de queja.");
				document.getElementById('pedido').focus();
				return;
			}
			if(id_tecnico==""){
				alert("por favor ingrese la identificacion del tecnico");
				document.getElementById('id_tecnico').focus();
				return;
			}
			if(nombre_de_la_empresa==""){
				alert("Recuerde que debe seleccionar un tecnico de la base de datos o crearlo si no existe.");
				document.getElementById('id_tecnico').focus();
				return;
			}
		}else{
			//pedido="";
			//id_tecnico="";
			//nombre_de_la_empresa="";
		}

		if(login_del_asesor==""){
			alert("por favor ingrese su login");
			document.getElementById('login_del_asesor').focus();
			return;
		}


		var id = '<? echo $id; ?>';
		var request='&pedido='+pedido+'&id_tecnico='+id_tecnico+'&nombre_de_la_empresa='+nombre_de_la_empresa+'&login_del_asesor='+login_del_asesor+'&accion='+accion+'&horaCompromiso='+horaCompromiso+'&observaciones='+observaciones+'&id='+id;

		//esto es para evitar el dialog que trata de confirmar si el usuario en realidad desea salir del sitio..
		var hidden2=document.getElementById("inicioLlamada");
                hidden2.value="false";
		location.href='./editar.php?operacion=actualizarRegistro'+request;
	}

	//AJAX
	function buscarTecnico(){
		//hideMsg();
		var identificacion=document.getElementById("id_tecnico").value;
		if(identificacion==""){
			alert("Por favor ingrese identificacion del tecnico.");
			document.getElementById("id_tecnico").focus();
			return;
		}
	      	http_request = false;
      		if (window.XMLHttpRequest) { // Mozilla, Safari,...
         		http_request = new XMLHttpRequest();
         	if (http_request.overrideMimeType) {
                // set type accordingly to anticipated content type
            	//http_request.overrideMimeType('text/xml');
            		http_request.overrideMimeType('text/html');
         	}
      		} else if (window.ActiveXObject) { // IE
         		try {
            			http_request = new ActiveXObject("Msxml2.XMLHTTP");
         		} catch (e) {
            			try {
               				http_request = new ActiveXObject("Microsoft.XMLHTTP");
            			} catch (e) {}
         		}
      		}
      		if (!http_request) {
         		alert('Cannot create XMLHTTP instance');
         		return false;
      		}	

		
		var url="./editar.php?operacion=buscarTecnico&identificacion="+identificacion;
                http_request.onreadystatechange = recuperarRespuestaBusquedaTecnico;
      		
      		http_request.open('GET', url, true);
      		http_request.send(null);

	
	}
	
	function modificarInformacionTecnico(){
		var identificacion=document.getElementById("id_tecnico").value;
		Modalbox.show('modificarTecnico.php?id_tecnico='+identificacion, {title: 'Ingreso de Tecnicos', height: 400, width: 500 });
		
	}

	function ingresarTecnico(){

		var nombre=document.getElementById("nombre_tecnico").value;
		var identificacion=document.getElementById("id_tecnico_ingreso").value;
		var empresa=document.getElementById("empresa_ingreso").value;
		nombre=nombre.toUpperCase();
		var ciudad=document.getElementById("ciudad").value;
                http_request = false;
                if (window.XMLHttpRequest) { // Mozilla, Safari,...
                        http_request = new XMLHttpRequest();
                if (http_request.overrideMimeType) {
                // set type accordingly to anticipated content type
                //http_request.overrideMimeType('text/xml');
                        http_request.overrideMimeType('text/html');
                }
                } else if (window.ActiveXObject) { // IE
                        try {
                                http_request = new ActiveXObject("Msxml2.XMLHTTP");
                        } catch (e) {
                                try {
                                        http_request = new ActiveXObject("Microsoft.XMLHTTP");
                                } catch (e) {}
                        }
                }
                if (!http_request) {
                        alert('Cannot create XMLHTTP instance');
                        return false;
                }


                //var url="${pageContext.request.contextPath}/action?accion=gestorReportes&operacion=guardarHorasAjax"+parametros;
                var url="./editar.php?operacion=ingresarTecnico&identificacion="+identificacion+"&nombre="+nombre+"&empresa="+empresa+"&ciudad="+ciudad;
                http_request.onreadystatechange = recuperarRespuestaIngresoTecnico;

                http_request.open('GET', url, true);
                http_request.send(null);

	}

	function doModificarTecnico(old_identificacion){
		
                var nombre=document.getElementById("nombre_tecnico").value;
                var identificacion=document.getElementById("id_tecnico_ingreso").value;
                var empresa=document.getElementById("empresa_ingreso").value;
                nombre=nombre.toUpperCase();
                var ciudad=document.getElementById("ciudad").value;
                http_request = false;
                if (window.XMLHttpRequest) { // Mozilla, Safari,...
                        http_request = new XMLHttpRequest();
                if (http_request.overrideMimeType) {
                // set type accordingly to anticipated content type
                //http_request.overrideMimeType('text/xml');
                        http_request.overrideMimeType('text/html');
                }
                } else if (window.ActiveXObject) { // IE
                        try {
                                http_request = new ActiveXObject("Msxml2.XMLHTTP");
                        } catch (e) {
                                try {
                                        http_request = new ActiveXObject("Microsoft.XMLHTTP");
                                } catch (e) {}
                        }
                }
                if (!http_request) {
                        alert('Cannot create XMLHTTP instance');
                        return false;
                }


                //var url="${pageContext.request.contextPath}/action?accion=gestorReportes&operacion=guardarHorasAjax"+parametros;
                var url="./editar.php?operacion=doModificarTecnico&identificacion="+identificacion+"&nombre="+nombre+"&empresa="+empresa+"&ciudad="+ciudad+"&old_identificacion="+old_identificacion;
                http_request.onreadystatechange = recuperarRespuestaIngresoTecnico;

                http_request.open('GET', url, true);
                http_request.send(null);

	}


	function recuperarRespuestaIngresoTecnico(){
		if (http_request.readyState == 4) {
         		if (http_request.status == 200) {
            			//alert(http_request.responseText);
            			var result = http_request.responseText;
            			//alert(result);
            			var rta = result.split(";");
            			var rta2=rta[0];
            			var nombre=rta[1];
            			rta2=rta2.replace('\n','');
				

				if(rta2=="SI"){
					var divi=document.getElementById("nombreTecnico");
					//div para guardar la ciudad del tecnico y hacer la validacion al momento de guardar
					var city=document.getElementById("city");
					var id_tecnico=document.getElementById("id_tecnico");
					var nombre_de_la_empresa=document.getElementById("nombre_de_la_empresa");
					var ciudad=rta[4];
					divi.innerHTML=nombre+" - "+ciudad;
					city.innerHTML=ciudad;
					id_tecnico.value=rta[2];
					nombre_de_la_empresa.value=rta[3];
					document.getElementById("observaciones").focus();
					
            			} else {

					alert("Ocurrio un error al momento de insertar el registro en la base de datos..");
            				
				}
            
           			 
            			//si llego aca se hizo bien la transaccion, ahora se debe calcular de nuevo la extension del turno
				//alert(horarioNuevo+" - "+idturno);
			
         		} else {
            			alert('There was a problem with the request.');
         		}
			Modalbox.hide();
      		}
	}

	function recuperarRespuestaBusquedaTecnico(){
		
		if (http_request.readyState == 4) {
         		if (http_request.status == 200) {
            			//alert(http_request.responseText);
            			var result = http_request.responseText;
            			//alert(result);
            			var rta = result.split(";");
            			var rta2=rta[0];
            			var mensaje=rta[1];
            			rta2=rta2.replace('\n','');
            			//en el array si es un NO viene aparte de la respuesta la identificacion.
				//si es un si viene aparte de la respuesta nombre e identificacion
				if(rta2=="NO"){
					Modalbox.show('ingresoTecnico.php?id_tecnico='+mensaje, {title: 'Ingreso de Tecnicos',height: 400, width: 500 });
					
					//ingresarTecnico();
					/*
            				var nombre=prompt("El tecnico con identificacion "+mensaje+" No existe en la base de datos, si desea ingresarlo por favor ingrese el nombre:");
					if(nombre!=""&&nombre!=null&&nombre!="null"){
						ingresarTecnico(nombre,mensaje);
					}*/
            				return;
            			} else {
					var divi=document.getElementById("nombreTecnico");
					var city=document.getElementById("city");
					var ciudad=rta[4]
					divi.innerHTML=mensaje+" - "+ciudad+" - <a href='javascript:modificarInformacionTecnico();'>editar</a>";
					city.innerHTML=ciudad;
					var nombre_de_la_empresa=document.getElementById("nombre_de_la_empresa");
					nombre_de_la_empresa.value=rta[3];
					//document.getElementById("observaciones").focus();
					document.getElementById("producto").focus();

				}
            
           			 
            			//si llego aca se hizo bien la transaccion, ahora se debe calcular de nuevo la extension del turno
				//alert(horarioNuevo+" - "+idturno);
			
         		} else {
            			alert('There was a problem with the request.');
         		}
      		}
	}

	//funciones de la pagina de ingreso
	function mostrarFormaIngreso(){
		var divi=document.getElementById('divIngresoEmpresa');
		

		if(divi.style.visibility == "visible"){
			divi.style.visibility="hidden"
			divi.style.position="absolute";
		} else {
			divi.style.visibility="visible"
			divi.style.position="relative";
		}
		
	}
	
	function getTimestamp(){
                http_request = false;
                if (window.XMLHttpRequest) { // Mozilla, Safari,...
                        http_request = new XMLHttpRequest();
                if (http_request.overrideMimeType) {
                // set type accordingly to anticipated content type
                //http_request.overrideMimeType('text/xml');
                        http_request.overrideMimeType('text/html');
                }
                } else if (window.ActiveXObject) { // IE
                        try {
                                http_request = new ActiveXObject("Msxml2.XMLHTTP");
                        } catch (e) {
                                try {
                                        http_request = new ActiveXObject("Microsoft.XMLHTTP");
                                } catch (e) {}
                        }
                }
                if (!http_request) {
                        alert('Cannot create XMLHTTP instance');
                        return false;
                }

                //var url="${pageContext.request.contextPath}/action?accion=gestorReportes&operacion=guardarHorasAjax"+parametros;
                var url="./editar.php?operacion=getTimestamp";
                http_request.onreadystatechange = recuperarRespuestaGetTimestamp;

                http_request.open('GET', url, true);
                http_request.send(null);
	
	}
	
	function recuperarRespuestaGetTimestamp(){
		if (http_request.readyState == 4) {
                        if (http_request.status == 200) {
                                //alert(http_request.responseText);
                                var result = http_request.responseText;
                                //alert(result);
                                var rta = result.split(";");
                                var rta2=rta[0];
                                var fecha=rta[1];
                                rta2=rta2.replace('\n','');

                                var hidden=document.getElementById("divhidden");
				hidden.value=fecha;
				
                        } else {
                                alert('There was a problem with the request.');
                        }
                }
	
	}	

	function ingresarEmpresa(){
		var nombre=document.getElementById("nueva_empresa").value;
		nombre=nombre.toUpperCase();
                http_request = false;
                if (window.XMLHttpRequest) { // Mozilla, Safari,...
                        http_request = new XMLHttpRequest();
                if (http_request.overrideMimeType) {
                // set type accordingly to anticipated content type
                //http_request.overrideMimeType('text/xml');
                        http_request.overrideMimeType('text/html');
                }
                } else if (window.ActiveXObject) { // IE
                        try {
                                http_request = new ActiveXObject("Msxml2.XMLHTTP");
                        } catch (e) {
                                try {
                                        http_request = new ActiveXObject("Microsoft.XMLHTTP");
                                } catch (e) {}
                        }
                }
                if (!http_request) {
                        alert('Cannot create XMLHTTP instance');
                        return false;
                }

                //var url="${pageContext.request.contextPath}/action?accion=gestorReportes&operacion=guardarHorasAjax"+parametros;
                var url="./editar.php?operacion=ingresarEmpresa&nombre="+nombre;
                http_request.onreadystatechange = recuperarRespuestaIngresarEmpresa;

                http_request.open('GET', url, true);
                http_request.send(null);
	}


	function recuperarRespuestaIngresarEmpresa(){
		
		if (http_request.readyState == 4) {
         		if (http_request.status == 200) {
            			//alert(http_request.responseText);
            			var result = http_request.responseText;
            			//alert(result);
            			var rta = result.split(";");
            			var rta2=rta[0];
            			var mensaje=rta[1];
            			rta2=rta2.replace('\n','');
				
				//Modalbox.hide();
				var id_tecnico=document.getElementById("id_tecnico_ingreso").value;
				Modalbox.show('ingresoTecnico.php?id_tecnico='+id_tecnico, {title: 'Ingreso de Tecnicos',height: 400, width: 500 });
			
        		} else {
        			alert('There was a problem with the request.');
        		}
		}
	}

	//////final funciones pagina de ingreso de tecnico
	

	//para validar la accion seleccionada, si es un pendiente se despliega la forma de tipos de pendiente
	function validarAccion(){
		
		var acciones=document.getElementById("accion").value;

		var divi=document.getElementById("divPendientes");
		if(acciones=="Compromisos"){//muestre el pick chooser de fecha y hora
			divi.style.visibility="visible";
			divi.style.position="relative";
		}else if(acciones=="Actualizar Infraestructura"){
			
			iniciarActualizacionInfraes();

			//divi=document.getElementById("divInfraestructura");
			//divi.innerHTML="<a href=''>Actualizar Infraestructura</a>";
		}else{
			//para cualquier otra opcion oculto el combo
			divi.style.position="absolute";
			divi.style.visibility="hidden";
			
		}
	}
	
	function hideMsg(){

		var divMensajeCentral=document.getElementById("divMensajeCentral");

		getTimestamp();
		//var hidden=document.getElementById("divhidden");
		
		//alert(hidden.value);		

		if(divMensajeCentral.style.visibility=="hidden") return;
		else {
			//sleep(3000);
			divMensajeCentral.style.position="absolute";
			divMensajeCentral.style.visibility="hidden";
		}
	}

	function buscarPedido(){
		var pedido=document.getElementById("pedido");
			
		if(pedido.value==""){
			alert("Ingrese un numero de pedido.");
			pedido.focus();
			return;
		}
		Modalbox.show('validacionPedido.php?pedido='+pedido.value, {title: 'Busqueda de Pedidos',height: 400, width: 800 });
	}


	///inicio funciones pagina de validacion de pedidos
	function mostrarObservacion(id){
		//alert("llegue");
		var divi=document.getElementById("div"+id);
		if(divi.innerHTML==""||divi.innerHTML=="null"||divi.innerHTML==null){
			return;
		}else{
			alert("OBSERVACIONES:\n\n"+divi.innerHTML);
		}
		//divi.style.visibility="visible";
		//divi.style.position="relative";
		return;
	}


	function formaRegistros(){
		var user="<? echo $_SESSION['login']; ?>";
		
		if(user==""){
			user=prompt("Ingrese su nombre de usuario.");
			if(user==""||user=="null"||user==null){
				alert("No se puede llevar a cabo la operacion, debe ingresar su nombre de usuario.");
				return;
			}
		}
		location.href="./registros.php?user="+user;
	}

	function clearDuracion() {
		
		var dur=document.getElementById("duracion");
		if(dur.value!=""){
			return;
		}
		else {
			dur.value="";
		}
  		//document.testform.email.value= "";
	}

	function saveTime(){
		var hidden=document.getElementById("initTime");
		hidden.value=new Date().getTime();
		var hidden2=document.getElementById("inicioLlamada");
		hidden2.value="true";
	}
	function doubleDigit(num){//estamos asumiendo que no hay cantidades negativas!!!
		if(num<=9){
			return "0"+num;
		}
		return num;
	}

</script>
<STYLE type="text/css">
        a:link { font-weight: plain; font-size: 16px; color: blue; text-decoration: none }
        a:visited { font-weight: plain; font-size: 16px; color: blue; text-decoration: none }
        a:hover { font-weight: bold; font-size: 16px; color: blue; text-decoration: none }
</STYLE>
<style type="text/css">
#question { background-color: white; padding: 10px; }
#question input { width: 10em }
#question h1 { color: black;  }
</style>

<link rel="stylesheet" href="javascript/actividades.css" type="text/css" />
<script language="JavaScript" src="javascript/calendar.js" type="text/javascript"></script>
<script language="JavaScript">
        addCalendar("DateIni", "calIni", "fechaCompromiso", "forma1");
</script>

</HEAD>

<BODY bgcolor="WHITE">
<FORM name="forma1">

<table width="100%">
<tr><td colspan="3">
<center><img src="img/banner.png" height="141" width="888"></center>
</td></tr>
</table>

<input type="hidden" value="" id="initTime">
<input type="hidden" value="false" id="inicioLlamada">
<DIV id="city" style="position:absolute; z-index:2;visibility:hidden;"></DIV>
<DIV id="cal" style="position:absolute; z-index:2;">&nbsp;</DIV>

<input type="hidden" value="" name="divhidden" id="divhidden">

<CENTER><H1>Seguimiento de Quejas</H1></CENTER>

<center>
<div id="divMensajeCentral" <? if($msg!="") echo " style=\"position:relative;visibility:visible;background-color: #FFFEBE;border:2px solid #FFFE88;\""; else echo " style=\"position:absolute;visibility:hidden;\""; ?>>
<font color="red"><b><? echo $msg;?></b></font>
</div>
</center>
<BR>
<TABLE align="center">

<TBODY>
<TR>
	<TD align="left">Numero de Queja</TD>
	<TD align="center"><INPUT type="text" name="pedido" size="12" id="pedido" value="<? echo $numero_queja;?>" onchange="javascript:hideMsg();"></TD>
	<TD align="center"><input type="button" class="btnpurple" value="Buscar" onclick="javascript:buscarPedido();"></TD>
</TR>
<TR>
	<TD align="left">Identificacion del Tecnico</TD>
	<TD align="center"><INPUT type="text" name="id_tecnico" size="12" id="id_tecnico" value="<? echo $id_tecnico; ?>"></TD>
	<TD align="center">
	<div id="divIngreso"><INPUT type="button" class="btnpurple" name="buscar" id="buscar" value="Buscar" onclick="javascript:buscarTecnico();"></div>
	</TD>
</TR>
<TR>
<TD align='left'>Nombre del Tecnico</TD>
<TD align='center' colspan="2"><font color='red'><div id="nombreTecnico"><? echo $nombre_tecnico." - ".$ciudad; ?></div></font></TD>
<!--TD align="center"></TD-->
</TR>

<TR>
	<TD align="left">Nombre de la empresa</TD>
	<TD align="center">
		<input type="text" id="nombre_de_la_empresa" disabled="true" value="<? echo $empresa; ?>" size="12">
	</TD>
	<TD align="center"></TD>
</TR>

<TR>
        <TD align="left">Duracion Llamada ACD</TD>
        <TD align="center"><INPUT type="text" name="duracion" id="duracion" size="12" value="<? echo $duracion; ?>" disabled='disabled'></TD>
        <TD align="center"></TD>
</TR>


<TR>
	<TD align="left">Login del asesor</TD>
	<TD align="center"><INPUT type="text" name="login_del_asesor" id="login_del_asesor" size="12" value="<? echo $asesor; ?>"></TD>
	<TD align="center"></TD>
</TR>
<TR>
	<TD align="left">Observaciones</TD>
	<TD align="center"><TEXTAREA name="observaciones" id="observaciones" cols="40" rows="4"><? echo $observaciones; ?></TEXTAREA></TD>
	<TD align="center"></TD>
</TR>
<TR>
	<TD align="left">Accion</TD>
	<TD align="center">
		<SELECT name="accion" id="accion">
			<OPTION value="Atendida" <? if($accion=="Atendida") echo "selected"; ?> > Atendida</OPTION>
			<OPTION value="Compromisos" <? if($accion=="Compromisos") echo "selected"; ?> > Compromisos</OPTION>
			<OPTION value="Atendida sin Solución" <? if($accion=="Atendida sin Solución") echo "selected"; ?> > Atendida sin Solución</OPTION>
			<OPTION value="Enrutar" <? if($accion=="Enrutar") echo "selected"; ?> > Enrutar</OPTION>
			<OPTION value="Brindar Información" <? if($accion=="Brindar Información") echo "selected"; ?> > Brindar Información</OPTION>
			<OPTION value="Inconsistencias Fénix" <? if($accion=="Inconsistencias Fénix") echo "selected"; ?> > Inconsistencias Fénix</OPTION>
			<OPTION value="Otros Usuarios" <? if($accion=="Otros Usuarios") echo "selected"; ?> > Otros Usuarios</OPTION>
			<OPTION value="Llamada Caida" <? if($accion=="Llamada Caida") echo "selected"; ?> > Llamada Caida</OPTION>
			<OPTION value="Otros" <? if($accion=="Otros") echo "selected"; ?> > Otros</OPTION>
		</SELECT>
	</TD>
	<TD align="center"> </TD>
</TR>
<TR>
        <TD align="left">Compromiso</TD>
        <TD align="center">
	<input size="10" type="text" id="fechaCompromiso" name="fechaCompromiso" value="<? echo $fecha_compromiso;?>" style="background-color: rgb(255, 255, 160);" >
	<select id="horaCompromiso" name="horaCompromiso">
		<option value="07:00 AM" <? if($hora_compromiso=="07:00 AM") echo "selected"; ?>>07:00 AM</option>
                <option value="08:00 AM" <? if($hora_compromiso=="08:00 AM") echo "selected"; ?>>08:00 AM</option>
                <option value="09:00 AM" <? if($hora_compromiso=="09:00 AM") echo "selected"; ?>>09:00 AM</option>
                <option value="10:00 AM" <? if($hora_compromiso=="10:00 AM") echo "selected"; ?>>10:00 AM</option>
                <option value="11:00 AM" <? if($hora_compromiso=="11:00 AM") echo "selected"; ?>>11:00 AM</option>
                <option value="12:00 M" <? if($hora_compromiso=="12:00 M") echo "selected"; ?>>12:00 M</option>
                <option value="01:00 PM" <? if($hora_compromiso=="01:00 PM") echo "selected"; ?>>01:00 PM</option>
                <option value="02:00 PM" <? if($hora_compromiso=="02:00 PM") echo "selected"; ?>>02:00 PM</option>
                <option value="03:00 PM" <? if($hora_compromiso=="03:00 PM") echo "selected"; ?>>03:00 PM</option>
                <option value="04:00 PM" <? if($hora_compromiso=="04:00 PM") echo "selected"; ?>>04:00 PM</option>
                <option value="05:00 PM" <? if($hora_compromiso=="05:00 PM") echo "selected"; ?>>05:00 PM</option>
                <option value="06:00 PM" <? if($hora_compromiso=="06:00 PM") echo "selected"; ?>>06:00 PM</option>
                <option value="07:00 PM" <? if($hora_compromiso=="07:00 PM") echo "selected"; ?>>07:00 PM</option>

	</select>
		</div>
	</TD>
</TR>
<tr>
<td colspan="3" align="center">
<div id="divInfraestructura"></div>
</td>
</tr>
</TBODY></TABLE>
<BR>
<CENTER><INPUT type="button" class="btnpurple" value="Guardar" onclick="javascript:guardarDatos();">
&nbsp;<INPUT type="button" class="btnpurple" value="Ver Registros" onclick="javascript:formaRegistros();">
</CENTER>


</FORM>


<br><br>

</BODY>
<script type="text/javascript">
	jQuery(document).ready(function($) {

        $(function() {
                $( "#fechaCompromiso" ).datepicker({ dateFormat: "yy-mm-dd" });
        });

});

	
</script> 
</HTML>
