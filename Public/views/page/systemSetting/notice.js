layui.use(['layer','jquery'], function(){
    var form = layui.form ,layer = parent.layer === undefined ? layui.layer : top.layer , $ = layui.jquery;

    //实例富文本编辑器
    var he = HE.getEditor('editor');//其中的editor是文本输入框的id

    //填充富文本
    $.get(host_url+"index.php/Admin/Index/showNotice",function (json) {
        if(json.code == 200){
            //console.log(json)
            $("#id").val(json.data[0].id);
            $("#title").val(json.data[0].title);
            he.set(json.data[0].content);//可以将HTML代码直接填充到编辑器里面，这个可以用于修改或者实时加载内容到编辑器
            $("#add_time").html(json.data[0].add_time);
        }else {
            $("#title").val("");
            he.set("");//可以将HTML代码直接填充到编辑器里面，这个可以用于修改或者实时加载内容到编辑器
        }
        form.render();//渲染表单
    });
    

    //提交表单
    $("#submit").bind("click",function () {
        var id = $("#id").val();//获取id（主键）
        var title = $("#title").val();//获取标题
        var content = he.getHtml();//获取富文本HTML代码
        if(title && content){
            $.post(host_url+"index.php/Admin/Index/showNotice",
                {
                    "id":id,
                    "title":title,
                    "content":content
                },
                function (json) {
                    //console.log(json);
                    if(json.code=200){
                        layer.msg("编辑成功!,正在跳转到首页",{icon: 1});
                        setTimeout(function () {
                            window.parent.location.href = "../../index.html";
                        },1000)
                    }
                }
            );
        }

    });



});