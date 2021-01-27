layui.use(['form','layer'],function(){
    var form = layui.form,
        layer = parent.layer === undefined ? layui.layer : top.layer,
        mylayer = layui.layer, //内框架内部使用标准layer,不然会变成top.layer，导致失效
        $ = layui.jquery;

    $.get(host_url+"index.php/Admin/UserInfo/showRole",function (json) {
        if(json.code == 200){
            let data = json.data;
            for(let i=0; i<data.length; i++){
                $("#role_id").append("<option value='"+data[i].role_id+"'>"+data[i].role+"</option>");
            }
            form.render("select");
        }else {
            layer.msg("出现未知错误");
        }
    });


    /**
     *  格式要求提示
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
     *  验证表单
     */



    /**
     *  异步验证用户名可用性
     */
    var oldstyle = {
        "border":$("#username").css("border"),
        "color":$("#username").css("color")
    };
    var checkUserNameResult = false;//用户名验证结果
    $("#username").blur(function () {
        let ubool = Pattern($(this).val(),"username");
        if(ubool){
            $.post(host_url+"index.php/Admin/UserInfo/checkUserName",
                {
                    username:$("#username").val()
                },
                function (json) {
                    if(json.code == 200){
                        if(json.used == 1){
                            checkUserNameResult = false;
                            $("#username").css({"border":"1px solid red","color":"red"});
                            layer.alert('用户名不可用！', {
                                skin: 'layui-layer-lan'
                                ,closeBtn: 0
                                ,anim: 4 //动画类型
                            });
                        }
                        if(json.used == 0){
                            checkUserNameResult = true;//更改验证结果为true
                            $("#username").css({"border":"1px solid green","color":"green"});
                            //小tips
                            mylayer.tips('用户名可用', '#username', {
                                tips: [1, '#3595CC'],
                                time: 2000
                            });
                        }
                    }else {
                        checkUserNameResult = false;
                        $("#username").css({"border":oldstyle.border,"color":oldstyle.color});
                        layer.msg("出现未知错误");
                    }
                }
            );
        }else {
            checkUserNameResult = false;
            $("#username").css({"border":oldstyle.border,"color":oldstyle.color});
        }

    });


    /**
     *  提交表单
     */
    $("#submit").bind("click",function(){
        let username = $("#username").val();
        let password = $("#password1").val();
        let email = $("#email").val();
        let sex = $("input[name='sex']:checked").val();
        let role_id = $("#role_id").val();
        let status_bool =  $("#status_bool").val();
        let user_profiles = $("#user_profiles").val();
        if(checkUserNameResult){ //用户名可用性是否为true
            //验证两个密码是否一致
            if($("#password").val() == $("#password1").val()){
                //验证密码和邮箱
                let pbool = Pattern(password,"password");
                let ebool = Pattern(email,"email");
                if(pbool &&ebool){ //密码，邮箱
                    //弹出loading
                    var index = top.layer.msg('数据提交中，请稍候',{icon: 16,time:false,shade:0.8});
                    //提交信息
                    $.post(host_url+"index.php/Admin/UserInfo/addUser",
                        {
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
                                    top.layer.msg("用户添加成功！",{icon:6});
                                    layer.closeAll("iframe");
                                    //刷新父页面
                                    parent.location.reload();
                                },1000);
                            }else if(json.code == 500){
                                top.layer.close(index);//关闭loading
                                top.layer.msg("用户名不可用！",{icon: 7});
                            }
                        })
                }else {
                    top.layer.close(index);//关闭loading
                    //提示验证结果
                    var checkResult = {//验证结果
                        pdtip : pbool?"":"密码  ",
                        emtip : ebool?"":"邮箱  "
                    }
                    layer.msg(checkResult.pdtip+checkResult.emtip+" 格式不对!" );
                }
            }else {
                //提示两次密码错误
                layer.msg("两次密码输入不一致");
                top.layer.close(index);//关闭loading
            }
        }else {
            layer.msg("用户名不可用，请重新输入");
        }

    })


})