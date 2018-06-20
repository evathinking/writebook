<?php
include "DAO\conn.php";
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods:POST');
header('Access-Control-Allow-Headers:x-requested-with,content-type');
header("Content-Type: text/html;charset=utf-8");

$username=isset($_POST['username']) ? $_POST['username'] : '';
$password=isset($_POST['password']) ? $_POST['password'] : '';
$array = array();
$sql_query_one="select * from user where username='".$username."' and password='".$password."'";
$userid= mysql_fetch_array(mysql_query($sql_query_one,$conn));
if ($userid) {
    $array["status"]=200;
    $array["str_sql"]=$sql_query_one;
    $_SESSION['user'] =$username;


}
else {
    $array["status"]=404;


}
echo json_encode($array);

?>