layui.use(['form','layer'],function(){
    var form = layui.form,
        layer = parent.layer === undefined ? layui.layer : top.layer,
        mylayer = layui.layer, //内框架内部使用标准layer,不然会变成top.layer，导致失效
        $ = layui.jquery;
    form.render();//再执行一次表单刷新

    $.get(host_url+"index.php/Admin/UserInfo/showRole",function (json) {
        if(json.code == 200){
            let data = json.data;
            for(let i=0; i<data.length; i++){
                $("#role_id").append("<option value='"+data[i].role_id+"'>"+data[i].role+"</option>");
            }
            let role_id = window.sessionStorage.getItem("temp_role_id");
            $("#role_id").val(role_id);//赋值role_id给select下拉框 （会员等级）
            window.sessionStorage.removeItem("temp_role_id");
            form.render("select");
        }else {
            layer.msg("出现未知错误");
        }
    });

    /**
     * 格式要求提示
     */
    $("#username").bind("focus",function () {
        //tips层
        mylayer.tips('需包含大写和小写英文字母', '#username');
    });
    $("#password").bind("focus",function () {
        mylayer.tips('需包含数字，特殊符号，大写，小写英文字母', '#password');
    });
    $("#email").bind("focus",function () {
        mylayer.tips('注意是邮箱格式', '#email');
    });


    /**
     *  提交表单
     */
    $("#submit").bind("click",function(){
        let id = $("#id").val();
        let username = $("#username").val();
        let password = $("#password1").val();
        let email = $("#email").val();
        let sex = $("input[name='sex']:checked").val();
        let role_id = $("#role_id").val();
        let status_bool =  $("#status_bool").val();
        let user_profiles = $("#user_profiles").val();
        //------有密码
        if($("#password").val() && $("#password1").val()){
            //验证两个密码是否一致
            if($("#password").val() == $("#password1").val()){
                //表单验证
                let ubool = Pattern(username,"username");
                let pbool = Pattern(password,"password");
                let ebool = Pattern(email,"email");
                if(ubool && pbool &&ebool){
                    //弹出loading
                    var index = top.layer.msg('数据提交中，请稍候',{icon: 16,time:false,shade:0.8});
                    //提交信息
                    $.post(host_url+"index.php/Admin/UserInfo/editUser",
                        {
                            id : id, //id主键
                            username: username,  //用户名
                            password:password,//密码
                            email : email,  //邮箱
                            sex : sex,  //性别
                            role_id : role_id,  //会员等级
                            status_bool : status_bool,    //用户状态
                            user_profiles : user_profiles,    //用户简介
                        },
                        function(json){
                            console.log(json);
                            if(json.code == 200){
                                setTimeout(function(){
                                    top.layer.close(index);//关闭loading
                                    top.layer.msg("编辑成功！",{icon: 1});
                                    layer.closeAll("iframe");
                                    //刷新父页面
                                    parent.location.reload();
                                },1000);
                            }
                        })
                }else {
                    top.layer.close(index);//关闭loading
                    //提示验证结果
                    var checkResult = {//验证结果
                        untip :  ubool?"":"用户名  ",
                        pdtip : pbool?"":"密码  ",
                        emtip : ebool?"":"邮箱  "
                    }
                    layer.msg(checkResult.untip+checkResult.pdtip+checkResult.emtip+" 格式不对!" );
                }
            }else {
                //提示两次密码错误
                layer.msg("两次密码输入不一致");
                top.layer.close(index);//关闭loading
            }
        }else {
            //---------没有密码
            //表单验证
            let ubool = Pattern(username,"username");
            let ebool = Pattern(email,"email");
            if(ebool){
                //弹出loading
                var index = top.layer.msg('数据提交中，请稍候',{icon: 16,time:false,shade:0.8});
                //提交信息
                $.post(host_url+"index.php/Admin/UserInfo/editUser",
                    {
                        id : id, //id主键
                        username: username,  //用户名
                        email : email,  //邮箱
                        sex : sex,  //性别
                        role_id : role_id,  //会员等级
                        status_bool : status_bool,    //用户状态
                        user_profiles : user_profiles,    //用户简介
                    },
                    function(json){
                        console.log(json);
                        if(json.code == 200){
                            setTimeout(function(){
                                top.layer.close(index);//关闭loading
                                top.layer.msg("编辑成功！",{icon: 1});
                                layer.closeAll("iframe");
                                //刷新父页面
                                parent.location.reload();
                            },1000);
                        }
                    })
            }else {
                top.layer.close(index);//关闭loading
                //提示验证结果
                var checkResult = {//验证结果
                    untip :  ubool?"":"用户名  ",
                    emtip : ebool?"":"邮箱  "
                }
                layer.msg(checkResult.pdtip+checkResult.emtip+" 格式不对!" );
            }
        }

    })


    /**
     *  取消
     */
    $("#cancel").bind("click",function () {
        top.layer.msg("正在退出！",{icon: 2});
        setTimeout(function () {
            layer.closeAll("iframe");
            //刷新父页面
            parent.location.reload();
        },1000)
    });

})