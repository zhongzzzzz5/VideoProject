layui.use(['table','layer','jquery'],function(){
    var table = layui.table,
    layer = parent.layer === undefined ? layui.layer : top.layer,
    $ = layui.jquery;

    //在线用户
    var tableIns = table.render({
        elem: '#userOnline',
        url : host_url+"index.php/Admin/UserInfo/userOnline",
        cellMinWidth : 95,
        page : true,
        height : "full-20",
        id : "systemLog",
        done: function(res, curr, count){
            //如果是异步请求数据方式，res即为你接口返回的信息。
            //如果是直接赋值的方式，res即为：{data: [], count: 99} data为当前页数据、count为数据总长度
            //console.log(res);
            //得到当前页码
            //console.log(curr+"curr");
            //得到数据总量
            //console.log(count+"count");
            $("#onlineNum").html(count);
        },
        cols : [[
            {field: 'No', title: '序号', width:60, align:"center"},
            {field: 'uid', title: '用户id', width:100, align:"center"},
            {field: 'uname', title: '用户名', width:200, align:"center",templet:function (d) {
                d.uname = d.uname == window.sessionStorage.getItem("UNAME")?d.uname+"<span style='color: #c2c2c2'> [自己]</span>":d.uname;
                return d.uname;
                }},
            {field: 'uface', title: '头像', align:'center',templet:function (d) {
                    return d.uface? "<span class='look-head-img' lay-event='lookImg' style='color: #01AAED; cursor: pointer'>查看</span>" :"<span style='color: #cccccc'>无</span>";
                }},
            {field: 'login_time', title: '登录时间', align:'center'},
            {field: 'login_status', title: '登录状态',  align:'center',templet:function (d) {
                    return d.login_status == 1?"<span style='color: #FF5722'>在线</span>":"不在线";
                }},
            {title: '操作', minWidth:100, templet:'#Bar',fixed:"right",align:"center",width: 150}
        ]]
    });

    //定时刷新表格
    setInterval(function () {
        tableIns.reload();
    },60000);

    //监听事件
    table.on('tool(user_online)',function (obj) {
        var layEvent = obj.event,
            data = obj.data;
        if(layEvent === 'downline'){
            if(data.uid == window.sessionStorage.getItem("UID")){
                layer.alert('不允许操作自己下线', {
                    skin: 'layui-layer-molv' //样式类名
                    ,closeBtn: 0
                });
            }else {
                layer.confirm('确定让该用户下线？', {
                    btn: ['确定','取消'] //按钮
                }, function(){
                    $.get(host_url+"index.php/Admin/Public/logout?id="+data.uid,function (json) {
                        if(json.code == 200){
                            layer.msg("下线成功！",{icon:1});
                            tableIns.reload();
                        }else {
                            layer.msg("出现未知错误",{icon:5});
                            table.render();
                        }
                    });
                });
            }
        }
        if(layEvent === 'lookImg'){
            layer.open({
                type: 1,
                skin: 'layui-layer-rim', //加上边框
                area: ['270px', '270px'], //宽高
                content: "<img width='200' style='padding: 25px' src='"+data.uface+"'/>"
            });
        }
    });

    $("#refresh").bind('click',function () {
        let index = layer.load(0, {shade: false});
        tableIns.reload();
        setTimeout(function () {
            layer.close(index);
        },800)
    });
})
