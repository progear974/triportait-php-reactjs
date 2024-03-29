<?php

namespace Container4EbycIi;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getCodeAnalyseCommandService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'App\Command\CodeAnalyseCommand' shared autowired service.
     *
     * @return \App\Command\CodeAnalyseCommand
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/symfony/console/Command/Command.php';
        include_once \dirname(__DIR__, 4).'/src/Command/CodeAnalyseCommand.php';
        include_once \dirname(__DIR__, 4).'/src/Services/OCR.php';

        $container->privates['App\\Command\\CodeAnalyseCommand'] = $instance = new \App\Command\CodeAnalyseCommand(($container->privates['App\\Repository\\ShootingRepository'] ?? $container->load('getShootingRepositoryService')), new \App\Services\OCR(), ($container->services['doctrine.orm.default_entity_manager'] ?? $container->load('getDoctrine_Orm_DefaultEntityManagerService')));

        $instance->setName('app:code-analyse');
        $instance->setDescription('Add a short description for your command');

        return $instance;
    }
}
