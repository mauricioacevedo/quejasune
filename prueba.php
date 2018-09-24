<html>
<head>

<script language="JavaScript" src="javascript/calendar.js" type="text/javascript"></script>
<script language="JavaScript">
<!--

addCalendar("DateIni", "calIni", "fechaIni", "forma1");

//-->
</script>
</head>

<body>
<form name="forma1">

<div id="divPendientes" height="1%" style="position:absolute;visibility:hidden;">

        <input size="10" type="text" id="fechaCompromiso" name="fechaCompromiso" value="2012-09-04" style="background-color: rgb(255, 255, 160);" onfocus="javascript:showCal('DateIni', 5, -100);">
        <select id="horaCompromiso" name="horaCompromiso">
                <option value="07:00 AM">07:00 AM</option>
                <option value="08:00 AM">08:00 AM</option>
                <option value="09:00 AM">09:00 AM</option>
                <option value="10:00 AM">10:00 AM</option>
                <option value="11:00 AM">11:00 AM</option>
                <option value="12:00 M">12:00 M</option>
                <option value="01:00 PM">01:00 PM</option>
                <option value="02:00 PM">02:00 PM</option>
                <option value="03:00 PM">03:00 PM</option>
                <option value="04:00 PM">04:00 PM</option>
                <option value="05:00 PM">05:00 PM</option>
                <option value="06:00 PM">06:00 PM</option>
                <option value="07:00 PM">07:00 PM</option>

        </select>
        <!--span title="Click Para Abrir El Calendario"><a href="javascript:showCal('DateIni', 5, -100);" style="color: black;">(aaaa-mm-dd)</a></span-->
                </div>
        <div id="calIni" style="position:relative; visibility: hidden;"></div>


</form>
</body>
</html>

