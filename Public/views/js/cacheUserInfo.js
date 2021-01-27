/**
 *  暂时不使用该js
 */

layui.config({
    base : "../../js/"
}).use(['form','jquery',"address"],function() {
    var form = layui.form,
        $ = layui.jquery,
        address = layui.address;

    //判断是否设置过头像，如果设置过则修改顶部、左侧和个人资料中的头像，否则使用默认头像
    if(window.sessionStorage.getItem('userFace')){
        $("#userFace").attr("src",window.sessionStorage.getItem('userFace'));//框架的
    }else{
        $("#userFace").attr("src","../../images/face.jpg");//框架的
    }

})