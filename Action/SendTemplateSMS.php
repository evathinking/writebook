<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/5 0005
 * Time: 上午 10:05
 */
if (!session_id()) session_start();
if (isset($_SESSION['time']))//判断缓存时间
{
    session_id();
    $_SESSION['time'];
}
else
{
    $_SESSION['time'] = date("Y-m-d H:i:s");
}
$mobile=isset($_POST['mobile']) ? $_POST['mobile'] : '';
$_SESSION['mcode']=substr(strval(rand(10000,19999)),1,4);
function Get($url)
{
    $ch = curl_init();
    $timeout = 5;
    curl_setopt ($ch, CURLOPT_URL, $url);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $file_contents = curl_exec($ch);
    curl_close($ch);
    return $file_contents;
}

if($mobile) {
    $url='http://utf8.api.smschinese.cn/?Uid=evathinking&Key=d41d8cd98f00b204e980&smsMob='.$mobile.'&smsText=注册验证码：'.$_SESSION['mcode'].'，10分钟内有效。';
    $_SESSION['mobile']=$mobile;
//    $res = Get($url);
//    if ($res) {
//     	$array["status"]=200;
//     	$array["res"]=$res;
//        $_SESSION['mobile']=$mobile;
//     } else {
//        $array["status"]=404;
//     	$array["error"]=$res;
//     }
    $array["status"]=200;
    $array["mcode"]= $_SESSION['mcode'];
    echo json_encode($array);


}
?>