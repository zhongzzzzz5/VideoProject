layui.use(['form','upload','jquery','layer','table'],function () {
    var form = layui.form,
        upload = layui.upload,
        $ = layui.jquery,
        table = layui.table ,
        layer = parent.layer === undefined ? layui.layer : top.layer;

    //给按钮绑定功能,点击该按钮显示对应的frame页面
     $(".myBt").each(function (i) {
         $(this).click(function () {
             $("#frame-main").attr("src",$(this).attr("data-htmlsrc"));
         });
     });
});