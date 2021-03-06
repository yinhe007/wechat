<?php

require __DIR__ . '/../vendor/autoload.php';

use PFinal\Wechat\Kernel;
use PFinal\Wechat\Message\Receive;
use PFinal\Wechat\Support\Log;
use PFinal\Wechat\Message;
use PFinal\Wechat\WechatEvent;

//$config = array(
//    //公众平台
//    'appId' => 'wxf26a7eereabaeee2',
//    'appSecret' => 'dc49ba13254cc09219f20e5186042324',
//    'token' => 'pfinal',
//    'encodingAesKey' => 'Sv11jWIKQW1x8CQAD6EZcssSomTA3kdJOtS3345',
//    'middleUrl' => 'http://wechat.it266.com/demo/authorize-redirect.php',
//
//    //微信支付
//    'mchId' => '12845234572', //商户号
//    'apiSecret' => '0919520aa2324d260f031fa6f016a4533', //API密钥
//    'sslCert' => '/Users/ethan/Desktop/esitools/cert/apiclient_cert.pem',
//    'sslKey' => '/Users/ethan/Desktop/esitools/cert/apiclient_key.pem',
//    'caInfo' => '/Users/ethan/Desktop/esitools/cert/rootca.pem',
//
//    //日志
//    'log' => array(
//        'level' => Monolog\Logger::DEBUG,
//        'file' => './wechat.log',
//    ),
//);

$config = require __DIR__ . '/config-local.php';

Kernel::init($config);


//注册事件消息处理函数
Kernel::register(Receive::TYPE_TEXT, function (WechatEvent $event) {

    $message = $event->getMessage();


    include __DIR__ . '/test-data.php'; // 一些测试用的数据

    Log::debug('收到请求', (array)$message);

    switch ($message->Content) {
        case 'hi':
            $event->setResponse('你好');
            break;
        case 'image':
            $event->setResponse(new \PFinal\Wechat\Message\Image($imageMediaId));
            break;
        case 'news':
            //$event->setResponse(new \PFinal\Wechat\Message\News('pFinal社区', '致力于提供优质PHP中文学习资源', 'http://www.pfinal.cn/', 'http://www.pfinal.cn/images/pfinal.png'));
            $event->setResponse(new \PFinal\Wechat\Message\News($news));
            break;
        case 'music':
            $event->setResponse(new \PFinal\Wechat\Message\Voice($voiceMediaId));
            break;
        case 'video':
            $event->setResponse(new \PFinal\Wechat\Message\Video($videoMediaId, $imageMediaId, '视频', '好看'));
            break;
        default:
            $text = date('Y-m-d H:i:s') . ' 消息已收到:' . $message->Content;
            $event->setResponse($text);
            break;
    }

    $event->stopPropagation();

});

/*
//调整优先级
Kernel::register(Receive::TYPE_TEXT, function (WechatEvent $event) {
    $event->setResponse($event->getApi()->responseText('haha'));
    $event->stopPropagation();
}, 1);
*/

Kernel::register(Receive::TYPE_EVENT_SUBSCRIBE, function (WechatEvent $event) {
    $event->setResponse($event->getApi()->buildReply('你关注或是不关注，我都在这里，不悲不喜~~'));
    $event->stopPropagation();
});

Kernel::register(Receive::TYPE_EVENT_UNSUBSCRIBE, function (WechatEvent $event) {
    $openid = $event->getMessage()->FromUserName;
    Log::debug($openid . '取消了关注');
});

Kernel::register(Receive::TYPE_SHORT_VIDEO, function (WechatEvent $event) {
    $event->setResponse('收到小视频');
    $event->stopPropagation();
});

Kernel::register(Receive::TYPE_VIDEO, function (WechatEvent $event) {
    $event->setResponse('收到视频');
    $event->stopPropagation();
});

Kernel::register(Receive::TYPE_EVENT_CLICK, function (WechatEvent $event) {
    $event->setResponse('点菜单' . $event->getMessage()->EventKey);
    $event->stopPropagation();
});


//处理微信服务器的请求
$response = Kernel::handle();

echo $response;