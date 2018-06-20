<?php
include "DAO\conn.php";
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods:POST');
header('Access-Control-Allow-Headers:x-requested-with,content-type');
header("Content-Type: text/html;charset=utf-8");
$action = isset($_POST['action']) ? $_POST['action'] : '';
if($action=="user"){
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $sql_query_one="select * from user where username='".$username."'";
    $userid= mysql_fetch_array(mysql_query($sql_query_one,$conn));
    if ($userid) {
        $array["status"]=200;
    }
    else {
        $array["status"]=404;
    }
    echo json_encode($array);
}
if($action=="email"){
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $sql_query_one="select * from user where email='".$email."'";
    $userid= mysql_fetch_array(mysql_query($sql_query_one,$conn));
    if ($userid) {
        $array["status"]=200;
    }
    else {
        $array["status"]=404;
    }
    echo json_encode($array);
}
if($action=="mobile"){
    $mobile = isset($_POST['mobile']) ? $_POST['mobile'] : '';
    $sql_query_one="select * from user where phone='".$mobile."'";
    $userid= mysql_fetch_array(mysql_query($sql_query_one,$conn));
    if ($userid) {
        $array["status"]=200;
    }
    else {
        $array["status"]=404;
    }
    echo json_encode($array);
}


mysql_close($conn);
?>