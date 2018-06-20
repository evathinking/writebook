<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
    <TITLE> New Document </TITLE>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>
<link type="text/css" rel="styleSheet"  href="css/style.css" />
<?php
include "head.php";
?>

<body>

    <div class="ch1" style="height: 80%">
<table>
    <tr>
        <td width="90px"><label>旧密码：</label></td>
        <td><input type="password" name="oldpasswd" id="oldpasswd"/> </td>
    </tr>
    <tr>
        <td><label>新密码：</label></td>
        <td><input type="password" name="newpasswd" id="newpasswd"/> </td>
    </tr>
    <tr>
        <td><label>确认密码：</label></td>
        <td><input type="password" name="confirmpasswd" id="confirmpasswd"/> </td>
    </tr>
</table>

<div class="ch2"><input type="button" name="bt_chpw" id="bt_chpw" value="更新密码"/></div>
    </div>

<div class="mask opacity"></div>
<div id="response"></div>

</body>
</html>
<script type="text/javascript" src="js/jquery-3.3.1.min.js"></script>
<script>
    $(document).ready(function(){

        var btn_login=document.getElementById("btn_login");
        btn_login.onclick=function(){
            var username = $.trim($('#username').val());
            var password = $.trim($('#password').val());
            $.ajax({
                url: 'Action/login.php',
                type: 'post',
                dataType: 'json',
                data:{
                    "username":username, "password":password
                },
                success: function(res) {
                    if (res.status == 200) {
                        location.href = 'people.php';
                    } else {
                        $('.mask').css('display', 'block');
                        $("#response").fadeIn("slow");
                        $("#response").html("密码错误");
                        $("#response").fadeOut("slow");
                        setTimeout( () => {$('.mask').css('display', 'none')},1500);


                    }
                }

            });

        };


    });
</script>
<?php
include "footer.php";
?>
