<?php
include "..\DAO\conn.php";
include '..\DAO\moneyman.php';
$mm= new moneyman();

header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods:POST');
header('Access-Control-Allow-Headers:x-requested-with,content-type');
header("Content-Type: text/html;charset=utf-8");
if((strtotime($_SESSION['time'])+600)<time()) {//将获取的缓存时间转换成时间戳加上60秒后与当前时间比较，小于当前时间即为过期
    session_destroy();
    unset($_SESSION);
    $array["status"] = 404;
    header("Content-Type: text/html;charset=utf-8");
    $array["error"] = "验证码已过期，请重新获取！";
    echo json_encode($array);
}
else {
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $mobile = isset($_POST['mobile']) ? $_POST['mobile'] : '';
    $code = isset($_POST['mcode']) ? $_POST['mcode'] : '';
    $reg_time = date("Y-m-d H:i:s");

    if ($code == $_SESSION['mcode'] and $mobile = $_SESSION['mobile']) {
        $sql_query_one="select * from user where username='".$username."'";
        $userid= mysql_fetch_array(mysql_query($sql_query_one,$conn));
        if ($userid) {
            $array["status"] = 500;
            $array['error'] = "已注册，重复提交";
        }
        else{
            $sql_string="INSERT INTO user (`username` ,`password`, `reg_time`, `phone`,`email`) VALUES('".$username."','".$password."','".$reg_time."','".$mobile."','".$email."')";
            if (mysql_query($sql_string,$conn)) {
                $array["status"] = 200;
                $array["res"] = "ok";
                //注册送1万颗币代码
                $mm->reg_send_money(10000,$username);
            } else {
                $array["status"] = 400;
                $array['error'] = die('Error: ' . mysql_error());
            }
        }

        echo json_encode($array);
    } else {
        $array["status"] = 404;
        $array["error"] = "验证码错误，请获取验证码！";
        echo json_encode($array);
    }
}

mysql_close($conn);
?>