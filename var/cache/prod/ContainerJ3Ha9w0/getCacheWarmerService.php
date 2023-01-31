<?php

namespace ContainerJ3Ha9w0;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/*
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getCacheWarmerService extends App_KernelProdContainer
{
    /*
     * Gets the public 'cache_warmer' shared service.
     *
     * @return \Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerAggregate
     */
    public static function do($container, $lazyLoad = true)
    {
        return $container->services['cache_warmer'] = new \Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerAggregate(new RewindableGenerator(function () use ($container) {
            yield 0 => ($container->privates['doctrine.orm.default_metadata_cache_warmer'] ?? $container->load('getDoctrine_Orm_DefaultMetadataCacheWarmerService'));
            yield 1 => ($container->privates['config_builder.warmer'] ?? $container->load('getConfigBuilder_WarmerService'));
            yield 2 => ($container->privates['router.cache_warmer'] ?? $container->load('getRouter_CacheWarmerService'));
            yield 3 => ($container->privates['doctrine.orm.proxy_cache_warmer'] ?? $container->load('getDoctrine_Orm_ProxyCacheWarmerService'));
        }, 4), false, ($container->targetDir.''.'/App_KernelProdContainerDeprecations.log'));
    }
}
