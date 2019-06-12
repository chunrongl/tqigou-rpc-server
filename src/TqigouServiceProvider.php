<?php
namespace Chunrongl\TqigouRpcService;

use Chunrongl\TqigouRpcService\Commands\SocketServer;
use Chunrongl\TqigouRpcService\Routes\RouteManage;
use Chunrongl\TqigouRpcService\Services\Socket;
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
            $server=(new Socket())->register();

            return $server;
        });

        $this->app->singleton('tqigou.route', function ($app) {
            return new RouteManage();
        });
    }
}