layui.use(['form','jquery','layer'], function() {
    var form = layui.form;
    var $ = layui.jquery;
    layer = layui.layer;

    form.render();//执行渲染表单

    form.on('select(way)', function(data){
        console.log(data.value);
        if(data.value == ""){
            $("#new-item").slideUp(500,function () {
                $("#new-item")?$("#new-item").remove():console.log("no new-item");
            });
        }
        if(data.value == 1){  //邮箱方式
            $("#new-item")?$("#new-item").remove():console.log("no new-item");
            $("#item-way").after(
                "        <div class=\"layui-form-item\" id='new-item' style='display: none'>\n" +
                "                   <label class=\"layui-form-label\">邮箱：</label>\n" +
                "                   <div class=\"layui-input-block\" style='display: flex'>\n" +
                "                       <input type=\"text\" value=\"\" id=\"email\" placeholder=\"请输入邮箱\" lay-verify=\"required\" style='flex: 5' class=\"layui-input\">\n" +
                "                       <button class=\"layui-btn layui-btn-normal\" id='sendEmail' type='button' style='flex: 1'>发送验证码</button>"+
                "                    </div>\n" +"<br/>"+
                "               </div>"

            );
            $("#new-item").slideDown(function () {
                $("#sendEmail").bind('click',function () {
                    var un = $("#username");
                    var em = $("#email");
                    if(un.val() && em.val()){
                        let em_bool = Pattern(em.val(),"email");//验证邮箱格式
                        if(em_bool){
                            let index = top.layer.msg('执行中，请稍候',{icon: 16,time:false,shade:0.8});
                            $.post(host_url+"index.php/Admin/Public/forgetPassword",
                                {
                                    "username":un.val(),
                                    "email":em.val()
                                },
                                function (json) {
                                    layer.close(index);
                                    if(json.code == 200){
                                        if(json.result == 1){
                                            $("#sendEmail").attr("disabled",true);
                                            var id = json.data.id;//用户主键
                                            let resIndex = top.layer.msg('邮箱发送成功，请注意查收',{icon: 16,time:false,shade:0.8})
                                            var timer = setInterval(function () {
                                                $.post(host_url+"index.php/Admin/Public/monitor",
                                                    {
                                                        "id":id
                                                    },
                                                    function (json) {
                                                        if(json.status == 1){ //用户在邮箱验证成功
                                                            layer.close(resIndex);
                                                            clearInterval(timer);
                                                            window.sessionStorage.setItem("temp_id",id);//临时id主键
                                                            top.layer.msg("验证成功！......",{icon:1});
                                                            setTimeout(function () {
                                                                layer.open({
                                                                    type: 2,
                                                                    title: '重置密码',
                                                                    shadeClose: false,
                                                                    shade: 0.5,
                                                                    area: ['600px', '60%'],
                                                                    content: 'reset_password.html' //iframe的url
                                                                });
                                                            },1000)
                                                        }
                                                    }
                                                );
                                            },800)
                                        }
                                        if(json.result == 0){
                                            layer.open({
                                                type: 1,
                                                skin: 'layui-layer-rim', //加上边框
                                                area: ['420px', '240px'], //宽高
                                                content: '<div style="text-align: center;font-size: 20px;margin-top: 20px">由于您的账户没有设置过邮箱</div><br><p style="text-align: center">请联系管理员修改密码: QQ: 2459450271</p>',
                                                cancel:function () {
                                                    window.parent.location.href = "login.html";
                                                }
                                            });
                                        }
                                        if(json.result == -1){
                                            layer.alert("提交的邮箱不正确，请重新提交",{icon:7});
                                        }
                                        if(json.result == -2){
                                            layer.alert("邮箱发送失败，服务器出现异常",{icoan:7},function () {
                                                window.parent.location.href = "login.html";
                                            });
                                        }
                                    }else if(json.code == 500){
                                        layer.msg("用户名不正确！",{icon:5});
                                    }else if(json.code == 400){
                                        layer.msg("出现未知错误，请稍后再试",{icon:5});
                                    }
                                }
                            )
                        }else {
                            em.focus();
                            layer.msg("邮箱格式不正确！",{icon:7});
                        }
                    }else {
                        un.val()?em.focus():un.focus();
                        layer.msg("用户名和邮箱必填",{icon:7});
                    }
                });
            });//动画显示邮箱框
        }
        if(data.value == 2){ //手机短信方式
            $("#new-item")?$("#new-item").remove():console.log("no new-item");
            $("#item-way").after(
                "        <div class=\"layui-form-item new-item\" id='new-item' style='display: none'>\n" +
                "                   <label class=\"layui-form-label\">手机号码：</label>\n" +
                "                   <div class=\"layui-input-block\" style='display: flex'>\n" +
                "                       <input type=\"text\" value=\"\" id=\"email\" placeholder=\"请输入手机号码\" lay-verify=\"required\" style='flex: 5'  class=\"layui-input\">\n" +
                "                       <button class=\"layui-btn layui-btn-normal\" id='sendEmail' type='button' style='flex: 1'>发送验证码</button>"+
                "                    </div>\n" +"<br/>"+
                "                    <div class=\"layui-input-block\"><input type='text' placeholder='请输入获得的验证码*' style='border: 1px solid #01AAED' class='layui-input'></div> "+
                "                   <div class=\"layui-input-block\">\n" +
                "                       <br><button class=\"layui-btn\" type=\"button\" id=\"submit\" lay-filter=\"changePwd\">提交验证码</button>\n" +
                "                   </div>\n" +
                "               </div>"
            );
            $("#new-item").slideDown();//动画显示手机号码框
        }
    });

});