<?php
include "..\DAO\article.php";
include "..\DAO\moneyman.php";
if (!isset($_SESSION["user"])){
    echo "<script>alert('请先登录!')</script>";
    echo "<script>location.href='index.php';</script>";
}
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods:POST');
header('Access-Control-Allow-Headers:x-requested-with,content-type');
header("Content-Type: text/html;charset=utf-8");
$action = isset($_GET['action']) ? $_GET['action'] : '';
if ($action == "add") {
    $title = isset($_POST['title']) ? $_POST['title'] : '';
    $content = isset($_POST['content']) ? $_POST['content'] : '';
    $tag = isset($_POST['tag']) ? $_POST['tag'] : '';
    $ar = new article();

    $reward_money=$ar->add_article($title, $content, $tag, $_SESSION["user"]);
    if ($reward_money>=0) {
        $array["status"] = 200;
        $array["money"] = $reward_money;
    }
    else{
        $array["status"] = 404;
    }
    echo json_encode($array);
}

?>