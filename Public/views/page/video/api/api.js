layui.use(['form','layer','jquery','table'],function () {
    var form = layui.form, $ = layui.jquery, layer = parent.layer === undefined ? layui.layer : top.layer,table = layui.table //表格;

    //获取默认接口状态(总开关)
    var id = 0;//主键
    var status = 0;//总开关
    $.get(host_url+"index.php/Admin/VideoApi/apiSwitch",function (json) {
        if(json.code == 200){
            id = json.data.id;
            $("#switch").attr("checked",json.data.switch == 0?"checked":false);
            json.data.switch == 0?$("#default_api").show():$("#default_api").hide();
            status = json.data.switch;
            $("#default_api_name").val(json.data.default_api_name+"  "+json.data.default_api_url+"   "+(json.data.default_api_status==0?"正常":"禁用"));
            form.render();//执行渲染表单
        }else {
            layer.msg("获取默认接口出现异常(可能没有默认接口)");
        }
    });

    //监听开启关闭框
    form.on('switch(switchTest)',function () {
        if(this.checked){
            status = this.checked? 0 : 1//1，关  0，开
            layer.msg('状态：'+ (status? "关闭" : '开启'), {offset: '6px'});
            if(status == 0){
                $.post(host_url+"index.php/Admin/VideoApi/switchChange",
                    {
                        "id":id,
                        "switch":status
                    },
                    function (json) {
                        if(json.code == 200){
                            layer.msg("开启成功",{icon:1});
                        }else {
                            layer.msg("更改失败，请售后重试",{icon:5});
                            //去执行刷新父页面
                            parent.location.reload();
                        }
                    }
                );
                $("#default_api").slideDown();
            }
            if(status == 1){
                $("#default_api").slideUp();
            }
        }else{
            layer.msg('状态： 关闭', {offset: '6px'});
            status = 1;
            $.post(host_url+"index.php/Admin/VideoApi/switchChange",
                {
                    "id":id,
                    "switch":status
                },
                function (json) {
                    if(json.code == 200){
                        layer.msg("关闭成功",{icon:1});
                    }else {
                        layer.msg("更改失败，请售后重试",{icon:5});
                    }
                }
            );
            $("#default_api").slideUp();
        }
        // console.log(temp_status)
    });
    
    function switchChange(id,status) {

    }
    
    //列表
    var tableIns = table.render({
        elem: '#newsList',
        url : host_url+'index.php/Admin/VideoApi/showApi',
        cellMinWidth : 95,
        page : true,
        height : "full-125",
        id : "newsListTable",
        cols : [[
            {type: "checkbox", fixed:"left", width:50},
            {field: 'id', title: 'ID', width:60, align:"center"},
            {field: 'api_name', title: '接口名'},
            {field: 'api_url', title: '接口地址',width:350, align:'center',templet:function (d) {
                    return "<a style='text-decoration: none;color: #01AAED' href='"+d.api_url+"' target='_blank'>"+d.api_url+"</a>";
                }},
            {field: 'api_describe', title: '描述',  align:'center',templet:function (d) {
                    return d.api_describe?d.api_describe:"<span style='color: #cccccc'>无描述</span>";
                }},
            {field: 'api_status', title: '状态', align:'center', templet: function (d) {
                    return d.api_status == 0?"<span style='color: #5FB878'>正常</span>":"<span style='color: #FF5722'>禁用</span>";
                }},
            {field: 'category_name', title: '分类', align:'center', templet: function (d) {
                    return d.category_name;
                }},
            {field: 'add_time', title: '添加时间', align:'center', minWidth:110, templet:function(d){
                    return d.add_time.substring(0,16);
                }},
            {title: '操作', width:200, templet:'#newsListBar',fixed:"right",align:"center"}
        ]]
    });

    //搜索
    $(".search_btn").on("click",function(){
        if($(".searchVal").val() != ''){
            table.reload("newsListTable",{
                page: {
                    curr: 1 //重新从第 1 页开始
                },
                where: {
                    key: $(".searchVal").val()  //搜索的关键字
                }
            })
        }else{
            layer.msg("请输入搜索的内容",{icon:3});
        }
    });

    //添加
    function addNews(edit){
        let index = layui.layer.open({
            title : "添加接口",
            type : 2,
            content : "add_api.html",
            success : function(){
                setTimeout(function(){
                    layui.layer.tips('点击此处返回列表', '.layui-layer-setwin .layui-layer-close', {
                        tips: 3
                    });
                },500)
            }
        })
        layui.layer.full(index);
        //改变窗口大小时，重置弹窗的宽高，防止超出可视区域（如F12调出debug的操作）
        $(window).on("resize",function(){
            layui.layer.full(index);
        })
    }
    $(".addNews_btn").click(function(){
        addNews();
    })

    function edit(edit){
        let index = layui.layer.open({
            title : "编辑接口",
            type : 2,
            content : "edit_api.html",
            success : function(layero, index){
                var body = layui.layer.getChildFrame('body', index);
                if(edit){
                    body.find("#id").val(edit.id);
                    body.find("#api_name").val(edit.api_name);
                    body.find("#api_url").val(edit.api_url);
                    window.sessionStorage.setItem("category_id",edit.category_id);
                    body.find("#api_status").attr("checked",edit.api_status == 0?"checked":false);
                    body.find("#add_time").html(edit.add_time);
                    window.sessionStorage.setItem("temp_text",edit.api_describe);
                    form.render();
                }
                setTimeout(function(){
                    layui.layer.tips('点击此处返回列表', '.layui-layer-setwin .layui-layer-close', {
                        tips: 3
                    });
                },500)
            }
        })
        layui.layer.full(index);
        //改变窗口大小时，重置弹窗的宽高，防止超出可视区域（如F12调出debug的操作）
        $(window).on("resize",function(){
            layui.layer.full(index);
        })
    }

    //批量删除
    $(".delAll_btn").click(function(){
        var checkStatus = table.checkStatus('newsListTable'),
            data = checkStatus.data,
            ids = [];
        if(data.length > 0) {
            for (var i in data) {
                ids.push(data[i].id);
            }
            console.log(ids)
            layer.confirm('确定删除选中的接口？', {icon: 3, title: '提示信息'}, function (index) {
                $.post(host_url+"index.php/Admin/VideoApi/deleteApiArr",{
                   "id_arr" : ids  //将需要删除的newsId作为参数传入
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
            })
        }else{
            layer.msg("请选择需要删除的接口",{icon:3});
        }
    })

    //列表操作
    table.on('tool(newsList)', function(obj){
        var layEvent = obj.event,
            data = obj.data;

        if(layEvent === 'edit'){ //编辑
            edit(data);
        } else if(layEvent === 'del'){ //删除
            layer.confirm('确定删除此接口？',{icon:3, title:'提示信息'},function(index){
                $.get(host_url+"index.php/Admin/VideoApi/deleteApi",{
                    "id" : data.id  //将需要删除的newsId作为参数传入
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
        }else if (layEvent === 'default'){
            console.log(data);
            var indexConfirm = layer.confirm('设为默认接口吗？', {
                btn: ['确定','取消'] //按钮
            }, function(){
                //弹出loading
                var index = top.layer.msg('设置中，请稍候',{icon: 16,time:false,shade:0.8});

                $.post(host_url+"index.php/Admin/VideoApi/defaultApi",
                    {
                        "id":id,//总开关的主键
                        "default_api_name":data.api_name,
                        "default_api_url":data.api_url,
                        "default_api_describe":data.api_describe,
                        "default_api_status":data.api_status
                    },
                    function (json) {
                        if(json.code == 200){
                            layer.msg("设置成功",{icon:6});
                            window.setTimeout(function () {
                                $.get(host_url+"index.php/Admin/VideoApi/apiSwitch",function (json) {
                                    if(json.code == 200){
                                        top.layer.close(index);//关闭loading

                                        id = json.data.id;
                                        $("#switch").attr("checked",json.data.switch == 0?"checked":false);
                                        json.data.switch == 0?$("#default_api").show():$("#default_api").hide();
                                        status = json.data.switch;
                                        $("#default_api_name").val(json.data.default_api_name+"  "+json.data.default_api_url+"   "+(json.data.default_api_status==0?"正常":"禁用"));
                                        form.render();//执行渲染表单
                                    }else {
                                        top.layer.close(index);//关闭loading
                                        layer.msg("获取默认接口出现异常(可能没有默认接口)");
                                    }
                                });
                            },800)
                        }else {
                            layer.msg("设置失败",{icon:5});
                        }
                    }
                );
                layer.close(indexConfirm);
            });
        }
    });
})