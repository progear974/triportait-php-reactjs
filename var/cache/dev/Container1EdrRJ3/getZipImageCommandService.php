<?php

namespace Container1EdrRJ3;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getZipImageCommandService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'App\Command\ZipImageCommand' shared autowired service.
     *
     * @return \App\Command\ZipImageCommand
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/symfony/console/Command/Command.php';
        include_once \dirname(__DIR__, 4).'/src/Command/ZipImageCommand.php';
        include_once \dirname(__DIR__, 4).'/src/Services/ZippingService.php';

        $a = ($container->privates['App\\Repository\\ShootingRepository'] ?? $container->load('getShootingRepositoryService'));

        $container->privates['App\\Command\\ZipImageCommand'] = $instance = new \App\Command\ZipImageCommand($a, new \App\Services\ZippingService(($container->services['kernel'] ?? $container->get('kernel', 1)), $a));

        $instance->setName('app:zip-image');
        $instance->setDescription('Add a short description for your command');

        return $instance;
    }
}
