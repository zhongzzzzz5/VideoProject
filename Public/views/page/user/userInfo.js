var form, $,areaData;
layui.config({
    base : "../../js/"
}).extend({
    "address" : "address"
})
layui.use(['form','layer','upload','laydate',"address"],function(){
    form = layui.form;
    $ = layui.jquery;
    var layer = parent.layer === undefined ? layui.layer : top.layer,
        upload = layui.upload,
        laydate = layui.laydate,
        address = layui.address;

    //获取用户数据，填充表单
    $.get(host_url+"index.php/Admin/UserInfo/showUser?id="+window.sessionStorage.getItem("UID"), function (json) {
            console.log(json);
            json.others.server_head_url?$("#userFace").attr("src",json.others.server_head_url):$("#userFace").attr("src","");//预览头像
            $("#username").val(json.data.username);
            $("#role").val(json.data.role);
            $(".oldUserAddress").val(json.data.home_address_text?json.data.home_address_text:"暂未填写");
            $(".oldSkill").val(json.data.skill?json.data.skill:"暂未填写");
            //填充需要提交的表单部分,总共11个
            $("#id").val(json.data.id);
            $("#file_name").val(json.data.head_img);
            $("#file_url").val(json.data.head_img_path);
            $("#real_name").val(json.data.real_name);
            //性别
            $("input[name='sex']").val([json.data.sex]);

            $("#phone").val(json.data.phone);
            $("#birthday").val(json.data.birthday);

            //家庭住址 ---暂不渲染

            //掌握技术
            $("input[class='skill']").val(json.others.skill_arr);

            $("#email").val(json.data.email);
            $("#self_evaluation").val(json.data.self_evaluation);
            form.render();//执行表单刷新

            //loading层
            var index = layer.load(1, {
                shade: [0.5,'#393D49'] //0.1透明度的白色背景
            });
            //刷新当前页面一次，重点：一次
            if(location.href.indexOf("#reloaded")==-1){
                location.href=location.href+"#reloaded";
                location.reload();
            }
            setTimeout(function () {
                layer.close(index);
            },1000);
        }
     );

    //上传头像
    upload.render({
        elem: '.userFaceBtn',
        url: host_url+'index.php/Admin/UserInfo/uploadHeadImg?id='+window.sessionStorage.getItem("UID"),
        method : "post",  //此处是为了演示之用，实际使用中请将此删除，默认用post方式提交
        done: function(res){
            console.log(res);
            if(res.code == 200){
                $('#userFace').attr('src',res.data.server_img_url);
                $("#file_name").val(res.data.file_name);
                $("#file_url").val(res.data.file_url);
                window.sessionStorage.setItem('UFACE',res.data.server_img_url);//自己要的
                layer.msg("上传成功 ! ",{icon: 1});
                $("#uploadResult").text("上传成功");
            }else {
                layer.msg("上传失败 ! ");
                $("#uploadResult").text("上传失败 ! ");
            }

        }
    });

    //选择出生日期
    laydate.render({
        elem: '.userBirthday',
        format: 'yyyy年MM月dd日',
        trigger: 'click',
        max : 0,
        mark : {"0-10-05":"生日"},
        done: function(value, date){
            if(date.month === 10 && date.date === 5){ //点击每年10月05日，弹出提示语
                layer.msg('今天也是管理员的生日噢');
            }
        }
    });

    //获取省信息
    address.provinces();


    //按按钮提交个人资料
    $("#submit").on("click",function(){
        //获取复选框的值(掌握技能)
        var skillstr = "";
        $("input[class='skill']:checked").each(function (i) {
            skillstr += $(this).val()+",";
        });
        skillstr = skillstr.substring(0,skillstr.length-1);
        // console.log(skillstr);
        var userInfoHtml = {
            "id" : $("#id").val(),
            "head_img":$("#file_name").val(),
            "head_img_path":$("#file_url").val(),
            'real_name' : $("#real_name").val(),
            'sex' : $("input[name='sex']:checked").val(),
            'phone' : $("#phone").val(),
            'birthday' : $("#birthday").val(),
            "skill":skillstr,
            'email' : $("#email").val(),
        };
        //若修改了家庭住址，执行添加家庭住址键值对
        if($("#home_address1").val() && $("#home_address2").val()  && $("#home_address3").val()){
            userInfoHtml["home_address"] = $("#home_address1").val()+","+$("#home_address2").val()+","+$("#home_address3").val();
            userInfoHtml["home_address_text"] = $("#home_address1").children("option[value="+$("#home_address1").val()+"]").text() +","+$("#home_address2").children("option[value="+$("#home_address2").val()+"]").text()+","+$("#home_address3").children("option[value="+$("#home_address3").val()+"]").text();
        }
        //console.log(userInfoHtml);
        var index = layer.msg('提交中，请稍候',{icon: 16,time:false,shade:0.8});
        //将填写的用户信息存到session以便下次调取
        $.post(host_url+"index.php/Admin/UserInfo/editMy",
            userInfoHtml,
            function (json) {
                //console.log(json);
                if(json.code == 200){
                    window.sessionStorage.setItem("userInfo",JSON.stringify(userInfoHtml));
                    layer.close(index);
                    layer.msg("提交成功！正在跳转到首页...");
                    setTimeout(function(){
                        window.parent.location.href = "../../index.html";
                    },2000);
                }else {
                    setTimeout(function(){
                        layer.close(index);
                        layer.msg("提交失败！, 请稍后再试...");
                    },1000);
                }
            }
        );

    })

    //按回车键提交个人资料
    $(document).keydown(function(event){
        if(event.keyCode == 13 || event.keyCode == 108){
            //执行提交表单(出发按钮点击事件)
            $("#submit").click();
        }
    });

})