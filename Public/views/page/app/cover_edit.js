layui.use(['form','layer','jquery','upload','carousel'], function(){
    var form = layui.form ,layer = parent.layer === undefined ? layui.layer : top.layer , $ = layui.jquery;
    var upload = layui.upload, carousel = layui.carousel;
    //实例富文本编辑器
    var he = HE.getEditor('editor');//其中的editor是文本输入框的id
    he.set($("#temp_editor").html()?$("#temp_editor").html():"");//可以将HTML代码直接填充到编辑器里面，这个可以用于修改或者实时加载内容到编辑器
    form.render();//渲染表单

    //常规轮播
    carousel.render({
        elem: '#carousel'
        ,arrow: 'none' //始终显示箭头
        ,interval:'10000'
        ,width:'150px'
    });


    //多图片上传
    var app_carousel_arr_name = [];
    var app_carousel_arr_path = [];
    upload.render({
        elem: '#test2'
        ,url: host_url+'index.php/Admin/AppManagement/downloadCarousel' //改成您自己的上传接口
        ,multiple: true
        ,before: function(obj){
            //预读本地文件示例，不支持ie8
            obj.preview(function(index, file, result){
                $('#demo2').append('<img width="100" src="'+ result +'" alt="'+ file.name +'" class="layui-upload-img">')
            });
        }
        ,done: function(json){
            console.log(json)
            //上传完毕
            if(json.code == 200){
                app_carousel_arr_name.push(json.data.carousel_name);
                app_carousel_arr_path.push(json.data.carousel_path);
                layer.msg("上传完毕",{icon:1});
                console.log(app_carousel_arr_name);
                console.log(app_carousel_arr_path);
            }
        }
    });


    //提交表单
    $("#submit").bind("click",function () {
        var id = $("#id").val();//获取id（主键）
        var describe = he.getHtml();//获取富文本HTML代码
        var formdata =  app_carousel_arr_path.length?{"id":id,
            "app_carousel_arr_name":app_carousel_arr_name,
            "app_carousel_arr_path":app_carousel_arr_path,
            "app_describe":describe
        }:{ "id":id, "app_describe":describe}
        console.log(formdata);
        if(id && describe){
            $.post(host_url+"index.php/Admin/AppManagement/coverAppEdit",
                formdata,
                function (json) {
                    console.log(json);
                    if(json.code=200){
                        top.layer.msg("编辑成功！",{icon: 1});
                        setTimeout(function () {
                            layer.closeAll("iframe");
                            //刷新父页面
                            parent.location.reload();
                        },800)
                    }
                }
            );
        }

    });



});