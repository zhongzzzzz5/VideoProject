/**
 *  验证用户是否已经登录和判定权限
 */
if(window.sessionStorage.getItem("UID")){ //是否已经登录
    //验证权限
    if(window.sessionStorage.getItem("ROLE") != -1 ){
        window.stop ? window.stop() : document.execCommand("Stop");//阻止页面加载
        console.log("role no");
        window.location.href = "../403.html";
    }
}else {  //若未登录
    alert("请先登录");
    window.stop ? window.stop() : document.execCommand("Stop");//阻止页面加载
    //原生js发送ajax请求，验证是否连通服务器
    var ajax = new XMLHttpRequest();
    ajax.open("get","http://www.5wpz.top/VideoProject/index.php/Api/Api/api");
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
}

