layui.use(['layer','jquery'], function(){
    var layer = parent.layer === undefined ? layui.layer : top.layer,
        $ = layui.jquery;

    $.get(host_url+"index.php/Admin/Main/app",function (json) {
        console.log(json)
        if(json.code == 200){
            var data = json.data;
            for(let i=0; i<data.length; i++){
                if(data[i].app_status == 1){
                    $(".all").append(
                        "<li><div class='img-box'><img src='"+data[i].server_app_logo_path+"' alt=''></div>" +
                        "<div class='app-name'><span>"+data[i].app_name+"</span></div>" +
                        "</li>"
                    );
                }
            }
        }else {
            $(".all").append("<div style='width: 100%;height: 30px;text-align: center;font-size: 30px;color: #ccc'>没有数据哟</div>");
        }
    });

});