layui.use(['table','layer','jquery','laydate'],function(){
    var table = layui.table , layer = layui.layer, $ = layui.jquery,laydate = layui.laydate;

    //日期选择
    laydate.render({
        elem: '#search_date' //指定元素
    });

    //系统日志
    var loginTabIn = table.render({
        elem: '#logs',
        url : host_url+'index.php/Admin/System/loginLog',
        cellMinWidth : 95,
        page : true,
        height : "full-65",
        id : "loginLog",
        cols : [[
            {field: 'No', title: '序号', width:60, align:"center"},
            {field: 'uid', title: '用户id', width:80, align: "center"},
            {field: 'uname', title: '用户名', align:'center'},
            {field: 'uface', title: '头像', align:'center',width:70,templet:function (d) {
                    return d.uface? "<span class='look-head-img' lay-event='lookImg' data-head-url='"+d.uface+"' style='color: #01AAED; cursor: pointer'>查看</span>" :"<span style='color: #cccccc'>无</span>";
                }},
            {field: 'ip', title: '操作IP',  align:'center',minWidth:130},
            {field: 'ip_location', title: '物理地址',  align:'center',minWidth:150},
            {field: 'os', title: '操作系统',  align:'center',minWidth:80},
            {field: 'browser', title: '浏览器',  align:'center',minWidth:80},
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
            title:false,
            shadeClose:true,
            area: ['330px', '330px'], //宽高
            content: "<img width='330' style='box-sizing: border-box; padding: 20px' src='"+obj.data.uface+"'/>"
        });
    });


    //搜索用户名
    $("#search").on("click",function(){
        if($("#searchVal").val() != ''){
            let index = layui.layer.load(2, {shade: false}); //0代表加载的风格，支持0-2
            setTimeout(()=>{
                layui.layer.close(index);
                table.reload("loginLog",{
                    page: {
                        curr: 1 //重新从第 1 页开始
                    },
                    where: {
                        key_username: $("#searchVal").val()  //搜索的关键字
                    }
                })
                $("#searchVal").val(null);
            },500)
        }else{
            layer.msg("请输入搜索的内容",{icon:3,time:500});
        }
    });
    //搜索地址
    $("#search1").on("click",function(){
        if($("#search_date").val() != ''){
            let index = layui.layer.load(2, {shade: false}); //0代表加载的风格，支持0-2
            setTimeout(()=>{
                layui.layer.close(index);
                table.reload("loginLog",{
                    page: {
                        curr: 1 //重新从第 1 页开始
                    },
                    where: {
                        key_date: $("#search_date").val()  //搜索的关键字
                    }
                })
                $("#search_date").val(null);
            },500)
        }else{
            layer.msg("请输入搜索的内容",{icon:3,time:500});
        }
    });

    //刷新
    $("#flush").click(function () {
        let index = layui.layer.load(2, {shade: false}); //0代表加载的风格，支持0-2
        setTimeout(()=>{
            loginTabIn.reload();
            layui.layer.close(index);
        },500)
    });

})
