layui.use(['form','jquery','layer'],function () {
    var form = layui.form ,layer = parent.layer === undefined ? layui.layer : top.layer , $ = layui.jquery;

    var category_id = 0;//初始化接口分类主键
    $.get(host_url+"index.php/Admin/VideoApi/api_category",function (json) {
        if(json.code == 200){
            let data = json.data;
            let api_category = $("#api_category");
            for(let i=0; i<json.data.length; i++){
                api_category.append("<input type='radio'  id='"+'api_categoryId'+(i+1)+"' value='"+data[i].category_id+"' lay-filter='api_category' name='api_category' title='"+data[i].category_name+"' data-describe='"+data[i].category_describe+"'>");
            }
            form.render();//执行渲染表单
            form.on('radio(api_category)', function(data){
                // console.log(data.elem); //得到radio原始DOM对象
                // console.log(data.value); //被点击的radio的value值
                category_id = data.value;
            });
        }else {
            console.log("api_category(接口分类) no data");
        }
    });
    form.render();//执行渲染表单

    //提交表单
    $('#submit').bind('click',function () {
        //弹出loading
        var index = top.layer.msg('数据提交中，请稍候',{icon: 16,time:false,shade:0.8});

        let api_name = $("#api_name").val();//接口名
        let api_url = $("#api_url").val();//接口地址
        let api_describe = editor.txt.html();//获取接口描述

        if(api_name && api_url && category_id){
            $.post(host_url+'index.php/Admin/VideoApi/addApi',
                {
                    "api_name":api_name,
                    "api_url":api_url,
                    "api_describe":api_describe,
                    "category_id":category_id,
                },
                function (json) {
                    if(json.code == 200){
                        setTimeout(function(){
                            top.layer.close(index);//关闭loading
                            layer.msg("接口添加成功",{icon:1});
                            layer.closeAll("iframe");
                            //刷新父页面
                            parent.location.reload();
                        },800);
                    }
                }
            );
        }else {
            top.layer.close(index);//关闭loading
            layer.msg('接口名称、接口地址、接口分类必需填哟',{icon:7});
        }

    });
})