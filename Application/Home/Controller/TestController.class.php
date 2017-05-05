<?php
    namespace Home\Controller;
    use Think\Controller;
    class TestController extends Controller {

        public $api;
        public $wechat;

        public function __construct()
        {
            // 这是使用了Memcached来保存access_token
            S(array(
                'type'=>'memcache',
                'host'=>'localhost',
                'port'=>'11211',
                'prefix'=>'think',
                'expire'=>0
            ));

            // 开发者中心-配置项-AppID(应用ID)
            $appId = 'wx5ef579ca2488a8ac';
            // 开发者中心-配置项-AppSecret(应用密钥)
            $appSecret = 'cafa3af16373ccf3de0638fe4e27cf14';
            // 开发者中心-配置项-服务器配置-Token(令牌)
            $token = 'joiar';
            // 开发者中心-配置项-服务器配置-EncodingAESKey(消息加解密密钥)
            // $encodingAESKey = '072vHYArTp33eFwznlSvTRvuyOTe5YME1vxSoyZbzaV';

            // wechat模块 - 处理用户发送的消息和回复消息
            $this->wechat = new \Gaoming13\WechatPhpSdk\Wechat(array(
                'appId' => $appId,
                'token' => 	$token,
                // 'encodingAESKey' =>	$encodingAESKey //可选
            ));
            // api模块 - 包含各种系统主动发起的功能
            $this->api = new \Gaoming13\WechatPhpSdk\Api(
                array(
                    'appId' => $appId,
                    'appSecret'	=> $appSecret,
                    'get_access_token' => function(){
                        // 用户需要自己实现access_token的返回
                        return S('wechat_token');
                    },
                    'save_access_token' => function($token) {
                        // 用户需要自己实现access_token的保存
                        S('wechat_token', $token);
                    }
                )
            );
        }

        public function createMenu()
        {
             {
                 "button":[
                 {
                      "type":"click",
                      "name":"我的简历",
                      "url":"http://www.joiar.com/"
                  },
                  {
                      "type":"click",
                      "name":"我的博客",
                      "url":"http://www.joiar.com/"
                  },
                  {
                       "name":"关于我",
                       "sub_button":[
                       {
                           "type":"view",
                           "name":"邮箱",
                           "url":"https://github.com/Joiar?tab=stars"
                        },
                        {
                           "type":"click",
                           "name":"我的Github",
                           "url":"https://github.com/Joiar?tab=stars"
                        }]
                   }]
             }
        }

        public function index()
        {
            // 获取微信消息
            $msg = $this->wechat->serve();

            // 回复文本消息
            if ($msg->MsgType == 'text' && $msg->Content == '图文') {
                // 生成获取用户授权的链接
                $url = $this->api->get_authorize_url('snsapi_userinfo', 'http://joiar.com/wechat/index.php/Home/Test/loginOk');

                // 回复消息
                $this->wechat->reply(array(
                    'type' => 'news',
                        'articles' => array(
                         array(
                            'title' => $url,                               //可选
                            'description' => '图文消息描述1',                     //可选
                            'picurl' => 'http://me.diary8.com/data/img/demo1.jpg',  //可选
                            'url' => $url                      //可选
                         ),
                    )
                ));
                // $wechat->reply("你也好！ - 这是我回复的额！");
            } else {
                $this->wechat->reply("我有点不太懂呢。");
            }

            // 主动发送
            // $this->api->send($msg->FromUserName, '这是我主动发送的一条消息');
        }

        // public function loginOk()
        // {
        //     // 获取用户信息
        //     list($err, $user_info) = $this->api->get_userinfo_by_authorize('snsapi_userinfo');
        //     if ($user_info !== null) {
        //         var_dump($user_info);;
        //     } else {
        //         echo '授权失败！';
        //     }
        // }

        // public function info()
        // {
        //     dump(phpinfo());
        // }

        // public function createMenu()
        // {
        //     $res = $this->api->create_menu('
        //     {
        //         "button":[
        //             {
        //               "name":"刷题",
        //               "sub_button":[
        //                   {
        //                       "type":"view",
        //                       "name":"申论",
        //                       "url":"http://www.morrios.com"
        //                   },
        //                   {
        //                       "type":"view",
        //                       "name":"行测",
        //                       "url":"http://www.morrios.com"
        //                   }
        //               ]
        //             },
        //             {
        //               "type":"view",
        //               "name":"课程",
        //               "url":"http://www.morrios.com"
        //             },
        //             {
        //                 "name":"我的",
        //                 "sub_button":[
        //                     {
        //                         "type":"view",
        //                         "name":"个人中心",
        //                         "url":"http://www.morrios.com"
        //                     },
        //                     {
        //                         "type":"view",
        //                         "name":"职位筛选",
        //                         "url":"http://www.morrios.com"
        //                     }
        //                 ]
        //            }
        //         ]
        //     }');
        // }
    }
