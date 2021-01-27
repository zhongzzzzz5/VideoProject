layui.use(['form','layer','laydate','table','laytpl'],function(){
    var form = layui.form,
        layer = parent.layer === undefined ? layui.layer : top.layer,
        $ = layui.jquery,
        laydate = layui.laydate,
        laytpl = layui.laytpl,
        table = layui.table;

    //全部分类
    var tableIns = table.render({
        elem: '#api_category',
        url : host_url+"index.php/Admin/VideoApiCategory/apiCategory",
        page:true,
        cellMinWidth : 95,
        height:"full-90",
        cols : [[
            {field:"category_id", title: 'category_id', width: 150, align:'center'},
            {field: 'category_name', title: '类别', align:'center'},
            {field: 'category_describe', title: '描述', align:'center',templet: function (d) {
                    return d.category_describe?d.category_describe:"<span style='color: #ccc'>无描述</span>"
                }},
            {title: '操作', minWidth:100, templet:'#api_categoryBar',fixed:"right",align:"center",width: 150}
        ]]
    });

    //新增
    $(".add").click(function(){
        layer.open({
            type: 2,
            title: '新增分类',
            shadeClose: true,
            shade: 0.8,
            area: ['380px', '300px'],
            content: 'page/video/api_category/add.html' ,//iframe的url,
        });
    });

    table.on('tool(api_category)',function (obj) {
        var layEvent = obj.event,
            data = obj.data;
        if(layEvent === 'delete'){
                layer.confirm('确定删除该分类？', {
                    btn: ['是','否'] //按钮
                }, function(){
                    let indexDel = top.layer.msg('正在操作，请稍后',{icon: 16,time:false,shade:0.8});
                    $.get(host_url+"index.php/Admin/VideoApiCategory/deleteApiCategory?id="+data.category_id,function (json) {
                        if(json.code == 200){
                            layer.close(indexDel);
                            layer.msg("删除成功",{icon:1});
                            tableIns.reload();
                        }else {
                            layer.close(indexDel);
                            let res_index = layer.alert('删除失败，该分类被使用', {
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
                area: ['380px', '300px'],
                content: 'page/video/api_category/edit.html' ,//iframe的url,
                success: function (layero, index) {
                    var body = $($(".layui-layer-iframe",parent.document).find("iframe")[0].contentWindow.document.body);
                    body.find("#category_id").val(data.category_id);
                    body.find("#category_name").val(data.category_name);
                    body.find("#category_describe").val(data.category_describe);
                    form.render();
                }
            });
        }else if(layEvent === 'browse'){
            layer.open({
                type: 2,
                title: '浏览',
                area: ['950px', '80%'],
                content: 'page/video/api_category/browse.html' ,//iframe的url,
                success: function (layero, index) {
                    var body = $($(".layui-layer-iframe",parent.document).find("iframe")[0].contentWindow.document.body);
                    body.find("#category_name").html(data.category_name);
                    window.sessionStorage.setItem("temp_category_id",data.category_id);
                }
            });
        }
    })

})