 layui.use(['form','layer','jquery'],function(){
    var form = layui.form,
        layer = parent.layer === undefined ? layui.layer : top.layer
        $ = layui.jquery;

    $(".loginBody .seraph").click(function(){
        layer.msg("这只是做个样式，至于功能，你见过哪个后台能这样登录的？还是老老实实的找管理员去注册吧",{
            time:5000
        });
    })

    //若设置记住密码（则填充表单）
    if($.cookie('username') && $.cookie('password') ){
        $("#userName").val($.base64.decode($.cookie('username'))).parent().addClass("layui-input-active");
        $("#password").val($.base64.decode($.cookie('password'))).parent().addClass("layui-input-active");
        $("#remember_password").attr("checked",true);
        form.render();
    }

    //监听记住密码复选框
    var rem_password = false;//是否记住密码
    form.on('checkbox(remember_password)',function (data) {
        // console.log(data.elem); //得到checkbox原始DOM对象
        // console.log(data.elem.checked); //是否被选中，true或者false
        // console.log(data.value); //复选框value值，也可以通过data.elem.value得到
        // console.log(data.othis); //得到美化后的DOM对象
        if(data.elem.checked){
            rem_password = true;
        }else {
            rem_password = false;
            $.removeCookie('username');
            $.removeCookie('password');

        }
    });

    $("#password").on("focus",function () {
        layer.tips('双击密码宽预览密码', '#password');
        $(this).off("focus");
    });
    //双击密码框预览密码
    $("#password").dblclick(function () {
        if($(this).attr("type") == "password"){
            $(this).attr("type","text");
        }else {
            $(this).attr("type","password");
        }
    });

    //显示验证码
    $("#captcha").attr("src",host_url+"index.php/Admin/Public/captcha");
    var old_src = $("#captcha").attr("src");
    $("#captcha").bind("click",function () {
        $("#captcha").attr("src",old_src+'/'+Math.random());
    });

    //异步验证验证码
    const codeOldStyle = $("#code").css("border");//旧样式
    var res_id = 0;//定义主键
    var voucher = null;//定义凭证
    $(document).keyup(function(){
        if($("#code").val().length ==4){
            $.post(host_url+"index.php/Admin/Public/checkCode",
                {
                    "code":$("#code").val()
                },
                function (json) {
                    if(json.code == 200){
                        $("#checkResult").val(json.data.checkResult);//校验结果
                        res_id = json.data.res_id;//主键
                        voucher = json.data.voucher;//凭证
                        layer.msg('验证码正确', {icon: 1});
                        $("#code").css({"border":"1px solid #00d20d"});//验证码正确，改变边框样式
                        $("#imgCode").css({"pointer-events":"none"});//禁止点击验证码盒子
                        setTimeout(function () {
                            $("#imgCode").slideUp(1000);//隐藏验证码盒子
                            $("#code").val(null);//验证成功，清除已经输入的值，避免再次请求服务器
                        },1000);
                    }else {
                        $("#code").css({"border":codeOldStyle});//验证码错误，复原旧为样式
                    }
                }
            );
        }
    });

    //登录按钮提交表单
    $("#submit").bind("click",function(){
        var username = $("#userName").val();
        var password = $("#password").val();
        var checkResult = $("#checkResult").val();

        $("#code").val(null);
        $(this).text("登录中...").attr("disabled","disabled").addClass("layui-disabled");
        var that = $(this);
        if(username && password){
            $.post(host_url+"index.php/Admin/Public/login",
                {"username":username,"password":password,"checkResult":checkResult,"res_id":res_id,"voucher":voucher},
                function (json) {
                    if(json.code == 200){
                        //账号密码正确，重置表单
                        $("#userName").val(null);
                        $("#password").val(null);
                        if(json.login_status == 1){
                            keydown_flag = false;//暂时关闭键盘事件
                            var user_id = json.data.id;
                            var indexTip = top.layer.confirm('该用户已在另一个地方登录,确定强制登录？', {
                                icon:7,
                                btn: ['确定','取消'] //按钮
                            }, function(){
                                top.layer.close(indexTip);
                                keydown_flag = true;//恢复键盘事件
                                $.get(host_url+"index.php/Admin/Public/forcedLogin?id="+user_id,function (json) {
                                    if(json.code==200){
                                        if(json.status_bool == 0){  //用户状态正常
                                            window.sessionStorage.setItem("LOGIN_TIME",json.login_time);//记录登录时间
                                            console.log(json.login_time);
                                            if(json.data.ROLE == -1 ||json.data.ROLE > 0){  //role为0表示普通用户，-1表示超级管理员，大于0表示高级以上用户
                                                $(".layui-form").slideUp(1000);//隐藏layui-form表单
                                                keydown_flag = false;//暂时关闭键盘事件
                                                //loading
                                                var index = top.layer.msg('登录成功，正在跳转...',{icon: 16,time:false,shade:0.8});
                                                //存入相关信息到本地
                                                window.sessionStorage.setItem("UID",json.data.UID);
                                                window.sessionStorage.setItem("UNAME",json.data.UNAME);
                                                if(json.data.UFACE != 0){
                                                    window.sessionStorage.setItem("UFACE",json.data.UFACE);
                                                }else {
                                                    window.sessionStorage.setItem("UFACE","0");
                                                }
                                                window.sessionStorage.setItem("ROLE",json.data.ROLE);

                                                if(rem_password){
                                                    $.cookie('username', $.base64.encode(username), { expires: 7 });//存cookie,有效期7天
                                                    $.cookie('password', $.base64.encode(password), { expires: 7 });
                                                }

                                                setTimeout(function(){
                                                    top.layer.close(index);//关闭loading
                                                    window.parent.location.href = "../../index.html";
                                                },2000);
                                            }else {
                                                keydown_flag = false;//暂时关闭键盘事件
                                                var indexRoleTip = layer.alert("对不起，您没有权限登录管理系统",{icon:5,skin: 'layui-layer-lan'
                                                    ,closeBtn: 0},function () {
                                                    top.layer.close(indexRoleTip);
                                                    that.text("登录").attr("disabled",null).removeClass("layui-disabled");
                                                    $("#imgCode").slideDown(1000);
                                                    $("#imgCode").css({"pointer-events":"auto"});//开启点击验证码盒子
                                                    $("#captcha").click();//刷新验证码
                                                    $("#code").css({"border":codeOldStyle});
                                                    keydown_flag = true;//恢复键盘事件
                                                });
                                            }
                                        }
                                        if(json.status_bool == 1){//用户状态--被禁用
                                            keydown_flag = false;//暂时关闭键盘事件
                                            var indexStatusTip = top.layer.alert("您暂时不能登录(账号被冻结)<br>请联系管理员：qq 2459450271",{icon:5,skin: 'layui-layer-lan'
                                                ,closeBtn: 0},function () {
                                                top.layer.close(indexStatusTip);
                                                that.text("登录").attr("disabled",null).removeClass("layui-disabled");
                                                $("#imgCode").slideDown(1000);
                                                $("#imgCode").css({"pointer-events":"auto"});//开启点击验证码盒子
                                                $("#captcha").click();//刷新验证码
                                                $("#code").css({"border":codeOldStyle});
                                                keydown_flag = true;//恢复键盘事件
                                            });
                                        }
                                    }
                                });
                            },function () {
                                    top.layer.close(indexTip);
                                    that.text("登录").attr("disabled",null).removeClass("layui-disabled");
                                    $("#imgCode").slideDown(1000);
                                    $("#imgCode").css({"pointer-events":"auto"});//开启点击验证码盒子
                                    $("#captcha").click();//刷新验证码
                                    $("#code").css({"border":codeOldStyle});
                                    keydown_flag = true;//恢复键盘事件
                            });

                            // var indexStatusTip = top.layer.alert("该用户已在另一个地方登录,确定强制登录",{icon:5,skin: 'layui-layer-lan'
                            //     ,closeBtn: 0},function () {
                            //     top.layer.close(indexStatusTip);
                            //     that.text("登录").attr("disabled",null).removeClass("layui-disabled");
                            //     $("#imgCode").slideDown(1000);
                            //     $("#imgCode").css({"pointer-events":"auto"});//开启点击验证码盒子
                            //     $("#captcha").click();//刷新验证码
                            //     $("#code").css({"border":codeOldStyle});
                            //     keydown_flag = true;//恢复键盘事件
                            // });
                        }else {
                            if(json.status_bool == 0){  //用户状态正常
                                window.sessionStorage.setItem("LOGIN_TIME",json.login_time);//记录登录时间
                                console.log(json.login_time);
                                if(json.data.ROLE == -1 ||json.data.ROLE > 0){  //role为0表示普通用户，-1表示超级管理员，大于0表示高级以上用户
                                    $(".layui-form").slideUp(1000);//隐藏layui-form表单
                                    keydown_flag = false;//暂时关闭键盘事件
                                    //loading
                                    var index = top.layer.msg('登录成功，正在跳转...',{icon: 16,time:false,shade:0.8});
                                    //存入相关信息到本地
                                    window.sessionStorage.setItem("UID",json.data.UID);
                                    window.sessionStorage.setItem("UNAME",json.data.UNAME);
                                    if(json.data.UFACE != 0){
                                        window.sessionStorage.setItem("UFACE",json.data.UFACE);
                                    }else {
                                        window.sessionStorage.setItem("UFACE","0");
                                    }
                                    window.sessionStorage.setItem("ROLE",json.data.ROLE);

                                    if(rem_password){
                                        $.cookie('username', $.base64.encode(username), { expires: 7 });//存cookie,有效期7天
                                        $.cookie('password', $.base64.encode(password), { expires: 7 });
                                    }

                                    setTimeout(function(){
                                        top.layer.close(index);//关闭loading
                                        window.parent.location.href = "../../index.html";
                                    },2000);
                                }else {
                                    keydown_flag = false;//暂时关闭键盘事件
                                    var indexRoleTip = layer.alert("对不起，您没有权限登录管理系统",{icon:5,skin: 'layui-layer-lan'
                                        ,closeBtn: 0},function () {
                                        top.layer.close(indexRoleTip);
                                        that.text("登录").attr("disabled",null).removeClass("layui-disabled");
                                        $("#imgCode").slideDown(1000);
                                        $("#imgCode").css({"pointer-events":"auto"});//开启点击验证码盒子
                                        $("#captcha").click();//刷新验证码
                                        $("#code").css({"border":codeOldStyle});
                                        keydown_flag = true;//恢复键盘事件
                                    });
                                }
                            }
                            if(json.status_bool == 1){//用户状态--被禁用
                                keydown_flag = false;//暂时关闭键盘事件
                                var indexStatusTip = top.layer.alert("您暂时不能登录(账号被冻结)<br>请联系管理员：qq 2459450271",{icon:5,skin: 'layui-layer-lan'
                                    ,closeBtn: 0},function () {
                                    top.layer.close(indexStatusTip);
                                    that.text("登录").attr("disabled",null).removeClass("layui-disabled");
                                    $("#imgCode").slideDown(1000);
                                    $("#imgCode").css({"pointer-events":"auto"});//开启点击验证码盒子
                                    $("#captcha").click();//刷新验证码
                                    $("#code").css({"border":codeOldStyle});
                                    keydown_flag = true;//恢复键盘事件
                                });
                            }
                        }
                    }
                    if(json.code == -1){
                        setTimeout(function () {
                            layer.msg("验证码错误！",{icon:2});
                        },500);
                    }
                    if(json.code == 500){
                        setTimeout(function () {
                            layer.msg('账户或者密码错误',{icon:5});
                        },500)
                    }
                    if(json.code == 400){
                        setTimeout(function () {
                            layer.msg("客户端请求出错！",{icon:7});
                        },500);
                    }
                    //刷新验证码
                    $("#captcha").attr("src",old_src+'/'+Math.random());
                    that.text("登录").attr("disabled",null).removeClass("layui-disabled");
                });
        }else {
            layer.msg("请输入用户名和密码",{icon:3});
            that.text("登录").attr("disabled",null).removeClass("layui-disabled");
        }
    });

    //按回车键提交个人资料
    var keydown_flag = true;
    $(document).keydown(function(event){
        if(keydown_flag == true){
            if(event.keyCode == 13 || event.keyCode == 108){
                //执行提交表单(出发按钮点击事件)
                $("#submit").click();
            }
        }else {
            return false;
        }

    });



    //表单输入效果
    $(".loginBody .input-item").click(function(e){
        e.stopPropagation();
        $(this).addClass("layui-input-focus").find(".layui-input").focus();
    })
    $(".loginBody .layui-form-item .layui-input").focus(function(){
        $(this).parent().addClass("layui-input-focus");
    })
    $(".loginBody .layui-form-item .layui-input").blur(function(){
        $(this).parent().removeClass("layui-input-focus");
        if($(this).val() != ''){
            $(this).parent().addClass("layui-input-active");
        }else{
            $(this).parent().removeClass("layui-input-active");
        }
    })
})
