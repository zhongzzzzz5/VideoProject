layui.use(['form','upload','jquery','layer','table'],function () {
    var form = layui.form,
        upload = layui.upload,
        $ = layui.jquery,
        table = layui.table ,
        layer = parent.layer === undefined ? layui.layer : top.layer;

    //给按钮绑定功能,点击该按钮显示对应的frame页面
     $(".myBt").each(function (i) {
         $(this).click(function () {
             var that = $(this);
             $("#frame-main").fadeOut(100,function () {
                 $(this).attr("src",that.attr("data-htmlsrc"));
                 $(this).fadeIn();
             });
         });
     });

     //给按钮添加点击后修改背景色事件
    let write_bt_bgc = $("#write-bt").css("background-color");//写信 按钮的背景色
    let old_bgc = $("#outbox-bt").css("background-color"); //其他颜色按钮的背景色
    let new_bgc = "orange";//新的背景色
    $(".myBt").each(function (i) {
        $(this).click(function () {
            $(".myBt").each(function (i) {
                if(i==0){
                    $(this).css({"background-color":write_bt_bgc});
                }else {
                    $(this).css({"background":old_bgc});
                }
            });
            $(this).css({"background-color":new_bgc});
        });
    });
});