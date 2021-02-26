//依赖jq库
if(!window.sessionStorage.getItem("UID")){
    alert("请先登录");
    window.stop ? window.stop() : document.execCommand("Stop");//阻止页面加载
    //原生js发送ajax请求，验证是否连通服务器
    var ajax = new XMLHttpRequest();
    ajax.open("get","http://www.5wpz.top/VideoProject/index.php/Admin/Api/api");
    ajax.send();
    ajax.onreadystatechange = function () {
        if (ajax.status==200) {
            console.log(ajax.responseText);//输出接口给的相应的内容
            window.parent.location.href = "http://www.5wpz.top/VideoProject/Public/views/page/login/login.html";
        }else {
            alert("无法连接服务器");
            window.parent.location.href = "http://www.5wpz.top/404.html";
        }
    }
}else {
    var timer = window.setInterval(function () {
        //原生js发送ajax请求
        let uid = window.sessionStorage.getItem("UID");
        let login_time = window.sessionStorage.getItem("LOGIN_TIME");
        //console.log(uid+":"+login_time)

        $.get(host_url+"index.php/Admin/Public/userTatus?UID="+uid+"&time="+login_time,function (json) {
            if(json.code == 200){
                if(json.data.user_status == 1){
                    document.querySelector("body").style.opacity = 0.1;
                    window.clearInterval(timer);//清除定时器
                    alert("您的账号状态异常，请先退出系统");
                    setTimeout(function () {
                        window.parent.location.href = host_url+"Public/views/page/login/login.html";
                    },1000)
                }
                if(json.data.user_status == -1){
                    document.querySelector("body").style.opacity = 0.1;
                    window.clearInterval(timer);//清除定时器
                    alert("您的账号被强制登录，您已被迫下线");
                    setTimeout(function () {
                        window.parent.location.href = host_url+"Public/views/page/login/login.html";
                    },1000)
                }

            }else {
                alert("无法连接服务器");
                window.parent.location.href = "http://www.5wpz.top/404.html";
            }
        })
    },10000)
}
