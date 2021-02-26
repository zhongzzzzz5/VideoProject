<?php
namespace Admin\Controller;
use Admin\Model\NoticeModel;
use Think\Controller;

/**
 * Class IndexController
 * @package Admin\Controller
 * @describe 首页
 */
class IndexController extends CommonController {
    /**
     *  单一入口
     */
    public function index(){
        $this->display();
    }
    /**
     *  系统公告
     */
    public function showNotice(){
        //渲染数据
        if(IS_GET){
            $model = M("Notice");
            $data = $model->select();
            if($data){
                $data[0]["content"] = html2text($data[0]["content"]); //格式化内容
                $data[0]["add_time"] = date("Y-m-d H:i:s",$data[0]["add_time"]);//格式化编辑时间
                $json = [
                    "code"=>200,
                    "data"=>$data,
                ];
            }
            /** @var TYPE_NAME $json */
            $this->ajaxReturn($json);
        }

        //添加数据
        if(IS_POST){
            $post = I("post.");
            if(!empty($post)){
                //如果不存在id，执行添加数据
                if(!$post["id"]){
                    $notice = new NoticeModel();
                    $result = $notice->addData($post);
                    if($result){
                        $json = [
                            "code"=>200,
                            "msg"=>"add ok"
                        ];
                    }else{
                        $json = [
                            "code"=>500,
                            "msg"=>"server error(add)"
                        ];
                    }
                }
                //存在id，执行更新数据
                if($post["id"]){
                    $notice = new NoticeModel();
                    $result = $notice->upData($post);
                    if($result){
                        $json = [
                            "code"=>200,
                            "msg"=>"updata ok"
                        ];
                    }else{
                        $json = [
                            "code"=>500,
                            "msg"=>"server error(updata)"
                        ];
                    }
                }
            }else{
                $json = [
                    "code"=>400,
                    "msg"=>"client no data"
                ];
            }
            /** @var TYPE_NAME $json */
            $this->ajaxReturn($json);
        }
    }

    /**
     *  首页左边的菜单
     */
    public function tabs(){
        $json = [
            //内容管理
            "contentManagement"=>[
                ["title"=>"影视接口","icon"=>"icon-zhiding","href"=>"","spread"=>false,
                    "children"=>[
                        ["title"=>"接口管理","icon"=>"&#xe609;","href"=>"page/video/api/api.html","spread"=>false],
                        ["title"=>"接口分类管理","icon"=>"&#xe609;","href"=>"page/video/api_category/api_category.html","spread"=>false],
                        ["title"=>"版本管理","icon"=>"&#xe609;","href"=>"","spread"=>false],
                        ["title"=>"软件下载管理","icon"=>"&#xe609;","href"=>"","spread"=>false],
                        ["title"=>"轮播图管理","icon"=>"&#xe609;","href"=>"","spread"=>false],
                        ["title"=>"影视分类","icon"=>"&#xe609;","href"=>"page/video/video_type/video_type.html","spread"=>false],
                        ["title"=>"影视管理","icon"=>"&#xe609;","href"=>"","spread"=>false],
                        ["title"=>"数据采集","icon"=>"&#xe609;","href"=>"http://www.5wpz.top/mac_cms/admin1.php/admin/index/index.html","spread"=>false],
                        
                    ]
                ],
                ["title"=>"文章列表","icon"=>"icon-text","href"=>"page/news/newsList.html","spread"=>false],
                ["title"=>"软件管理","icon"=>"&#xe609;","href"=>"page/app/app.html","spread"=>false],
                ["title"=>"图片管理","icon"=>"&#xe634;","href"=>"page/img/images.html","spread"=>false],
                ["title"=>"站内信","icon"=>"iconfont icon-youxiang_huaban","href"=>"","spread"=>false,
                    "children"=>[
                        ["title"=>"邮件管理","icon"=>"iconfont icon-youxiang1","href"=>"page/message/email/email.html","spread"=>false],
                        ["title"=>"站内聊天","icon"=>"iconfont icon-huaban","href"=>"","spread"=>false],
                    ]
                ]
            ],
            //用户中心
            "memberCenter"=>[
                ["title"=>"所有用户","icon"=>"&#xe612;","href"=>"page/user/userList.html","spread"=>false],
                ["title"=>"权限管理","icon"=>"icon-look","href"=>"","spread"=>false],
                ["title"=>"会员等级","icon"=>"icon-vip","href"=>"page/user/userGrade.html","spread"=>false],
                ["title"=>"在线用户","icon"=>"icon-liulanqi","href"=>"page/user/userOnline.html","spread"=>false],
            ],
            //系统设置
            "systemeSttings"=>[
                ["title"=>"系统基本参数","icon"=>"&#xe631;","href"=>"page/systemSetting/basicParameter.html","spread"=>false],
                ["title"=>"系统公告","icon"=>"&#xe645;","href"=>"page/systemSetting/notice.html","spread"=>false],
                ["title"=>"联系作者","icon"=>"icon-qq","href"=>"page/systemSetting/author.html","spread"=>false],
                ["title"=>"设置锁屏","icon"=>"icon-lock","href"=>"page/systemSetting/lock.html","spread"=>false],
                ["title"=>"系统日志","icon"=>"icon-log","href"=>"page/systemSetting/logs.html","spread"=>false],
                ["title"=>"登录日志","icon"=>"icon-log","href"=>"page/systemSetting/login_log.html","spread"=>false],
                ["title"=>"友情链接","icon"=>"&#xe64c;","href"=>"page/systemSetting/linkList.html","spread"=>false],
                ["title"=>"图标管理","icon"=>"&#xe857;","href"=>"page/systemSetting/icons.html","spread"=>false],
            ],
            //使用文档
            "seraphApi"=>[
                ["title"=>"三级联动模块","icon"=>"icon-mokuai","href"=>"page/doc/addressDoc.html","spread"=>false],
                ["title"=>"bodyTab模块","icon"=>"icon-mokuai","href"=>"page/doc/bodyTabDoc.html","spread"=>false],
                ["title"=>"三级菜单","icon"=>"icon-mokuai","href"=>"page/doc/navDoc.html","spread"=>false],
            ]
        ];
        /** @var TYPE_NAME $json */
        $this->ajaxReturn($json);
    }

    /**
     * 网站备案号
     */
    public function copyright(){
        $model = M("System");
        $data = $model->find();
        $model_au = M("Author");
        $data_au = $model_au->find();
        $server_url = "http://".$_SERVER['HTTP_HOST'];
        $json = [
            "copyright" => $data['site_copyright'],
            "ICP" => $data['site_icp'],
            "wx_img"=>$server_url.__ROOT__.$data_au["wx_img_path"],
            "qq_img"=>$server_url.__ROOT__.$data_au["qq_img_path"],
        ];
        /** @var TYPE_NAME $json */
        $this->ajaxReturn($json);
    }
}