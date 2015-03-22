<?php
    require_once("../../../../wp-load.php");
    echo date( 'n/j/Y H:i:s', strtotime(current_time('mysql')) );
?>