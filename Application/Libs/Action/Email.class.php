<?php
namespace Libs\Action;

use Org\PHPMailer\PHPMailer;

/**
 * Class Email
 * @package Libs\Action
 * @describe 邮件处理类
 */
class Email{
    //服务器配置,qq服务器
    /**
     * @var string
     */
    private $Username = '2459450271@qq.com';// SMTP.class 用户名  即邮箱的用户名
    /**
     * @var string
     */
    private $Password = 'nvpotvzkslziecjd'; // SMTP.class 密码  部分邮箱是授权码(例如163邮箱)【可以去qq邮箱设置查看】

    /**
     * @var string
     */
    private $addresser_email = "2459450271@qq.com";//发件人邮箱
    /**
     * @var string
     */
    private $addresser_name = "吴培忠";//发件人姓名


    /**
     * @var mixed
     */
    private $sendSuccess;//成功结果
    /**
     * @var mixed
     */
    private  $sendError;//失败结果

    /**
     * Email constructor.
     * @param string $Username ***SMTP用户名[可选]
     * @param string $Password ***SMTP密码[可选]
     * @param $addresser_email ***发件人邮箱[可选]
     * @param $addresser_name  ***发件人姓名[可选]
     * @describe 构造函数
     */
    public function __construct($Username='',$Password='',$addresser_email='', $addresser_name=''){
        //若提供用户名和密码，则执行重新初始化
        if($Username&&$Password&&$addresser_email&&$addresser_name){
            $this->Username = $Username;
            $this->Password = $Password;
            $this->addresser_email = $addresser_email;
            $this->addresser_name = $addresser_name;
        }
    }

    /**
     * @param $addressee_email  ***收件人邮箱
     * @param $addressee_name   ***收件人名字
     * @param $textSubject      ***主题
     * @param $htmlBody         ***内容
     * @param $accessoryPath    ***附件 [可选]
     * @return string|bool
     * @describe 发送邮件
     */
    public function sendEmail($addressee_email,$addressee_name,$textSubject,$htmlBody,$accessoryPath=""){
        //发送邮箱
        $mail = new PHPMailer(true);
        //服务器配置
        $mail->CharSet ="UTF-8";                     //设定邮件编码
        $mail->SMTPDebug = 0;                        // 调试模式输出
        $mail->isSMTP();                             // 使用SMTP
        $mail->Host = 'ssl://smtp.qq.com';                // SMTP服务器
        $mail->SMTPAuth = true;                      // 允许 SMTP.class 认证
        $mail->Username = $this->Username;                // SMTP.class 用户名  即邮箱的用户名
        $mail->Password = $this->Password;             // SMTP.class 密码  部分邮箱是授权码(例如163邮箱)【可以去qq邮箱设置查看】
        $mail->SMTPSecure = 'ssl';                    // 允许 TLS 或者ssl协议
        $mail->Port = 465;                            // 服务器端口 25 或者465 具体要看邮箱服务器支持

        $mail->setFrom($this->addresser_email, $this->addresser_name);  //发件人
        $mail->addAddress($addressee_email, $addressee_name);  // 收件人
        $mail->addReplyTo($this->addresser_email, $this->addresser_name); //回复的时候回复给哪个邮箱 建议和发件人一致

        //发送附件
        if($accessoryPath){ $mail->addAttachment($accessoryPath); }         // 添加附件
        // $mail->addAttachment('../thumb-1.jpg', 'new.jpg');    // 发送附件并且重命名

        //Content
        $mail->isHTML(true);                                  // 是否以HTML文档格式发送  发送后客户端可直接显示对应HTML内容
        $mail->Subject = $textSubject . time();//主题
        $mail->Body    = '<h1>您好！'.$addressee_name.'</h1>'.$htmlBody. "<br/>".date('Y-m-d H:i:s');//内容
        $mail->AltBody = htmlspecialchars(trim(strip_tags($htmlBody)));//若对方不支持html，则使用纯文本内容

        $sendRes = $mail->send();
        if($sendRes){
            $this->sendSuccess = $sendRes;
            return $sendRes; //成功则返回发送结果
        }else{
            $this->sendError = $mail->ErrorInfo;
            return false; //失败
        }
    }

    /**
     * @return mixed
     * @describe 返回成功结果
     */
    public function getSendSuccess(){
        return $this->sendSuccess?$this->sendSuccess:false;
    }

    /**
     * @return mixed
     * @describe 返回失败的错误信息
     */
    public function getSendError(){
        return $this->sendError?$this->sendError:false;
    }

}