<?php

include_once('tools.php');

echo posttohtml(readbyposted($cx, ttime($_GET['id'])));

?>
