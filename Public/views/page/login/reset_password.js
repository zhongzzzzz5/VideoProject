layui.use(['form','jquery','layer'],function () {
    var form = layui.form, $ = layui.jquery, layer = parent.layer === undefined ? layui.layer : top.layer;

    $("#submit").bind('click',function () {
        var password = $("#new_password").val();
        var password1 = $("#new_username1").val();
        if(password && password1){
            if(password == password1){
                var pd_bool = Pattern(password1,"password");
                if(pd_bool){
                    let id = window.sessionStorage.getItem("temp_id");
                    window.sessionStorage.removeItem("temp_id");
                    //console.log(id);
                    let indexLoading = top.layer.msg('提交中，请稍候',{icon: 16,time:false,shade:0.8});
                    $.post(host_url+"index.php/Admin/Public/resetPassword",
                        {
                            "id": id,
                            "password":password1
                        },
                        function (json) {
                            layer.close(indexLoading);
                            if(json.code == 200){
                                let indexNewPage = top.layer.msg('重置密码成功，正在跳转到登录页',{icon: 16,time:false,shade:0.8});
                                setTimeout(function () {
                                    layer.close(indexNewPage);
                                    window.parent.location.href = "login.html";
                                },3000)
                            }else {
                                top.layer.msg("出现未知错误，请稍后重试",{icon:7});
                            }
                        }
                    );
                }else {
                    top.layer.msg("密码格式不正确，需包含 数字，大写，小写，英文");
                }
            }else {
                top.layer.msg("两次密码不一致",{icon:7});
            }
        }else {
            top.layer.msg("请输入密码",{icon:7});
        }
    });
});