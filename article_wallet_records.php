<?php
if (!isset($_SESSION["user"])) {
    echo "<script>alert('未登录')</script>";
    echo "<script>location.href='index.php';</script>";
}
include "DAO\wallet.php";
$wa= new wallet();
$unlock_records=$wa->unlock_records($_SESSION["user"]);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML xmlns="http://www.w3.org/1999/html">
<HEAD>
    <TITLE>解锁记录</TITLE>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link type="text/css" rel="styleSheet" href="css/style.css"/>
    <script type="text/javascript" src="js/jquery-3.3.1.min.js"></script>

</HEAD>
</body>
<?php
include "head.php";
?>

<div class="ch1"  style="height: 100%;width: 50%">
    <p>小于3个月的解锁记录可以取消</br></p>
    <table id="record_table" class="record_table" style="width:100%">
        <thead>
        <tr>

            <th width="15%">金额</th>
            <th  width="20%">时间</th>
            <th>状态</th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach($unlock_records as $unlock){

        ?>
        <tr>

            <td width="20%"><?php echo $unlock["money_value"];?></td>
            <td width="40%"><?php echo $unlock["lock_time"];?></td>

            <td>解锁成功
                <?php
                if(time() - strtotime($unlock["lock_time"]) > 30*24*3600)
                {
                    echo "<button type=\"button\">取消解锁</button>";
                }


                ?>


              </td>
        </tr>
            <?php
        }
        ?>
        </tbody>

    </table>

</div>


</BODY>
</HTML>
<?php
include "footer.php";
?>

