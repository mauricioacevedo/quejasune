<?php

function getConnection() {
    //$conn=pg_connect("host=10.65.140.67 dbname=actividades_asesores user=postgres password=Animo");
    $conn=pg_connect("host=10.65.83.61 dbname=quejasune user=postgres password=Animo");
    if (!$conn) {
        return false;
    }
    return $conn;
}

?>

