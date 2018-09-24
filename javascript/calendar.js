// +------------------------------------------------------------+
// |                       Popup Calendar                       |
// +------------------------------------------------------------+
// | Last Modified:                  15-Nov-2002                |
// | Web Site:                       http://www.yxscripts.com   |
// | EMail:                          m_yangxin@hotmail.com      |
// +------------------------------------------------------------+
// |       Copyright 2002  Xin Yang   All Rights Reserved.      |
// +------------------------------------------------------------+

// default settings
var fontFace="Verdana";
var fontSize=10;

var titleWidth=90;
var titleMode=1;
var dayWidth=15;
var dayDigits=1;

var titleColor="#f7ff1c";
var daysColor="#000000";
var bodyColor="#ffffff";
var dayColor="#ffffff";
var currentDayColor="#f7ff1c";
var footColor="#000000";
var borderColor="#000000";

var titleFontColor = "#000000";
var daysFontColor = "white";
var dayFontColor = "#000000";
var currentDayFontColor = "#000000";
var footFontColor = "white";

var calFormat = "yyyy-mm-dd";

var weekDay = 1;
// ------

// codes
var cal="cal";
var cals = new Array();
var currentCal=null;

var yxMonths=new Array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
var yxDays=new Array("Domingo","Lunes","Martes","Miércoles","Jueves","Viernes","Sábado","Domingo");
var yxLinks=new Array("[Cerrar]", "[Borrar]");

var isOpera=(navigator.userAgent.indexOf("Opera")!=-1)?true:false;
var isN6=(navigator.userAgent.indexOf("Gecko")!=-1);
var isN4=(document.layers)?true:false;
var isMac=(navigator.userAgent.indexOf("Mac")!=-1);
var isIE=(document.all && !isOpera && (!isMac || navigator.appVersion.indexOf("MSIE 4")==-1))?true:false;
var isIE4=(isIE && document.getElementById)?false:true;


var yxLayers=null;
if (isIE) {
  yxLayers=document.all;
}
else if (isN4) {
  yxLayers=document.layers;
  fontSize+=2;
}

function span1(color) {
  return "<span style='font-family:"+fontFace+"; font-size:"+fontSize+"px; color:"+color+";'>";
}

function calOBJ(name, id, field, form) {
  this.name = name;
  this.id = id;
  this.field = field;
  this.formName = form;
  this.form = null
}

function setFont(font, size) {
  if (font != "") {
    fontFace=font;
  }
  if (size > 0) {
    fontSize=size;

    if (isN4) {
      fontSize+=2;
    }
  }
}

function setWidth(tWidth, tMode, dWidth, dDigits) {
  if (tWidth > 0) {
    titleWidth=tWidth;
  }
  if (tMode == 1 || tMode == 2) {
    titleMode=tMode;
  }
  if (dWidth > 0) {
    dayWidth=dWidth;
  }
  if (dDigits > 0) {
    dayDigits=dDigits;
  }
}

function setColor(tColor, dsColor, bColor, dColor, cdColor, fColor, bdColor) {
  if (tColor != "") {
    titleColor=tColor;
  }
  if (dsColor != "") {
    daysColor=dsColor;
  }
  if (bColor != "") {
    bodyColor=bColor;
  }
  if (dColor != "") {
    dayColor=dColor;
  }
  if (cdColor != "") {
    currentDayColor=cdColor;
  }
  if (fColor != "") {
    footColor=fColor;
  }
  if (bdColor != "") {
    borderColor=bdColor;
  }
}

function setFontColor(tColorFont, dsColorFont, dColorFont, cdColorFont, fColorFont) {
  if (tColorFont != "") {
    titleFontColor=tColorFont;
  }
  if (dsColorFont != "") {
    daysFontColor=dsColorFont;
  }
  if (dColorFont != "") {
    dayFontColor=dColorFont;
  }
  if (cdColorFont != "") {
    currentDayFontColor=cdColorFont;
  }
  if (fColorFont != "") {
    footFontColor=fColorFont;
  }
}

function setFormat(format) {
  calFormat = format;
}

function setWeekDay(wDay) {
  if (wDay == 0 || wDay == 1) {
    weekDay = wDay;
  }
}

function setMonthNames(janName, febName, marName, aprName, mayName, junName, julName, augName, sepName, octName, novName, decName) {
  if (janName != "") {
    yxMonths[0] = janName;
  }
  if (febName != "") {
    yxMonths[1] = febName;
  }
  if (marName != "") {
    yxMonths[2] = marName;
  }
  if (aprName != "") {
    yxMonths[3] = aprName;
  }
  if (mayName != "") {
    yxMonths[4] = mayName;
  }
  if (junName != "") {
    yxMonths[5] = junName;
  }
  if (julName != "") {
    yxMonths[6] = julName;
  }
  if (augName != "") {
    yxMonths[7] = augName;
  }
  if (sepName != "") {
    yxMonths[8] = sepName;
  }
  if (octName != "") {
    yxMonths[9] = octName;
  }
  if (novName != "") {
    yxMonths[10] = novName;
  }
  if (decName != "") {
    yxMonths[11] = decName;
  }
}

function setDayNames(sunName, monName, tueName, wedName, thuName, friName, satName) {
  if (sunName != "") {
    yxDays[0] = sunName;
    yxDays[7] = sunName;
  }
  if (monName != "") {
    yxDays[1] = monName;
  }
  if (tueName != "") {
    yxDays[2] = tueName;
  }
  if (wedName != "") {
    yxDays[3] = wedName;
  }
  if (thuName != "") {
    yxDays[4] = thuName;
  }
  if (friName != "") {
    yxDays[5] = friName;
  }
  if (satName != "") {
    yxDays[6] = satName;
  }
}

function setLinkNames(closeLink, clearLink) {
  if (closeLink != "") {
    yxLinks[0] = closeLink;
  }
  if (clearLink != "") {
    yxLinks[1] = clearLink;
  }
}

function addCalendar(name, id, field, form) {
  cals[cals.length] = new calOBJ(name, id, field, form);
}

function findCalendar(name) {
  for (var i = 0; i < cals.length; i++) {
    if (cals[i].name == name) {
      if (cals[i].form == null) {
        if (cals[i].formName == "") {
          if (document.forms[0]) {
            cals[i].form = document.forms[0];
          }
        }
        else if (document.forms[cals[i].formName]) {
          cals[i].form = document.forms[cals[i].formName];
        }
      }

      return cals[i];
    }
  }

  return null;
}

function getDayName(y,m,d) {
  var wd=new Date(y,m,d);
  return yxDays[wd.getDay()].substring(0,3);
}

function getMonthFromName(m3) {
  for (var i = 0; i < yxMonths.length; i++) {
    if (yxMonths[i].toLowerCase().substring(0,3) == m3.toLowerCase()) {
      return i;
    }
  }

  return 0;
}

function getFormat() {
  var calF = calFormat;

  calF = calF.replace(/\\/g, '\\\\');
  calF = calF.replace(/\//g, '\\\/');
  calF = calF.replace(/\[/g, '\\\[');
  calF = calF.replace(/\]/g, '\\\]');
  calF = calF.replace(/\(/g, '\\\(');
  calF = calF.replace(/\)/g, '\\\)');
  calF = calF.replace(/\{/g, '\\\{');
  calF = calF.replace(/\}/g, '\\\}');
  calF = calF.replace(/\</g, '\\\<');
  calF = calF.replace(/\>/g, '\\\>');
  calF = calF.replace(/\|/g, '\\\|');
  calF = calF.replace(/\*/g, '\\\*');
  calF = calF.replace(/\?/g, '\\\?');
  calF = calF.replace(/\+/g, '\\\+');
  calF = calF.replace(/\^/g, '\\\^');
  calF = calF.replace(/\$/g, '\\\$');

  calF = calF.replace(/dd/i, '\\d\\d');
  calF = calF.replace(/mm/i, '\\d\\d');
  calF = calF.replace(/yyyy/i, '\\d\\d\\d\\d');
  calF = calF.replace(/day/i, '\\w\\w\\w');
  calF = calF.replace(/mon/i, '\\w\\w\\w');

  return new RegExp(calF);
}

function getDateNumbers(date) {
  var y, m, d;

  var yIdx = calFormat.search(/yyyy/i);
  var mIdx = calFormat.search(/mm/i);
  var m3Idx = calFormat.search(/mon/i);
  var dIdx = calFormat.search(/dd/i);

  y=date.substring(yIdx,yIdx+4)-0;
  if (mIdx != -1) {
    m=date.substring(mIdx,mIdx+2)-1;
  }
  else {
    var m = getMonthFromName(date.substring(m3Idx,m3Idx+3));
  }
  d=date.substring(dIdx,dIdx+2)-0;

  return new Array(y,m,d);
}

function hideCal() {
  if (isIE) {
    yxLayers[cal].style.visibility="hidden";
  }
  else if (isN4) {
    yxLayers[cal].visibility="hide";
    yxLayers[cal].document.open();
    yxLayers[cal].document.close();
  }
  else if (isN6) {
    document.getElementById(cal).style.visibility="hidden";
  }

  window.status = "";
}

function getLeftIEx(x,m) {
  var dx=0;
  if (x.tagName=="TD"){
    dx=x.offsetLeft;
  }
  else if (x.tagName=="TABLE") {
    dx=x.offsetLeft;
    if (m) { dx+=(x.cellPadding!=""?parseInt(x.cellPadding):2); m=false; }
  }
  return dx+(x.parentElement.tagName=="BODY"?0:getLeftIEx(x.parentElement,m));
}
function getLeftIE(l) { return l.offsetLeft+(l.offsetParent?getLeftIE(l.offsetParent):0 ); }
function getTopIEx(x,m) {
  var dy=0;
  if (x.tagName=="TR"){
    dy=x.offsetTop;
  }
  else if (x.tagName=="TABLE") {
    dy=x.offsetTop;
    if (m) { dy+=(x.cellPadding!=""?parseInt(x.cellPadding):2); m=false; }
  }
  return dy+(x.parentElement.tagName=="BODY"?0:getTopIEx(x.parentElement,m));
}
function getTopIE(l) { return l.offsetTop+(l.offsetParent?getTopIE(l.offsetParent):0); }

function getLeftN4(l) { return l.pageX; }
function getTopN4(l) { return l.pageY; }

function getLeftN6(l) { return l.offsetLeft; }
function getTopN6(l) { return l.offsetTop; }

function lastDay(d) {
  var yy=d.getFullYear(), mm=d.getMonth();
  for (var i=31; i>=28; i--) {
    var nd=new Date(yy,mm,i);
    if (mm == nd.getMonth()) {
      return i;
    }
  }
  return 31;
}

function firstDay(d) {
  var yy=d.getFullYear(), mm=d.getMonth();
  var fd=new Date(yy,mm,1);
  return fd.getDay();
}

function dayDisplay(i) {
  if (dayDigits == 0) {
    return yxDays[i];
  }
  else {
    return yxDays[i].substring(0,dayDigits);
  }
}

function calTitle(d) {
  var yy=d.getFullYear(), mm=yxMonths[d.getMonth()];
  var s;

  if (titleMode == 2) {
    s="<tr align='center' bgcolor='"+titleColor+"'><td colspan='7'><table cellpadding='0' cellspacing='0' border='0'><tr align='center' valign='middle'><td align='right'><b><a href='javascript:moveYear(-10)' style='text-decoration:none'>"+span1(titleFontColor)+"&nbsp;&#171;</span></a>"+span1(titleFontColor)+"&nbsp;</span><a href='javascript:moveYear(-1)' style='text-decoration:none'>"+span1(titleFontColor)+"&#139;&nbsp;</span></a></b></td><td width='"+titleWidth+"'><b>"+span1(titleFontColor)+yy+"</span></b></td><td align='left'><b><a href='javascript:moveYear(1)' style='text-decoration:none'>"+span1(titleFontColor)+"&nbsp;&#155;</span></a>"+span1(titleFontColor)+"&nbsp;</span><a href='javascript:moveYear(10)' style='text-decoration:none'>"+span1(titleFontColor)+"&#187;&nbsp;</span></a></b></td></tr><tr align='center' valign='middle'><td align='right'><b><a href='javascript:prepMonth("+d.getMonth()+")' style='text-decoration:none'>"+span1(titleFontColor)+"&nbsp;&#139;&nbsp;</span></a></b></td><td width='"+titleWidth+"'><b>"+span1(titleFontColor)+mm+"</span></b></td><td align='left'><b><a href='javascript:nextMonth("+d.getMonth()+")' style='text-decoration:none'>"+span1(titleFontColor)+"&nbsp;&#155;&nbsp;</span></a></b></td></tr></table></td></tr><tr align='center' bgcolor='"+daysColor+"'>";
  }
  else {
    s="<tr align='center' bgcolor='"+titleColor+"'><td colspan='7'><table cellpadding='0' cellspacing='0' border='0'><tr align='center' valign='middle'><td><b><a href='javascript:moveYear(-1)' style='text-decoration:none'>"+span1(titleFontColor)+"&nbsp;&#171;</span></a>"+span1(titleFontColor)+"&nbsp;</span><a href='javascript:prepMonth("+d.getMonth()+")' style='text-decoration:none'>"+span1(titleFontColor)+"&#139;&nbsp;</span></a></b></td><td width='"+titleWidth+"'><nobr><b>"+span1(titleFontColor)+mm+" "+yy+"</span></b></nobr></td><td><b><a href='javascript:nextMonth("+d.getMonth()+")' style='text-decoration:none'>"+span1(titleFontColor)+"&nbsp;&#155;</span></a>"+span1(titleFontColor)+"&nbsp;</span><a href='javascript:moveYear(1)' style='text-decoration:none'>"+span1(titleFontColor)+"&#187;&nbsp;</span></a></b></td></tr></table></td></tr><tr align='center' bgcolor='"+daysColor+"'>";
  }

  for (var i=weekDay; i<weekDay+7; i++) {
    s+="<td width='"+dayWidth+"'>"+span1(daysFontColor)+dayDisplay(i)+"</span></td>";
  }

  s+="</tr>";

  return s;
}

function calHeader() {
  return "<table bgcolor='"+borderColor+"' cellspacing='0' cellpadding='1'><tr><td><table cellspacing='1' cellpadding='3' border='0'>";
}

function calFooter() {
  return "<tr bgcolor='"+footColor+"'><td colspan='7' align='center'><b><a href='javascript:hideCal()' style='text-decoration:none'>"+span1(footFontColor)+yxLinks[0]+"</span></a>"+span1(footFontColor)+"&nbsp;&nbsp;<a href='javascript:clearDate()' style='text-decoration:none'>"+span1(footFontColor)+yxLinks[1]+"</span></a></b></td></tr></table></td></tr></table>";
}

function calBody(d,day) {
  var s="", dayCount=1, fd=firstDay(d), ld=lastDay(d);

  if (weekDay > 0 && fd == 0) {
    fd = 7;
  }

  for (var i=0; i<6; i++) {
    s+="<tr align='center' bgcolor='"+bodyColor+"'>";
    for (var j=weekDay; j<weekDay+7; j++) {
      if (i*7+j<fd || dayCount>ld) {
        s+="<td>"+span1(dayFontColor)+"&nbsp;</span></td>";
      }
      else {
        var bgColor=dayColor;
        var fgColor=dayFontColor;
        if (dayCount==day) { 
          bgColor=currentDayColor; 
          fgColor=currentDayFontColor; 
        }
        
        s+="<td bgcolor='"+bgColor+"'><a href='javascript:pickDate("+dayCount+")' style='text-decoration:none'>"+span1(fgColor)+(dayCount++)+"</span></a></td>";
      }
    }
    s+="</tr>";
  }

  return s;
}

function moveYear(dy) {
  cY+=dy;
  var nd=new Date(cY,cM,1);
  changeCal(nd);
}

function prepMonth(m) {
  cM=m-1;
  if (cM<0) { cM=11; cY--; }
  var nd=new Date(cY,cM,1);
  changeCal(nd);
}

function nextMonth(m) {
  cM=m+1;
  if (cM>11) { cM=0; cY++;}
  var nd=new Date(cY,cM,1);
  changeCal(nd);
}

function changeCal(d) {
  var dd = 0;

  if (currentCal != null) {
    var calRE = getFormat();

    if (currentCal.form[currentCal.field].value!="" && calRE.test(currentCal.form[currentCal.field].value)) {
      var cd = getDateNumbers(currentCal.form[currentCal.field].value);
      if (cd[0] == d.getFullYear() && cd[1] == d.getMonth()) {
        dd=cd[2];
      }
    }
    else {
      var cd = new Date();
      if (cd.getFullYear() == d.getFullYear() && cd.getMonth() == d.getMonth()) {
        dd=cd.getDate();
      }
    }
  }

  var calendar=calHeader()+calTitle(d)+calBody(d,dd)+calFooter();

  if (isIE) {
    yxLayers[cal].innerHTML=calendar;
  }
  else if (isN4) {
    yxLayers[cal].document.open();
    yxLayers[cal].document.writeln(calendar);
    yxLayers[cal].document.close();
  }
  else if (isN6) {
    document.getElementById(cal).innerHTML=calendar;
  }
}

function showCal(name, x, y) {
  var lastCal=currentCal;
  var d=new Date();

  currentCal = findCalendar(name);

  if (currentCal != null && currentCal.form != null && currentCal.form[currentCal.field]) {
    var calRE = getFormat();

    if (currentCal.form[currentCal.field].value!="" && calRE.test(currentCal.form[currentCal.field].value)) {
      var cd = getDateNumbers(currentCal.form[currentCal.field].value);
      d=new Date(cd[0],cd[1],cd[2]);

      cY=cd[0];
      cM=cd[1];
      dd=cd[2];
    }
    else {
      cY=d.getFullYear();
      cM=d.getMonth();
      dd=d.getDate();
    }

    var calendar=calHeader()+calTitle(d)+calBody(d,dd)+calFooter();

    var calOX=0, calOY=0;
    if (typeof(x) == "number") {
      calOX=x;
    }
    if (typeof(y) == "number") {
      calOY=y;
    }

    if (isIE) {
      yxLayers[cal].style.pixelTop=calOY+((yxLayers[currentCal.id].parentElement.tagName=="TD" && !isIE4)?getTopIEx(yxLayers[currentCal.id],true):getTopIE(yxLayers[currentCal.id]));
      yxLayers[cal].style.pixelLeft=calOX+((yxLayers[currentCal.id].parentElement.tagName=="TD" && !isIE4)?getLeftIEx(yxLayers[currentCal.id],true):getLeftIE(yxLayers[currentCal.id]));
      yxLayers[cal].innerHTML=calendar;

      yxLayers[cal].style.clip="rect(0px; " + yxLayers[cal].children[0].offsetWidth + "px; " + yxLayers[cal].children[0].offsetHeight + "px; 0px)";
      yxLayers[cal].style.visibility="visible";
    }
    else if (isN4) {
      yxLayers[cal].top=calOY+getTopN4(yxLayers[currentCal.id]);
      yxLayers[cal].left=calOX+getLeftN4(yxLayers[currentCal.id]);
      yxLayers[cal].document.open();
      yxLayers[cal].document.writeln(calendar);
      yxLayers[cal].document.close();
      yxLayers[cal].visibility="show";
    }
    else if (isN6) {
      var l=document.getElementById(currentCal.id);
      var layer=document.getElementById(cal);
      layer.style.top=calOY+getTopN6(l)+"px";
      layer.style.left=calOX+getLeftN6(l)+"px";
      layer.innerHTML=calendar;
      layer.style.visibility="visible";
    }
    else {
      window.status = "Browser is not supported.";
    }
  }
  else {
    if (currentCal == null) {
      window.status = "Calendar ["+name+"] not found.";
    }
    else if (!currentCal.form) {
      window.status = "Form ["+currentCal.formName+"] not found.";
    }
    else if (!currentCal.form[currentCal.field]) {
      window.status = "Form Field ["+currentCal.formName+"."+currentCal.field+"] not found.";
    }

    if (lastCal != null) {
      currentCal = lastCal;
    }
  }
}

function get2Digits(n) {
  return ((n<10)?"0":"")+n;
}

function clearDate() {
  currentCal.form[currentCal.field].value="";
  hideCal();
}

function pickDate(d) {
  var date=calFormat;
  date = date.replace(/yyyy/i, cY);
  date = date.replace(/mm/i, get2Digits(cM+1));
  date = date.replace(/MON/, yxMonths[cM].substring(0,3).toUpperCase());
  date = date.replace(/Mon/i, yxMonths[cM].substring(0,3));
  date = date.replace(/dd/i, get2Digits(d));
  date = date.replace(/DAY/, getDayName(cY,cM,d).toUpperCase());
  date = date.replace(/day/i, getDayName(cY,cM,d));

  currentCal.form[currentCal.field].value=date;

  hideCal();
}
// ------

// the cal layer
if (isN4) {
  document.writeln("<layer id='"+cal+"' z-index='2'>&nbsp;</layer>");
}
else {
  document.writeln("<div id='"+cal+"' style='position:absolute; z-index:2;'>&nbsp;</div>");
}
// ----

// user functions
function checkDate(name) {
  var thisCal = findCalendar(name);

  if (thisCal != null && thisCal.form != null && thisCal.form[thisCal.field]) {
    var calRE = getFormat();

    if (calRE.test(thisCal.form[thisCal.field].value)) {
      return 0;
    }
    else {
      return 1;
    }
  }
  else {
    return 2;
  }
}

function getCurrentDate() {
  var date=calFormat, d = new Date();
  date = date.replace(/yyyy/i, d.getFullYear());
  date = date.replace(/mm/i, get2Digits(d.getMonth()+1));
  date = date.replace(/dd/i, get2Digits(d.getDate()));

  return date;
}

function compareDates(date1, date2) {
  var calRE = getFormat();
  var d1, d2;

  if (calRE.test(date1)) {
    d1 = getNumbers(date1);
  }
  else {
    d1 = getNumbers(getCurrentDate());
  }

  if (calRE.test(date2)) {
    d2 = getNumbers(date2);
  }
  else {
    d2 = getNumbers(getCurrentDate());
  }

  var dStr1 = d1[0] + "" + d1[1] + "" + d1[2];
  var dStr2 = d2[0] + "" + d2[1] + "" + d2[2];

  if (dStr1 == dStr2) {
    return 0;
  }
  else if (dStr1 > dStr2) {
    return 1;
  }
  else {
    return -1;
  }
}

function getNumbers(date) {
  var calRE = getFormat();
  var y, m, d;

  if (calRE.test(date)) {
    var yIdx = calFormat.search(/yyyy/i);
    var mIdx = calFormat.search(/mm/i);
    var m3Idx = calFormat.search(/mon/i);
    var dIdx = calFormat.search(/dd/i);

    y=date.substring(yIdx,yIdx+4);
    if (mIdx != -1) {
      m=date.substring(mIdx,mIdx+2);
    }
    else {
      var mm=getMonthFromName(date.substring(m3Idx,m3Idx+3))+1;
      m=(mm<10)?("0"+mm):(""+mm);
    }
    d=date.substring(dIdx,dIdx+2);

    return new Array(y,m,d);
  }
  else {
    return new Array("", "", "");
  }
}
// ------
