<?php

namespace Chunrongl\tqigouRpcService\Routes;


class RouteManage
{
    public function add(string $accessName, $path)
    {
        list($class,$methodName)=$this->formatPath($path);

        $this->addMethod($methodName,$class,$accessName);

        return $this;
    }

    private function formatPath($path){
        list($className,$methodName)=explode('@',$path);

        $namespaceName=$this->getNamespace();

        $class=resolve(join('\\',array_filter([$namespaceName,$className])));

        return [$class,$methodName];
    }

    private function getNamespace(){
        return config('tqigou-rpc-server.namespace');
    }

    private function addMethod(string $method, $class, string $alias)
    {
        app('tqigou.server')->addMethod($method, $class, $alias);
    }
}