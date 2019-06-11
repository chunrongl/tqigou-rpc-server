<?php
namespace Chunrongl\TqigouRpcService;

use Chunrongl\TqigouRpcService\Commands\SocketServer;
use Chunrongl\TqigouRpcService\Routes\RouteManage;
use Hprose\Socket\Server;
use Illuminate\Support\ServiceProvider;

class TqigouServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(){
        $configPath = __DIR__ . '/Config/tqigou-rpc-server.php';
        $publishPath = config_path('tqigou-rpc-server.php');
        $this->publishes([$configPath => $publishPath], 'config');

        $configRoutePath=__DIR__.'/Config/tqigou-rpc-route.php';
        $publishRoutePath=base_path('routes/tqigou-rpc-route.php');
        $this->publishes([$configRoutePath=>$publishRoutePath],'config');

        if(file_exists($publishRoutePath)) {
            $this->loadRoutesFrom($publishRoutePath);
        }

        if ($this->app->runningInConsole()) {
            $this->commands([
                SocketServer::class,
            ]);
        }
    }

    public function register(){
        $configPath = __DIR__ . '/config/tqigou-rpc-server.php';
        $this->mergeConfigFrom($configPath, 'tqigou-rpc-server');


        $this->app->singleton('tqigou.server', function ($app) {
            $server = new Server(null);
            $server->uris=[];

            $server->setErrorTypes(E_ALL);
            $server->setDebugEnabled(true);

            $server->onSendError = function ($error, \stdClass $context) {
                \Log::error($error);
            };

            $uris = config('tqigou-rpc-server.uris');

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