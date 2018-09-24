<?php
ob_end_flush();
ini_set("output_buffering", "0");
ob_implicit_flush(true);

function pingtest()
{
    $proc = popen("/usr/java/java/bin/java -jar /var/www/html/quejasune/java/QuejasUne.jar InformacionSiebel 10.120.53.129 fenixune ogarzon f7n8x902014 4424 /var/www/html/quejasune/java/fileConfig.xml", 'r');
    while (!feof($proc))
    {
        echo "[".date("h24:i:s")."] ".fread($proc, 4096);
    }
}

?>
<!DOCTYPE html>
<html>
<body>
  <pre>
Immediate output: 
<?php
pingtest();
?>
  </pre>
</body>
</html>
