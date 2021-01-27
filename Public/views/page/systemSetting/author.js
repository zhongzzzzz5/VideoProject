layui.use(["upload","layer","jquery"], function() {
    var form = layui.form, $ = layui.jquery , upload = layui.upload, layer = parent.layer === undefined ? layui.layer : top.layer ,mylayer = layui.layer;

    /**
     *  格式要求提示
     */
    $("#author_wx").bind("focus",function () {
        mylayer.tips('注意是微信号，请规范填写', '#author_wx');
    });
    $("#author_qq").bind("focus",function () {
        mylayer.tips('注意是QQ邮箱，请规范填写', '#author_qq');
    });

    /**
     *  填充数据
     */
    var id = 0; //主键
    $.get(host_url+"index.php/Admin/System/showAuthor",function (json) {
        id = json.data[0].id;
        $("#author").val(json.data[0].author);
        $("#author_wx").val(json.data[0].author_wx);
        $("#wx_img_name").val(json.data[0].wx_img_name);
        $("#wx_img_path").val(json.data[0].wx_img_path);
        $('#demo1').attr('src', json.data[0].wx_img_path_online); //图片链接（base64）
        $("#author_qq").val(json.data[0].author_qq);
        $("#qq_img_name").val(json.data[0].qq_img_name);
        $("#qq_img_path").val(json.data[0].qq_img_path);
        $('#demo2').attr('src', json.data[0].qq_img_path_online); //图片链接（base64）
        $("#content").val(json.data[0].content);
        form.render();//渲染表单
    });


    //微信图片上传
    var uploadInst = upload.render({
        elem: '#test1'
        ,url: host_url+'index.php/Admin/System/uploadQR' //改成您自己的上传接口
        ,before: function(obj){
            //预读本地文件示例，不支持ie8
            obj.preview(function(index, file, result){
                $('#demo1').attr('src', result); //图片链接（base64）
            });
        }
        ,done: function(res){
            //如果上传失败
            if(res.code != 200){
                return layer.msg('上传失败');
            }
            //上传成功
            if(res.code = 200){
                let wx_img_name = res.data.file_name;
                let wx_img_path = res.data.file_url;
                $("#wx_img_name").val(wx_img_name);
                $("#wx_img_path").val(wx_img_path);
                layer.msg("上传成功");
            }
        }
        ,error: function(){
            //演示失败状态，并实现重传
            var demoText = $('#demoText');
            demoText.html('<span style="color: #FF5722;">上传失败</span> <a class="layui-btn layui-btn-xs demo-reload">重试</a>');
            demoText.find('.demo-reload').on('click', function(){
                uploadInst.upload();
            });
        }
    });

    //QQ图片上传
    var uploadInst1 = upload.render({
        elem: '#test2'
        ,url: host_url+'index.php/Admin/System/uploadQR' //改成您自己的上传接口
        ,before: function(obj){
            //预读本地文件示例，不支持ie8
            obj.preview(function(index, file, result){
                $('#demo2').attr('src', result); //图片链接（base64）
            });
        }
        ,done: function(res){
            //如果上传失败
            if(res.code != 200){
                return layer.msg('上传失败');
            }
            //上传成功
            if(res.code = 200){
                let qq_img_name = res.data.file_name;
                let qq_img_path = res.data.file_url;
                $("#qq_img_name").val(qq_img_name);
                $("#qq_img_path").val(qq_img_path);
                layer.msg("上传成功");
            }
        }
        ,error: function(){
            //演示失败状态，并实现重传
            var demoText1 = $('#demoText1');
            demoText1.html('<span style="color: #FF5722;">上传失败</span> <a class="layui-btn layui-btn-xs demo-reload">重试</a>');
            demoText1.find('.demo-reload').on('click', function(){
                uploadInst.upload();
            });
        }
    });

    /**
     *  提交表单
     */
    $("#submit").bind("click",function () {
        //表单验证
        if(Pattern($("#author").val(),"name") && Pattern($("#author_qq").val(),"email")){
            //提交
            if(id){
                $.post(host_url+"index.php/Admin/System/editAuthor",
                    {
                        "id":id, //主键
                        "author":$("#author").val(),
                        "author_wx":$("#author_wx").val(),
                        "wx_img_name":$("#wx_img_name").val(),
                        "wx_img_path":$("#wx_img_path").val(),
                        "author_qq":$("#author_qq").val(),
                        "qq_img_name":$("#qq_img_name").val(),
                        "qq_img_path":$("#qq_img_path").val(),
                        "content":$("#content").val()
                    },
                    function (json) {
                        if(json.code == 200){
                            layer.msg("更新数据成功!");
                            setTimeout(function () {
                                window.parent.location.href = "../../index.html";
                            },500)
                        }
                    }
                );
            }else {
                $.post(host_url+"index.php/Admin/System/contactAuthor",
                    {
                        "author":$("#author").val(),
                        "author_wx":$("#author_wx").val(),
                        "wx_img_name":$("#wx_img_name").val(),
                        "wx_img_path":$("#wx_img_path").val(),
                        "author_qq":$("#author_qq").val(),
                        "qq_img_name":$("#qq_img_name").val(),
                        "qq_img_path":$("#qq_img_path").val(),
                        "content":$("#content").val()
                    },
                    function (json) {
                        if(json.code == 200){
                            layer.msg("设置成功!");
                            setTimeout(function () {
                                window.parent.location.href = "../../index.html";
                            },500)
                        }
                    }
                );
            }
        }else {
            //提示验证错误
            var formVerify={   //默认验证结果
                author : '' ,
                author_qq: ''
            }
            if(!Pattern($("#author").val(),"name")){
                formVerify.author = "作者名字 "
            }
            if(!Pattern($("#author_qq").val(),"email")){
                formVerify.author_qq = "作者QQ ";
            }
            //layer提示
            layer.msg(formVerify.author+formVerify.author_qq+""+"   输入有误 !");
        }
    });

});