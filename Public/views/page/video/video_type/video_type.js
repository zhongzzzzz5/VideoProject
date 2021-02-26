layui.use(['form','layer','jquery','table'],function () {
    var form = layui.form,
        layer = parent.layer === undefined ? layui.layer : top.layer,
        $ = layui.jquery,
        table = layui.table;

    //全部分类
    var tableIns = table.render({
        elem: '#video_type',
        url : host_url+"index.php/Admin/VideoType/showVideoType",
        page:true,
        cellMinWidth : 95,
        height:"full-90",
        cols : [[
            {field:"type_id", title: 'type_id', width: 150, align:'center'},
            {field: 'type_name', title: '影视类别', align:'center'},
            {field: 'type_icon', title: '图标', align:'center',templet: function (d) {
                    return d.type_icon?"<img src='"+d.type_icon+"' width='50'/>" :"<span style='color: #ccc;font-size: 12px'>无图标</span>"
                }},
            {field: "type_order",title: "排序",align: "center"},
            {field: "type_status",title: "是否禁用",align: "center",templet:function (d) {
                    return d.type_status==0?"<input type='checkbox' id='type_status' data-type_id='"+d.type_id+"' lay-filter='type_status' lay-skin='switch' lay-text='正常|禁用' checked>":"<input type='checkbox' id='type_status' data-type_id='"+d.type_id+"' lay-filter='type_status' lay-skin='switch' lay-text='正常|禁用'>";
                }},
            {field: 'type_content', title: '描述', align:'center',templet: function (d) {
                    return d.type_content?d.type_content:"<span style='color: #ccc'>无描述</span>"
                }},
            {title: '操作', minWidth:100, templet:'#Bar',fixed:"right",align:"center",width: 250}
        ]]
    });


    //新增
    $(".add").click(function(){
        layer.open({
            type: 2,
            title: '新增分类',
            area: ['500px', '400px'],
            shadeClose: true,
            fixed: false, //不固定
            maxmin: true,
            content: 'page/video/video_type/add.html'
        });
    });

    //监听开关
    var type_status_switch = 0;//初始化
    form.on('switch(type_status)', function(data){
        // console.log(data.elem.getAttribute("data-type_id"));
        // console.log(data.elem.checked); //开关是否开启，true或者false
        data.elem.checked == true?type_status_switch=0:type_status_switch=1;
        // console.log(type_status_switch);
        $.post(host_url+"index.php/Admin/VideoType/tableEventEdit",
            {
                "type_id" : data.elem.getAttribute("data-type_id"),
                "type_status" : type_status_switch
            },
            function (json) {
                if(json.code == 200){
                    layer.msg("修改成功",{icon:1,time:500});
                    tableIns.reload();
                }else {
                    layer.msg("修改失败",{icon:5,time:700});
                    setTimeout(()=>{
                        tableIns.reload();
                    },500)
                }
            }
        );
    });

    //监听表格事件
    table.on('tool(video_type)',function (obj) {
        var layEvent = obj.event,
            data = obj.data;
        if(layEvent === 'delete'){
            layer.confirm('确定删除该分类？', {
                btn: ['是','否'] //按钮
            }, function(){
                let indexDel = top.layer.msg('正在操作，请稍后',{icon: 16,time:false,shade:0.8});
                $.get(host_url+"index.php/Admin/VideoType/deleteVideoType?type_id="+data.type_id,function (json) {
                    if(json.code == 200){
                        layer.close(indexDel);
                        layer.msg("删除成功",{icon:1});
                        tableIns.reload();
                    }else {
                        layer.close(indexDel);
                        let res_index = layer.alert('删除失败', {
                            skin: 'layui-layer-lan' //样式类名
                            ,closeBtn: 0
                        },function () {
                            layer.close(res_index);
                            tableIns.reload();
                        });
                    }
                });
            });
        }else if(layEvent === 'edit'){
            layer.open({
                type: 2,
                title: '编辑',
                shadeClose: true,
                shade: 0.8,
                area: ['500px', '500px'],
                content: 'page/video/video_type/edit.html' ,//iframe的url,
                success: function (layero, index) {
                    // console.log(data)
                    var body = $($(".layui-layer-iframe",parent.document).find("iframe")[0].contentWindow.document.body);
                    body.find("#type_id").val(data.type_id);
                    body.find("#type_name").val(data.type_name);
                    data.type_icon?body.find("#type_icon").attr("src",data.type_icon):"";
                    body.find("#type_order").attr("title",data.type_order);
                    body.find("#type_status").attr("checked",data.type_status==0?"checked":false);
                    body.find("#type_content").val(data.type_content);
                    form.render();
                }
            });
        }else if (layEvent === 'up'){
            $.get(host_url+"index.php/Admin/VideoType/up?type_id="+data.type_id,
                function (json) {
                    if(json.code == 200){
                        tableIns.reload();
                    }else {
                        layer.msg("出现错误",{icon:5});
                    }
                }
            );
        }
    })


});