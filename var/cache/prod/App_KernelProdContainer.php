<?php

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.

if (\class_exists(\ContainerJ3Ha9w0\App_KernelProdContainer::class, false)) {
    // no-op
} elseif (!include __DIR__.'/ContainerJ3Ha9w0/App_KernelProdContainer.php') {
    touch(__DIR__.'/ContainerJ3Ha9w0.legacy');

    return;
}

if (!\class_exists(App_KernelProdContainer::class, false)) {
    \class_alias(\ContainerJ3Ha9w0\App_KernelProdContainer::class, App_KernelProdContainer::class, false);
}

return new \ContainerJ3Ha9w0\App_KernelProdContainer([
    'container.build_hash' => 'J3Ha9w0',
    'container.build_id' => 'ec00d16c',
    'container.build_time' => 1673207789,
], __DIR__.\DIRECTORY_SEPARATOR.'ContainerJ3Ha9w0');
