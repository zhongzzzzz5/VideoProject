layui.use(['form','jquery','table','layer'],function () {
    var form = layui.form,
        layer = parent.layer === undefined ? layui.layer : top.layer,
        $ = layui.jquery,
        table = layui.table;

    var category_id = window.sessionStorage.getItem("temp_category_id");
    window.sessionStorage.removeItem("temp_category_id");
    var tableIns = table.render({
        elem: '#browse',
        url : host_url+"index.php/Admin/VideoApiCategory/browse?category_id="+category_id,
        page:true,
        height : "full-90",
        cellMinWidth : 95,
        cols : [[
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
        ]]
    });
});