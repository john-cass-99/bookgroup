<?php
if (isset($showPDOErrors) && $showPDOErrors) {
    print("<p>PDO Error: " . $ex->getMessage() . "</p>");
}
else {
    print( "<p>An error has occurred\r\n</p>");
    error_log($ex->getMessage());
}
?>
