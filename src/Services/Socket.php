<?php
namespace Chunrongl\TqigouRpcService\Services;


use Chunrongl\TqigouRpcService\Exceptions\BusinessException;
use Chunrongl\TqigouRpcService\Exceptions\InvalidConfigException;
use Hprose\Socket\Server;

class Socket
{
    public function register(){
        $server = new Server(null);
        $server->uris=[];

        $server->setErrorTypes(E_ALL);
        $server->setDebugEnabled(false);


        $server->onSendError = function ($error, \stdClass $context) {
            \Log::error($error);
            throw new BusinessException($error->getMessage(),$error->getCode());
        };

        $uris = config('tqigou-rpc-server.uris');

        if (!is_array($uris)) {
            throw new InvalidConfigException('配置监听地址格式有误',500);
        }

        // 添加监听地址
        array_map(function ($uri) use ($server) {
            $server->addListener($uri);
        }, $uris);

        return $server;
    }

}