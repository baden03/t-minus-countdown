<?php
    require_once("../../../../wp-load.php");
    $response = array( 'now' => date( 'n/j/Y H:i:s', strtotime(current_time('mysql'))));
    echo json_encode($response);
    die();
?>
