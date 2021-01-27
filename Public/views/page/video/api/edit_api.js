layui.use(['form','layer','jquery'],function () {
    var form = layui.form ,layer = parent.layer === undefined ? layui.layer : top.layer , $ = layui.jquery;
    editor.txt.html(window.sessionStorage.getItem("temp_text")); // 设置富文本内容
    form.render();//再次填充表单
    window.sessionStorage.removeItem("temp_text");//删除临时内容

    var category_id = 0;//初始化接口分类主键
    $.get(host_url+"index.php/Admin/VideoApi/api_category",function (json) {
        if(json.code == 200){
            let data = json.data;
            let api_category = $("#api_category");
            for(let i=0; i<json.data.length; i++){
                api_category.append("<input type='radio'  id='"+'api_categoryId'+(i+1)+"' value='"+data[i].category_id+"' lay-filter='api_category' name='api_category' title='"+data[i].category_name+"' data-describe='"+data[i].category_describe+"'>");
            }
            category_id = window.sessionStorage.getItem("category_id");//赋值给接口分类主键
            $("#api_category input[value='"+window.sessionStorage.getItem("category_id")+"']").attr("checked","checked");
            window.sessionStorage.removeItem("category_id");
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


    //监听开启关闭框
    var temp_status = $("#api_status").attr("checked")?0:1;//接口状态
    form.on('switch(switchTest)',function () {
        if(this.checked){
            temp_status = this.checked? 0 : 1//1，关  0，开
            layer.msg('状态：'+ (temp_status? "禁用" : '可用'), {offset: '6px'});
        }else{
            layer.msg('状态： 禁用', {offset: '6px'});
            temp_status = 1;
        }
        // console.log(temp_status)
    });

    //提交表单
    $("#submit").bind('click',function () {
        //弹出loading
        var index = top.layer.msg('数据提交中，请稍候',{icon: 16,time:false,shade:0.8});

        let id = $("#id").val();//主键
        let api_name = $("#api_name").val();//接口名
        let api_url = $("#api_url").val();//接口地址
        let api_status = temp_status;//接口状态
        let api_describe = editor.txt.html();//获取接口描述
        if(id && api_name && api_url && category_id){
            $.post(host_url+"index.php/Admin/VideoApi/editApi",
                {
                    "id":id,
                    "api_name":api_name,
                    "api_url":api_url,
                    "api_status":api_status,
                    "api_describe":api_describe,
                    "category_id":category_id
                },
                function (json) {
                    if(json.code == 200){
                        setTimeout(function(){
                            top.layer.close(index);//关闭loading
                            layer.msg("编辑数据成功",{icon:1});
                            layer.closeAll("iframe");
                            //刷新父页面
                            parent.location.reload();
                        },800);
                    }else {
                        layer.msg("编辑数据失败",{icon:5});
                    }
                }
            );
        }else {
            top.layer.close(index);//关闭loading
            layer.msg("接口名、接口地址、接口分类必填哟",{icon:7});
        }
    });
})