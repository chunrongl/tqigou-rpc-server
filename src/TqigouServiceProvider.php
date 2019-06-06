<?php
namespace Chunrongl\TqigouRpcService;

use Chunrongl\tqigouRpcService\Routes\RouteManage;
use Hprose\Socket\Server;
use Illuminate\Support\ServiceProvider;

class TqigouServiceProvider extends ServiceProvider
{

    public function boot(){
        $configPath = __DIR__ . '/config/tqigou-rpc-server.php';
        $publishPath = config_path('tqigou-rpc-server.php');

        $this->publishes([$configPath => $publishPath], 'config');
    }

    public function register(){
        $configPath = __DIR__ . '/config/tqigou-rpc-server.php';
        $this->mergeConfigFrom($configPath, 'tqigou-rpc-server');

        $routePath = __DIR__ . '/config/tqigou-rpc-route.php';
        $this->mergeConfigFrom($routePath, 'tqigou-rpc-route');


        $this->app->singleton('tqigou.server', function ($app) {
            $server = new Server();

            $server->onSendError = function ($error, \stdClass $context) {
                \Log::error($error);
            };

            $uris = config('tqigou-rpc-server.listening_uris');

            if (!is_array($uris)) {
                throw new \Exception('配置监听地址格式有误', 500);
            }

            // 添加监听地址
            array_map(function ($uri) use ($server) {
                $server->addListener($uri);
            }, $uris);

            return $server;
        });

        $this->app->singleton('tqigou.route', function ($app) {
            return new RouteManage();
        });
    }
}