layui.use(['form','upload','jquery','layer','table'],function () {
    var form = layui.form,
        upload = layui.upload,
        $ = layui.jquery,
        table = layui.table ,
        layer = parent.layer === undefined ? layui.layer : top.layer;

    //监听收件人框
    var openIndex;
    $("#addressee").focus(function () {
        openIndex = layer.open({
            type: 2,
            offset: ['180px', '700px'],
            title: '选择收件人',
            move: false,
            shadeClose: true,
            shade: 0.1,
            area: ['420px', '420px'],
            content: 'page/message/email/email_table.html',
            end:function () {
                if(window.sessionStorage.getItem("okObj")){
                    var okObj = JSON.parse(window.sessionStorage.getItem("okObj"));
                    window.sessionStorage.removeItem("okObj");
                    let str_username = '';
                    let str_ids = '';
                    for(let i=0; i<okObj.newUserName.length; i++){
                        if(i==okObj.newUserName.length-1){
                            str_username += okObj.newUserName[i];
                            str_ids += okObj.newsId[i];
                        }else {
                            str_username += okObj.newUserName[i] +',';
                            str_ids += okObj.newsId[i] +',';
                        }
                    }
                    $("#addressee").val(str_username);
                    $("#addressee_ids").val(str_ids);
                }else {
                    $("#addressee").val(null);
                    $("#addressee_ids").val(null);
                }
            }
        });
    });



    //多文件列表示例
    var demoListView = $('#demoList')
        ,uploadListIns = upload.render({
        elem: '#testList'
        ,url: 'https://httpbin.org/post' //改成您自己的上传接口
        ,accept: 'file'
        ,multiple: true
        ,auto: false
        ,bindAction: '#submit'
        ,choose: function(obj){
            var files = this.files = obj.pushFile(); //将每次选择的文件追加到文件队列
            //读取本地文件
            obj.preview(function(index, file, result){
                var tr = $(['<tr id="upload-'+ index +'">'
                    ,'<td>'+ file.name +'</td>'
                    ,'<td>'+ (file.size/1024).toFixed(1) +'kb</td>'
                    ,'<td>等待上传</td>'
                    ,'<td>'
                    ,'<button class="layui-btn layui-btn-xs demo-reload layui-hide">重传</button>'
                    ,'<button class="layui-btn layui-btn-xs layui-btn-danger demo-delete">删除</button>'
                    ,'</td>'
                    ,'</tr>'].join(''));

                //单个重传
                tr.find('.demo-reload').on('click', function(){
                    obj.upload(index, file);
                });

                //删除
                tr.find('.demo-delete').on('click', function(){
                    delete files[index]; //删除对应的文件
                    tr.remove();
                    uploadListIns.config.elem.next()[0].value = ''; //清空 input file 值，以免删除后出现同名文件不可选
                });

                demoListView.append(tr);
            });
        }
        ,done: function(res, index, upload){
            if(res.files.file){ //上传成功
                var tr = demoListView.find('tr#upload-'+ index)
                    ,tds = tr.children();
                tds.eq(2).html('<span style="color: #5FB878;">上传成功</span>');
                tds.eq(3).html(''); //清空操作
                return delete this.files[index]; //删除文件队列已经上传成功的文件
            }
            this.error(index, upload);
        }
        ,error: function(index, upload){
            var tr = demoListView.find('tr#upload-'+ index)
                ,tds = tr.children();
            tds.eq(2).html('<span style="color: #FF5722;">上传失败</span>');
            tds.eq(3).find('.demo-reload').removeClass('layui-hide'); //显示重传
        }
    });


    //表单提交
    $("#submit").bind('click',function () {
        let addressee = $("#addressee").val();
        let subject = $("#subject").val();
        let content = editor.txt.html();

        if(addressee && subject && content){
            console.log(addressee+","+subject+","+content)

        }else {
            layer.msg("发件人,主题,正文内容 必填!!!",{icon:7});
            if(!addressee){
                setTimeout(()=>{
                    $("#addressee").focus();
                },500)
            }else {
                $("#subject").focus();
            }
        }

    });

});