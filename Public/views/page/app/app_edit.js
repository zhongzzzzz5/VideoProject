layui.use(['form','layer','upload','jquery'],function () {
    var form = layui.form,
        layer = parent.layer === undefined ? layui.layer : top.layer,
        $ = layui.jquery,
        upload = layui.upload;

    form.render();//执行填充表单

    //id主键
    const id = window.sessionStorage.getItem("temp_id");
    window.sessionStorage.removeItem("temp_id");

    //上传logo
    upload.render({
        elem: '.linkLogo',
        url: host_url+'index.php/Admin/AppManagement/uploadLogo',
        method : "post",  //此处是为了演示之用，实际使用中请将此删除，默认用post方式提交
        data:{
            id:id
        },
        done: function(json, index, upload){
            console.log(json);
            if(json.code == 200){
                layer.msg('logo上传成功',{icon:1});
                $(".linkLogoImg").attr("src",json.others.app_logo_path);
            }
        }
    });


    //上传文件
    var indexFile;
    upload.render({
        elem: '#test8'
        ,url: host_url+'index.php/Admin/AppManagement/uploadFile' //改成您自己的上传接口
        ,method: "post"
        ,data:{
            id:id
        }
        ,auto:false
        ,bindAction: '#test9'
        //,multiple: true
        ,accept: 'file' //普通文件
        ,exts: 'apk|zip|rar|7z' ,//只允许上传该后缀的文件
        before:function (obj) {  //上传前回调，一般用于loading
            console.log(id);
            indexFile = top.layer.msg('正在上传文件，请稍等...',{icon: 16,time:false,shade:0.8});
        }
        ,done: function(json){
            console.log(json);
            if(json.code == 200){
                layer.close(indexFile);//关闭loading
                layer.msg('文件上传成功',{icon:1});
                $(".server_app_file_path").val(json.others.server_file_path);
            }else {
                layer.close(indexFile);
                layer.msg('文件上传出错',{icon:2});
            }
        }
    });

    //监听开启关闭框
    var form_app_status = $(".app_status").attr("checked") == "checked"?1:0;//初始状态
    console.log(id)
    console.log(form_app_status);
    form.on('switch(switchTest)',function () {
        if(this.checked){
            form_app_status = this.checked? 1 : 0;//1，开  0，关
            layer.msg('开关：'+ (form_app_status? "开" : '关'), {offset: '6px'});
        }else{
            layer.msg('状态： 关', {offset: '6px'});
            form_app_status = 0;
        }
        //do some ajax opeartiopns;
    });

    //提交表单
    $("#submit").bind("click",function () {
        $.post(host_url+"index.php/Admin/AppManagement/editApp",
            {
                "id":id,
                "app_name":$(".app_name").val(),
                "app_user_email":$(".app_user_email").val(),
                "app_status":form_app_status
            },
            function (json) {
                console.log(json);
                if(json.code == 200){
                    layer.msg("编辑成功!",{icon:1});
                    setTimeout(function () {
                        layer.closeAll("iframe");
                        //刷新父页面
                        $(".layui-tab-item.layui-show",parent.document).find("iframe")[0].contentWindow.location.reload();
                    },800)
                }else {
                    layer.msg("服务器出差了",{icon:5});
                }
            }
        );
    });


    /**
     *  取消
     */
    $("#cancel").bind("click",function () {
        top.layer.msg("正在退出！",{icon: 2});
        setTimeout(function () {
            layer.closeAll("iframe");
            //刷新父页面
            $(".layui-tab-item.layui-show",parent.document).find("iframe")[0].contentWindow.location.reload();
        },1000)
    });

});