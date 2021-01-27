layui.use(['form','layer','jquery'],function () {
    var form = layui.form, $ = layui.jquery, layer = parent.layer === undefined ? layui.layer : top.layer;
    form.render();

    var old_val = {  //旧数据
       "old_category_name" : $("#category_name").val(),
       "old_category_describe" : $("#category_describe").val()
    }
    $("#submit").bind('click',function () {
        let category_id = $("#category_id");
        let category_name = $("#category_name");
        let category_describe = $("#category_describe");
        if(category_id.val() && category_name.val()){ //判断旧数据是否被修改了
            if((old_val.old_category_name == category_name.val()) && (old_val.old_category_describe == category_describe.val())){
                layer.msg("未修改值，请勿提交",{icon:7});
                return;
            }
            let indexAdd = top.layer.msg('正在操作，请稍后',{icon: 16,time:false,shade:0.8});
            $.post(host_url+"index.php/Admin/VideoApiCategory/editApiCategory",
                {
                    "category_id":category_id.val(),
                    "category_name":category_name.val(),
                    "category_describe":category_describe.val()
                },
                function (json) {
                    if(json.code == 200){
                        layer.close(indexAdd);
                        layer.msg("编辑成功",{icon:1});
                        setTimeout(function () {
                            layer.closeAll("iframe");
                            //刷新父页面
                            $(".layui-tab-item.layui-show",parent.document).find("iframe")[0].contentWindow.location.reload();
                        },800)
                    }else {
                        layer.close(indexAdd);
                        layer.msg("编辑失败，请稍后再试",{icon:5});
                    }
                }
            );
        }else {
            layer.msg("请输入类别",{icon:7});
        }
    });

});