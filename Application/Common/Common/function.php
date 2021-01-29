<?php

function gbk2utf8(){
    echo 'gbk2utf8';
}

/**
 *字符串截取函数
 *开启mbstring扩展
 */
function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true){
    if(mb_strlen($str,$charset)>$length)
    {
        if(function_exists("mb_substr")){
            if($suffix)
                return mb_substr($str, $start, $length, $charset)."...";
            else
                return mb_substr($str, $start, $length, $charset);
        }elseif(function_exists('iconv_substr')) {
            if($suffix)
                return iconv_substr($str,$start,$length,$charset)."...";
            else
                return iconv_substr($str,$start,$length,$charset);
        }
        $re['utf-8'] = "/[x01-x7f]|[xc2-xdf][x80-xbf]|[xe0-xef][x80-xbf]{2}|[xf0-xff][x80-xbf]{3}/";
        $re['gb2312'] = "/[x01-x7f]|[xb0-xf7][xa0-xfe]/";
        $re['gbk'] = "/[x01-x7f]|[x81-xfe][x40-xfe]/";
        $re['big5'] = "/[x01-x7f]|[x81-xfe]([x40-x7e]|xa1-xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("",array_slice($match[0], $start, $length));
        if($suffix) return $slice."…";
        return $slice;
    }
    else
    {
        return $str;
    }
}

/**
 * GET 请求
 * 需要curl扩展支持
 */
function http_get($url){
    $oCurl = curl_init();
    if(stripos($url,"https://")!==FALSE){
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($oCurl, CURLOPT_SSLVERSION, 1);
    }
    curl_setopt($oCurl, CURLOPT_URL, $url);
    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
    $sContent = curl_exec($oCurl);
    $aStatus = curl_getinfo($oCurl);
    curl_close($oCurl);
    if(intval($aStatus["http_code"])==200){
        return $sContent;
    }else{
        return false;
    }
}

/**
 * POST 请求
 * 需要curl扩展支持
 */
function http_post($url,$param,$post_file=false){
    $oCurl = curl_init();
    if(stripos($url,"https://") !== FALSE){
        curl_setopt($oCurl,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($oCurl,CURLOPT_SSL_VERIFYHOST,false);
        curl_setopt($oCurl,CURLOPT_SSLVERSION,1);
    }
    if (is_string($param) || $post_file){
        $strPOST = $param;
    } else {
        $aPOST = array();
        foreach($param as $key => $val){
            $aPOST[] = $key."=" . urlencode($val);
        }
        $strPOST = join("&",$aPOST);
    }
    curl_setopt($oCurl,CURLOPT_URL,$url);
    curl_setopt($oCurl,CURLOPT_RETURNTRANSFER,1);
    curl_setopt($oCurl,CURLOPT_POST,true);
    curl_setopt($oCurl,CURLOPT_POSTFIELDS,$strPOST);
    $sContent = curl_exec($oCurl);
    $aStatus = curl_getinfo($oCurl);
    curl_close($oCurl);
    if(intval($aStatus["http_code"]) == 200){
        return $sContent;
    }else{
        return false;
    }
}

/**
 * 空格换行符过滤
 */
function trimAll($parma){
    if(is_array($parma)){
        return array_map('trimAll',$parma);
    }
    $before = array(" ","   ","\t","\r","\n");
    $after = array('','','','','');
    return str_replace($before,$after,$parma);
}

/**
+-----------------------------------------------------------------------------------------
 * 删除目录及目录下所有文件或删除指定文件
+-----------------------------------------------------------------------------------------
 * @param str $path   待删除目录路径
 * @param int $delDir 是否删除目录，1或true删除目录，0或false则只删除文件保留目录（包含子目录）
+-----------------------------------------------------------------------------------------
 * @return bool 返回删除状态
+-----------------------------------------------------------------------------------------
 */
function delDirAndFile($path, $delDir = FALSE) {
    if (is_array($path)) {
        foreach ($path as $subPath)
            delDirAndFile($subPath, $delDir);
    }
    if (is_dir($path)) {
        $handle = opendir($path);
        if ($handle) {
            while (false !== ( $item = readdir($handle) )) {
                if ($item != "." && $item != "..")
                    is_dir("$path/$item") ? delDirAndFile("$path/$item", $delDir) : unlink("$path/$item");
            }
            closedir($handle);
            if ($delDir)
                return rmdir($path);
        }
    } else {
        if (file_exists($path)) {
            return unlink($path);
        } else {
            return FALSE;
        }
    }
    clearstatcache();
}

/**
 * @param $birthday
 * @return bool|false|int
 *  根据生日计算年龄
 */
function birthday($birthday){
    $age = strtotime($birthday);
    if($age === false){
        return false;
    }
    list($y1,$m1,$d1) = explode("-",date("Y-m-d",$age));
    $now = strtotime("now");
    list($y2,$m2,$d2) = explode("-",date("Y-m-d",$now));
    $age = $y2 - $y1;
    if((int)($m2.$d2) < (int)($m1.$d1))
        $age -= 1;
    return $age;
}


/**
 * @param $str
 * @return null|string|string[]
 *
 * 将html 解析为 文本 (解码)
 */
function html2text($str){
    $str = preg_replace("/<style .*?<\\/style>/is", "", $str);
    $str = preg_replace("/<script .*?<\\/script>/is", "", $str);
    $str = preg_replace("/<br \\s*\\/>/i", ">>>>", $str);
    $str = preg_replace("/<\\/?p>/i", ">>>>", $str);
    $str = preg_replace("/<\\/?td>/i", "", $str);
    $str = preg_replace("/<\\/?div>/i", ">>>>", $str);
    $str = preg_replace("/<\\/?blockquote>/i", "", $str);
    $str = preg_replace("/<\\/?li>/i", ">>>>", $str);
    $str = preg_replace("/ /i", " ", $str);
    $str = preg_replace("/ /i", " ", $str);
    $str = preg_replace("/&/i", "&", $str);
    $str = preg_replace("/&/i", "&", $str);
    $str = preg_replace("/</i", "<", $str);
    $str = preg_replace("/</i", "<", $str);
    $str = preg_replace("/“/i", '"', $str);
    $str = preg_replace("/&ldquo/i", '"', $str);
    $str = preg_replace("/‘/i", "'", $str);
    $str = preg_replace("/&lsquo/i", "'", $str);
    $str = preg_replace("/'/i", "'", $str);
    $str = preg_replace("/&rsquo/i", "'", $str);
    $str = preg_replace("/>/i", ">", $str);
    $str = preg_replace("/>/i", ">", $str);
    $str = preg_replace("/”/i", '"', $str);
    $str = preg_replace("/&rdquo/i", '"', $str);
    $str = strip_tags($str);
    $str = html_entity_decode($str, ENT_QUOTES, "utf-8");
    $str = preg_replace("/&#.*?;/i", "", $str);
    return $str;
}

/**
 *  html 解析为 文本 （解码） --- 同上方法
 */

function getplaintextintrofromhtml($html) {

    // Remove the HTML tags
    $html = strip_tags($html);

    // Convert HTML entities to single characters
    $html = html_entity_decode($html, ENT_QUOTES, 'UTF-8');
    $html_len = mb_strlen($html,'UTF-8');

    // Make the string the desired number of characters
    // Note that substr is not good as it counts by bytes and not characters
    $html = mb_substr($html, 0, strlen($html), 'UTF-8');

    return $html;
}


/**
 *  解决ipLocation乱码
 * @param $in_charset
 * @param $out_charset
 * @param $arr
 * @return false|string
 */
function array_iconv($in_charset,$out_charset,$arr){
    return iconv($in_charset,$out_charset,var_export($arr,true));
}
