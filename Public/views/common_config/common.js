/**
 *  后端项目地址
 */
const host_url = "/VideoProject/";

/**
 *  服务器地址
 */
const SERVER = "http://www.5wpz.top/";

/**
 * 表单验证函数
 */
//各类型验证方法 --- (含正则表达式)
function Pattern(str,type){
    if(type == 'username'){
        //验证用户名
        var uPattern = /^[a-zA-Z0-9_-]{4,16}$/;
        var ubool = uPattern.test(str);
        return ubool;
    }
    if(type == 'password'){
        //验证密码
        var pPattern = /^.*(?=.{6,})(?=.*\d)(?=.*[A-Z])(?=.*[a-z])(?=.*[!@#$%^&*? ]).*$/;
        var pbool = pPattern.test(str);
        return pbool;
    }
    if(type == 'email' ){
        //验证邮箱
        var ePattern = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
        var ebool = ePattern.test(str);
        return ebool;
    }
    if(type == 'name'){
        //验证名字
        var namePattern = /^[\u4E00-\u9FA5\uf900-\ufa2d·s]{2,20}$/;
        var nameBool = namePattern.test(str);
        return nameBool;
    }
    if(type == 'telephone'){
        //验证电话号码
        var telPattern = /^1[3|4|5|7|8][0-9]{9}$/;
        var telBool = telPattern.test(str);
        return telBool;
    }
    if(type == 'date'){
        //验证日期
        var datePattern = /^\d{4}(\-)\d{1,2}\1\d{1,2}$/;
        var dateBool = datePattern.test(str);
        return dateBool;
    }

    //随机数，参数：最小值 最大值
    function getRandom(min, max) {
        return Math.floor(Math.random() * (max - min + 1)) + min;
    }
}