
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
    <TITLE> New Document </TITLE>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>
<link type="text/css" rel="styleSheet"  href="css/reg.css" />
<body>

	<input id="username" type="text" name="username">
	<input id="password" type="password" name="password" >
	<input id="btn_login" type="button" value="登录" name="btn_login">
	<a href="reg.php">注册</a>

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
                url: 'login.php',
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