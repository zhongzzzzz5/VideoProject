layui.use(['table','layer','jquery'],function(){
    var table = layui.table , layer = layui.layer, $ = layui.jquery;

    //系统日志
    table.render({
        elem: '#logs',
        url : host_url+'index.php/Admin/System/loginLog',
        cellMinWidth : 95,
        page : true,
        height : "full-20",
        id : "systemLog",
        cols : [[
            {field: 'No', title: '序号', width:60, align:"center"},
            {field: 'uid', title: '用户id', width:80, align: "center"},
            {field: 'uname', title: '用户名', align:'center'},
            {field: 'uface', title: '头像', align:'center',templet:function (d) {
                    return d.uface? "<span class='look-head-img' lay-event='lookImg' data-head-url='"+d.uface+"' style='color: #01AAED; cursor: pointer'>查看</span>" :"<span style='color: #cccccc'>无</span>";
                }},
            {field: 'ip', title: '操作IP',  align:'center',minWidth:130},
            {field: 'ip_location', title: '物理地址',  align:'center',minWidth:130},
            {field: 'login_time', title: '登录时间', align:'center'},
            {field: 'logout_time', title: '注销时间', align:'center',templet:function (d) {
                    return d.logout_time?d.logout_time:"<span style='color: #cccccc'>未注销</span>"
                }},
        ]]
    });

    table.on("tool(logs)",function (obj) {
        //console.log(obj.data);
        layer.open({
            type: 1,
            skin: 'layui-layer-rim', //加上边框
            area: ['400px', '400px'], //宽高
            content: "<img width='330' style='padding: 20px' src='"+obj.data.uface+"'/>"
        });
    });

})
