layui.use(['form','layer','jquery'],function(){
    var form = layui.form,
        layer = parent.layer === undefined ? layui.layer : top.layer,
        $ = layui.jquery;

        var form_app_status = 0;//初始状态
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

        //点击添加
        $("#submit").bind("click",function () {
            let app_name = $("#app_name").val();
            let app_user_email = $("#app_user_email").val();
            let app_status = form_app_status;
            console.log({
                "app_name":app_name,
                "app_user_email":app_user_email,
                "app_status":app_status
            });
            if(app_name && app_user_email){
                $.post(host_url+"index.php/Admin/AppManagement/addApp",
                    {
                        "app_name":app_name,
                        "app_user_email":app_user_email,
                        "app_status":app_status
                    },
                    function (json) {
                        if(json.code == 200){
                            console.log(json);
                            layer.msg("添加数据成功!",{icon:1});
                            setTimeout(function () {
                                layer.closeAll("iframe");
                                //刷新父页面
                                $(".layui-tab-item.layui-show",parent.document).find("iframe")[0].contentWindow.location.reload();
                            },800)
                        }else {
                            layer.msg("添加数据失败!",{icon:5});
                        }
                    }
                );
            }else {
                layer.msg("请输入完整!",{icon:7});
            }
        });
})