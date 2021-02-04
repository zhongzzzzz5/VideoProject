layui.use(['form','layer','jquery'],function(){
    var layer = parent.layer === undefined ? layui.layer : top.layer,  $ = layui.jquery;

    $("#submit").bind("click",function () {
        if($("#password").val()&&$("#password1").val()){
            if($("#password").val() == $("#password1").val()){
                window.sessionStorage.setItem("lock_password",md5($("#password").val()));
                if($("#describe").val()){
                    window.sessionStorage.setItem("lock_describe",$("#describe").val());
                }
                layer.msg("设置成功!",{icon:1});
                setTimeout(function () {
                    window.parent.location.href = "../../index.html";
                },500)
            }else {
                layer.msg("两次密码输入不一样");
            }
        }else {
            layer.msg("请输入密码");
        }

    });
})