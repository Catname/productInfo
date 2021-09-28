<?php
/**
 * @author: ZhangHQ
 * @email : tomcath@foxmail.com
 */
namespace Catname\ProductInfo;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    protected $defer = true;

    public function register()
    {
        $this->app->singleton(ProductInfo::class, function(){
            return new ProductInfo();
        });

        $this->app->alias(ProductInfo::class, 'productInfo');
    }

    public function provides()
    {
        return [ProductInfo::class, 'productInfo'];
    }
}