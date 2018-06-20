<?php
header("Content-Type: text/html;charset=utf-8");
    session_destroy();
    unset($_SESSION);
    echo "<script>location.href='index.php';</script>";




?>