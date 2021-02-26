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
}


//随机数，参数：最小值 最大值
function getRandom(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

//获得操作系统版本和浏览器版本
function getOSAndBrowser(){
    var os = navigator.platform;
    var userAgent = navigator.userAgent;
    console.log(userAgent);
    var info = "";
    var tempArray = "";
    if(os.indexOf("Win") > -1){
        if(userAgent.indexOf("Windows NT 5.0") > -1){
            info += "Windows 2000";
        }else if(userAgent.indexOf("Windows NT 5.1") > -1){
            info += "Windows XP";
        }else if(userAgent.indexOf("Windows NT 5.2") > -1){
            info += "Windows 2003";
        }else if(userAgent.indexOf("Windows NT 6.0") > -1){
            info += "Windows Vista";
        }else if(userAgent.indexOf("Windows NT 6.1") > -1 || userAgent.indexOf("Windows 7") > -1){
            info += "Windows 7";
        }else if(userAgent.indexOf("Windows NT 6.2") > -1 || userAgent.indexOf("Windows NT 6.3") > -1 || userAgent.indexOf("Windows 8") > -1){
            info += "Windows 8";
        }else if(userAgent.indexOf("Windows NT 6.4") > -1 ||userAgent.indexOf("Windows NT 10") > -1){
            info += "Windows 10";
        }else{
            info += "Other";
        }
    }else if(os.indexOf("Mac") > -1){
        info += "Mac";
    }else if(os.indexOf("X11") > -1){
        info += "Unix";
    }else if(os.indexOf("Linux") > -1){
        info += "Linux";
    }else{
        info += "Other";
    }
    info += "/";
    if(/[Ff]irefox(\/\d+\.\d+)/.test(userAgent)){
        tempArray = /([Ff]irefox)\/(\d+\.\d+)/.exec(userAgent);
        info += tempArray[1] + tempArray[2];
    }else if(/[Tt]rident(\/\d+\.\d+)/.test(userAgent)){
        tempArray = /([Tt]rident)\/(\d+\.\d+)/.exec(userAgent);
        if(tempArray[2] === "7.0"){
            tempArray[2] = "11";
        }else if(tempArray[2] === "6.0"){
            tempArray[2] = "10";
        }else if(tempArray[2] === "5.0"){
            tempArray[2] = "9";
        }else if(tempArray[2] === "4.0"){
            tempArray[2] = "8";
        }
        tempArray[1] = "IE";
        info += tempArray[1] + tempArray[2];
    }else if(/[Cc]hrome\/\d+/.test(userAgent)){
        tempArray = /([Cc]hrome)\/(\d+)/.exec(userAgent);
        info += tempArray[1] + tempArray[2];
    }else if(/[Vv]ersion\/\d+\.\d+\.\d+(\.\d)* *[Ss]afari/.test(userAgent)){
        tempArray = /[Vv]ersion\/(\d+\.\d+\.\d+)(\.\d)* *([Ss]afari)/.exec(userAgent);
        info += tempArray[3] + tempArray[1];
    }else if(/[Oo]pera.+[Vv]ersion\/\d+\.\d+/.test(userAgent)){
        tempArray = /([Oo]pera).+[Vv]ersion\/(\d+)\.\d+/.exec(userAgent);
        info += tempArray[1] + tempArray[2];
    }else{
        info += "unknown";
    }
    return info;
}

//获得操作系统版本
function getOS() {
    var sUserAgent = navigator.userAgent;
    var isWin = (navigator.platform == "Win32") || (navigator.platform == "Windows");
    var isMac = (navigator.platform == "Mac68K") || (navigator.platform == "MacPPC") || (navigator.platform == "Macintosh") || (navigator.platform == "MacIntel");
    if (isMac) return "Mac";
    var isUnix = (navigator.platform == "X11") && !isWin && !isMac;
    if (isUnix) return "Unix";
    var isLinux = (String(navigator.platform).indexOf("Linux") > -1);
    if (isLinux) return "Linux";
    if (isWin) {
        var isWin2K = sUserAgent.indexOf("Windows NT 5.0") > -1 || sUserAgent.indexOf("Windows 2000") > -1;
        if (isWin2K) return "Windows 2000";
        var isWinXP = sUserAgent.indexOf("Windows NT 5.1") > -1 || sUserAgent.indexOf("Windows XP") > -1;
        if (isWinXP) return "Windows XP";
        var isWin2003 = sUserAgent.indexOf("Windows NT 5.2") > -1 || sUserAgent.indexOf("Windows 2003") > -1;
        if (isWin2003) return "Windows 2003";
        var isWinVista= sUserAgent.indexOf("Windows NT 6.0") > -1 || sUserAgent.indexOf("Windows Vista") > -1;
        if (isWinVista) return "Windows Vista";
        var isWin7 = sUserAgent.indexOf("Windows NT 6.1") > -1 || sUserAgent.indexOf("Windows 7") > -1;
        if (isWin7) return "Windows 7";
        var isWin10 = sUserAgent.indexOf("Windows NT 10") > -1 || sUserAgent.indexOf("Windows 10") > -1;
        if (isWin10) return "Windows 10";
    }
    return "other";
}

//获得浏览器版本
function getBrowse () {
    var browser = {};
    var userAgent = navigator.userAgent.toLowerCase();
    var s;
    (s = userAgent.match(/msie ([\d.]+)/)) ? browser.ie = s[1] : (s = userAgent.match(/firefox\/([\d.]+)/)) ? browser.firefox = s[1] : (s = userAgent.match(/chrome\/([\d.]+)/)) ? browser.chrome = s[1] : (s = userAgent.match(/opera.([\d.]+)/)) ? browser.opera = s[1] : (s = userAgent.match(/version\/([\d.]+).*safari/)) ? browser.safari = s[1] : 0;
    var version = "";
    if (browser.ie) {
        version = 'IE ' + browser.ie;
    }
    else {
        if (browser.firefox) {
            version = 'firefox ' + browser.firefox;
        }
        else {
            if (browser.chrome) {
                version = 'chrome ' + browser.chrome;
            }
            else {
                if (browser.opera) {
                    version = 'opera ' + browser.opera;
                }
                else {
                    if (browser.safari) {
                        version = 'safari ' + browser.safari;
                    }
                    else {
                        version = '未知浏览器';
                    }
                }
            }
        }
    }
    return version;
}


//判断两个对象的内容是否相同
function isObjectValueEqual(a, b) {
    // 判断两个对象是否指向同一内存，指向同一内存返回true
    if (a === b) return true
    // 获取两个对象键值数组
    let aProps = Object.getOwnPropertyNames(a)
    let bProps = Object.getOwnPropertyNames(b)
    // 判断两个对象键值数组长度是否一致，不一致返回false
    if (aProps.length !== bProps.length) return false
    // 遍历对象的键值
    for (let prop in a) {
        // 判断a的键值，在b中是否存在，不存在，返回false
        if (b.hasOwnProperty(prop)) {
            // 判断a的键值是否为对象，是则递归，不是对象直接判断键值是否相等，不相等返回false
            if (typeof a[prop] === 'object') {
                if (!isObjectValueEqual(a[prop], b[prop])) return false
            } else if (a[prop] !== b[prop]) {
                return false
            }
        } else {
            return false
        }
    }
    return true
}
