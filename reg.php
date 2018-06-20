<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
 <HEAD>
  <TITLE> New Document </TITLE>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>


 </HEAD>
 <BODY>
 
 <p> 用户名：只能定义一次
  <input id="username" type="text" onblur="check_username(this.value)"><span id="username_notice"></span></p>
 <p> 密码：
  <input id="password" type="password" onblur="check_password(this.value)"><span id="passwd_notice"></span></p>
 <p> 确认密码：
  <input id="conform_password" type="password" onblur="check_conform_password(this.value)" ><span id="confirmp_notice"></span></p>
  <p>邮箱：<input id="email" type="text" onblur="check_email(this.value)" ><span id="email_notice"></span></p>
  <p>手机号：<input type="text" name="mobile" value="" onblur="check_mobile(this.value)" id="mobile" /><span id="mobile_notice"></span></p>
  <p>验证码：<input type="text" name="code" value="" id="code"/><input type="button" id="bt_verify" value="获取验证码"></p>

<input type="button" name="bt_reg" value="注册" id="bt_reg" />
<div id="response"></div>
 </BODY>
</HTML>
<script type="text/javascript" src="https://cdn.bootcss.com/jquery/2.2.3/jquery.min.js"></script>
 <script src="js/regcheck.js"></script>
<script>
    $(document).ready(function(){
        var test = {
            node:null,
            count:60,

            start:function(){
                //console.log(this.count);
                if(this.count > 0){
                    this.node.value ="剩余时间:"+parseInt(this.count--) ;
                    var _this = this;
                    setTimeout(function(){
                        _this.start();
                    },1000);
                }else{
                    this.node.removeAttribute("disabled");
                    this.node.innerHTML = "再次发送";
                    this.count = 60;
                }
            },
            //初始化
            init:function(node){
                this.node = node;
                this.node.setAttribute("disabled",true);
                this.start();
            }
        };
        var btn = document.getElementById("bt_verify");
        btn.onclick = function(){

            test.init(btn);
            var mobile = $.trim($('#mobile').val());
            $.ajax({
                url: "http://localhost:8081/writebook/reg_user.php",
                type: 'post',
                dataType: 'json',
                data: {
                    "action":"verify",
                    "mobile":mobile
                },
                success: function(a) {
                    var b = a.status;
                    var c = a.mcode;
                    if (b == 200) {
                        $("#response").html(c);
                    } else {
                        $("#response").html(c);
                    }
                }
            });

        };
        var btn_reg = document.getElementById("bt_reg");

        btn_reg.onclick = function(){
            var username = $.trim($('#username').val());
            var password = $.trim($('#password').val());
            var mobile = $.trim($('#mobile').val());
            var email = $.trim($('#email').val());
            var mcode = $.trim($('#code').val());
            $.ajax({
                url: "http://localhost:8081/writebook/reg_user.php",
                type: 'post',
                dataType: 'json',
                data: {
                    "action":"reg",
                    "username":username,
                    "password":password,
                    "mobile":mobile,
                    "email":email,
                    "mcode":mcode


                },
                success: function(a) {
                    var b = a.status;
                    var c = a.res;
                    if (b == 200) {
                        $("#response").html(c);
                    } else {
                        $("#response").html(c);
                    }
                }
            });

        }
    });
</script>

Copyright © 2018 WriteBook Foundation Ltd. All Rights Reserved
