layui.use(['form','layer','laydate','table','upload'],function(){
    var form = layui.form,
        layer = parent.layer === undefined ? layui.layer : top.layer,
        $ = layui.jquery,
        laydate = layui.laydate,
        upload = layui.upload,
        table = layui.table;

    //软件列表
    var tableIns = table.render({
        elem: '#linkList',
        url : host_url+'index.php/Admin/AppManagement/showAllApp',
        page : true,
        cellMinWidth : 95,
        height : "full-104",
        id : "linkListTab",
        cols : [[
            {type: "checkbox", fixed:"left", width:50},
            {field: 'id', title: 'ID', width:70},
            {field: 'app_logo_path', title: 'LOGO', width:180, align:"center",templet:function(d){
                    return d.server_app_logo_path? '<span href="'+d.server_app_logo_path+'" target="_blank"><img src="'+d.server_app_logo_path+'" height="26" /></span>':"暂无";
                }},
            {field: 'app_name', title: '软件名称', minWidth:240},
            {field: 'app_file_path', title: '下载地址',width:300,templet:function(d){
                    return d.server_app_file_path? '<span class="layui-blue" data-url="'+d.server_app_file_path+'" target="_blank">'+d.server_app_file_path+'</span>':"暂无";
                }},
            {field: 'app_user_email', title: '作者邮箱',minWidth:200, align:'center'},
            {field: 'app_status', title: '软件状态', align:'center',templet:function(d){
                    return d.app_status == "1" ? "<span style='color:#FF5722'>开启</span>" : "<span style='color:#c2c2c2'>关闭</span>";
                }},
            {field: 'add_time', title: '添加时间', align:'center',minWidth:110},
            {title: '操作', width:230,fixed:"right",align:"center", templet:function(){
                    return '<a class="layui-btn layui-btn-xs layui-btn-normal" lay-event="download">下载</a><a class="layui-btn layui-btn-xs layui-btn-warm" lay-event="cover">封面</a> <a class="layui-btn layui-btn-xs" lay-event="edit">编辑</a><a class="layui-btn layui-btn-xs layui-btn-danger" lay-event="del">删除</a>';
                }}
        ]]
    });


    //搜索
    $(".search_btn").on("click",function(){
        if($(".searchVal").val() != ''){
            table.reload("linkListTab",{
                page: {
                    curr: 1 //重新从第 1 页开始
                },
                where: {
                    key: $(".searchVal").val()  //搜索的关键字
                }
            })
        }else{
            layer.msg("请输入搜索的内容");
        }
    });


    $(".addLink_btn").click(function(){
        addLink();
    })
    //添加软件方法
    function addLink(){
        var index = layer.open({
            title : "添加软件",
            type : 2,
            area : ["330px","280px"],
            content : "page/app/app_add.html",
            success : function(layero, index){
                setTimeout(function(){
                    layui.layer.tips('点击此处返回', '.layui-layer-setwin .layui-layer-close', {
                        tips: 3
                    });
                },500);
            }
        })
    }

    //编辑
    function editLink(edit){
        var index = layer.open({
            title : "编辑软件",
            type : 2,
            area : ["500px","500px"],
            content : "page/app/app_edit.html",
            success : function(layero, index){
                var body = $($(".layui-layer-iframe",parent.document).find("iframe")[0].contentWindow.document.body);
                if(edit){
                    window.sessionStorage.setItem("temp_id",edit.id);
                    body.find(".server_app_logo_path").attr("src",edit.server_app_logo_path);
                    body.find(".app_name").val(edit.app_name);
                    body.find(".server_app_file_path").val(edit.server_app_file_path);
                    body.find(".app_user_email").val(edit.app_user_email);
                    body.find("#app_status").attr("checked",edit.app_status == 1?"checked":false);
                    form.render();
                }
                setTimeout(function(){
                    layui.layer.tips('点击此处返回', '.layui-layer-setwin .layui-layer-close', {
                        tips: 3
                    });
                },500)
            }
        })
    }


    //批量删除
    $(".delAll_btn").click(function(){
        var checkStatus = table.checkStatus('linkListTab'),
            data = checkStatus.data,
            id_arr = [];
        //console.log(data);
        if(data.length > 0) {
            for (var i in data) {
                id_arr.push(data[i].id);
            }
            //console.log(id_arr)
            layer.confirm('确定删除？', {icon: 3, title: '提示信息'}, function (index) {
                $.post(host_url+"index.php/Admin/AppManagement/deleteAppArr",{
                    id_arr : id_arr  //将需要删除的linkId作为参数传入
                },function(json){
                    // console.log(json)
                    if(json.code == 200){
                        layer.msg("删除成功",{icon:1});
                        setTimeout(function () {
                            tableIns.reload();
                            layer.close(index);
                        },800)
                    }else {
                        layer.msg("删除失败",{icon:5});
                        setTimeout(function () {
                            tableIns.reload();
                            layer.close(index);
                        },800)
                    }
                })
            })
        }else{
            layer.msg("请选择需要删除的数据");
        }
    });


    //编辑封面
    function coverEdit(edit){
        console.log(edit)
        var indexEdit = layui.layer.open({
            title : "编辑封面和内容",
            type : 2,
            content : "cover_edit.html",
            success : function(layero, indexEdit){
                var body = layui.layer.getChildFrame('body', indexEdit);
                if(edit){
                    body.find("#id").val(edit.id);//id主键
                    body.find("#app_name").val(edit.app_name);
                    for(let i=0; i<edit.server_app_carousel_arr_path.length; i++){
                        body.find(".carouselBox").append("<div><img src=' "+edit.server_app_carousel_arr_path[i]+" ' alt='' width='150'></div>");
                    }
                    body.find("#add_time").text(edit.add_time);
                    body.find("#temp_editor").html(edit.app_describe);
                    form.render();//执行表单刷新
                }
                setTimeout(function(){
                    layui.layer.tips('点击此处返回用户列表', '.layui-layer-setwin .layui-layer-close', {
                        tips: 3
                    });
                },500)
            }
        })
        layui.layer.full(indexEdit);
        window.sessionStorage.setItem("indexEdit",indexEdit);
        //改变窗口大小时，重置弹窗的宽高，防止超出可视区域（如F12调出debug的操作）
        $(window).on("resize",function(){
            layui.layer.full(window.sessionStorage.getItem("indexEdit"));
        })
    }


    //列表操作
    table.on('tool(linkList)', function(obj){
        var layEvent = obj.event,
            data = obj.data;
        // console.log(data.id)
        if(layEvent === 'edit'){ //编辑
            editLink(data);
        } else if(layEvent === 'del'){ //删除
            layer.confirm('确定删除？',{icon:3, title:'提示信息'},function(index){
                $.get(host_url+"index.php/Admin/AppManagement/deleteApp?id="+data.id,{
                    id : data.id  //将需要删除的linkId作为参数传入
                },function(json){
                    if(json.code == 200){
                        layer.msg("删除成功",{icon:1});
                        tableIns.reload();
                        layer.close(index);
                    }else {
                        layer.msg("删除失败",{icon:5});
                        tableIns.reload();
                        layer.close(index);
                    }
                })
            });
        }else if(layEvent === 'download'){
            var indexConfirm = layer.confirm('是否执行下载？', {
                btn: ['下载','取消'] //按钮
            }, function(){
                layer.close(indexConfirm);
                window.location.href = data.server_app_file_path;
            });
        }else if(layEvent == "cover"){
            coverEdit(data);
        }
    });


    //格式化时间
    function filterTime(val){
        if(val < 10){
            return "0" + val;
        }else{
            return val;
        }
    }
    //添加时间
    var time = new Date();
    var submitTime = time.getFullYear()+'-'+filterTime(time.getMonth()+1)+'-'+filterTime(time.getDate())+' '+filterTime(time.getHours())+':'+filterTime(time.getMinutes())+':'+filterTime(time.getSeconds());

    form.on("submit(addLink)",function(data){
        //弹出loading
        var index = top.layer.msg('数据提交中，请稍候',{icon: 16,time:false,shade:0.8});
        // 实际使用时的提交信息
        // $.post("上传路径",{
        //     linkLogoImg : $(".linkLogo").attr("src"),  //logo
        //     linkName : $(".linkName").val(),  //网站名称
        //     linkUrl : $(".linkUrl").val(),    //网址
        //     masterEmail : $('.masterEmail').val(),    //站长邮箱
        //     showAddress : data.filed.showAddress == "on" ? "checked" : "",    //展示位置
        //     newsTime : submitTime,    //发布时间
        // },function(res){
        //
        // })
        setTimeout(function(){
            top.layer.close(index);
            top.layer.msg("文章添加成功！");
            layer.closeAll("iframe");
            //刷新父页面
            $(".layui-tab-item.layui-show",parent.document).find("iframe")[0].contentWindow.location.reload();
        },500);
        return false;
    })

})