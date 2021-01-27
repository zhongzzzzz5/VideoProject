<?php
namespace Org\Net;
class JWT
{
    /**
     * 头部
     * @var array
     */
    private $header;
    /**
     * 使用HMAC生成信息摘要时所使用的密钥
     * @var string
     */
    private $key;
    /**
     * 该JWT的签发者
     * @var string
     */
    private $iss;
    /**
     * 签发时间
     * @var int
     */
    private $iat;
    /**
     * 过期时间
     * @var int
     */
    private $exp;
    /**
     * @var int
     * 该时间之前不接收处理该Token
     */
    private $nbf;
    /**
     * 面向的用户
     * @var string
     */
    private $sub;
    /**
     * 该Token唯一标识
     * @var string
     */
    private $jti;
    /**
     * 自定义数据
     * @var mixed
     */
    private $claim;

    public function __construct()
    {
        $this->header = array(
            'alg' => 'HS256', //生成signature的算法
            'typ' => 'JWT'    //类型
        );
        $this->key = '';//使用HMAC生成信息摘要时所使用的密钥
        $this->iss = 'Auth0';//该JWT的签发者
        $this->iat = time();//签发时间
        $this->exp = time() + 7200;//过期时间
        $this->nbf = time() + 0;//该时间之前不接收处理该Token
        $this->sub = 'www.auth.com';//面向的用户
        $this->jti = md5(uniqid('JWT') . time());//该Token唯一标识
    }

    /**
     * @param string $key
     */
    public function setKey(string $key): void
    {
        $this->key = $key;
    }

    /**
     * @param string $iss
     */
    public function setIss(string $iss): void
    {
        $this->iss = $iss;
    }

    /**
     * @param int $iat
     */
    public function setIat(int $iat): void
    {
        $this->iat = $iat;
    }

    /**
     * @param int $exp
     */
    public function setExp(int $exp): void
    {
        $this->exp = $exp;
    }

    /**
     * @param int $nbf
     */
    public function setNbf(int $nbf): void
    {
        $this->nbf = $nbf;
    }

    /**
     * @param string $sub
     */
    public function setSub(string $sub): void
    {
        $this->sub = $sub;
    }

    /**
     * @param string $jti
     */
    public function setJti(string $jti): void
    {
        $this->jti = $jti;
    }

    /**
     * @param mixed $claim
     */
    public function setClaim($claim): void
    {
        $this->claim = $claim;
    }

    /**
     * @return mixed
     */
    public function getClaim()
    {
        return $this->claim;
    }

    /**
     * @return int
     */
    public function getExp(): int
    {
        return $this->exp;
    }

    /**
     * 获取jwt token
     * @return string
     */
    public function getToken()
    {
        $payload = [
            'iss' => $this->iss,  //该JWT的签发者
            'iat' => $this->iat,  //签发时间
            'exp' => $this->exp,  //过期时间
            'nbf' => $this->nbf,  //该时间之前不接收处理该Token
            'sub' => $this->sub,  //面向的用户
            'jti' => $this->jti,  //该Token唯一标识
            'claim' => $this->claim, //插入数据
        ];
        $header = $this->header;
        $key = $this->key;
        $base64header = self::base64UrlEncode(json_encode($header, JSON_UNESCAPED_UNICODE));
        $base64payload = self::base64UrlEncode(json_encode($payload, JSON_UNESCAPED_UNICODE));
        return $base64header . '.' . $base64payload . '.' . self::signature($base64header . '.' . $base64payload, $key, $header['alg']);
    }


    /**
     * 验证token是否有效,默认验证exp,nbf,iat时间
     * @param string $Token 需要验证的token
     * @return bool|mixed
     */
    public function verifyToken(string $Token)
    {
        $tokens = explode('.', $Token);
        if (count($tokens) != 3)
            return false;

        list($base64header, $base64payload, $sign) = $tokens;

        //获取jwt算法
        $base64decodeheader = json_decode(self::base64UrlDecode($base64header), JSON_OBJECT_AS_ARRAY);
        if (empty($base64decodeheader['alg']))
            return false;

        //签名验证
        if (self::signature($base64header . '.' . $base64payload, $this->key, $base64decodeheader['alg']) !== $sign)
            return false;

        $payload = json_decode(self::base64UrlDecode($base64payload), JSON_OBJECT_AS_ARRAY);

        //签发时间大于当前服务器时间验证失败
        if (isset($payload['iat']) && $payload['iat'] > time())
            return false;

        //过期时间小宇当前服务器时间验证失败
        if (isset($payload['exp']) && $payload['exp'] < time())
            return false;

        //该nbf时间之前不接收处理该Token
        if (isset($payload['nbf']) && $payload['nbf'] > time())
            return false;

        $this->setExp($payload['exp']);
        $this->setClaim($payload['claim']);
        return $payload;
    }


    /**
     * base64UrlEncode   https://jwt.io/  中base64UrlEncode编码实现
     * @param string $input 需要编码的字符串
     * @return string
     */
    private static function base64UrlEncode(string $input)
    {
        return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
    }

    /**
     * base64UrlEncode  https://jwt.io/  中base64UrlEncode解码实现
     * @param string $input 需要解码的字符串
     * @return bool|string
     */
    private static function base64UrlDecode(string $input)
    {
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $addlen = 4 - $remainder;
            $input .= str_repeat('=', $addlen);
        }
        return base64_decode(strtr($input, '-_', '+/'));
    }

    /**
     * HMACSHA256签名   https://jwt.io/  中HMACSHA256签名实现
     * @param string $input 为base64UrlEncode(header).".".base64UrlEncode(payload)
     * @param string $key
     * @param string $alg 算法方式
     * @return mixed
     */
    private static function signature(string $input, string $key, string $alg = 'HS256')
    {
        $alg_config = array(
            'HS256' => 'sha256'
        );
        return self::base64UrlEncode(hash_hmac($alg_config[$alg], $input, $key, true));
    }
}
