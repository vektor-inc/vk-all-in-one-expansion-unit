<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitc2b7f28ada6fc699bcea15b5f5d82e25
{
    public static $prefixLengthsPsr4 = array (
        'V' => 
        array (
            'VektorInc\\VK_Helpers\\' => 21,
            'VektorInc\\VK_Breadcrumb\\' => 24,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'VektorInc\\VK_Helpers\\' => 
        array (
            0 => __DIR__ . '/..' . '/vektor-inc/vk-helpers/src',
        ),
        'VektorInc\\VK_Breadcrumb\\' => 
        array (
            0 => __DIR__ . '/..' . '/vektor-inc/vk-breadcrumb/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitc2b7f28ada6fc699bcea15b5f5d82e25::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitc2b7f28ada6fc699bcea15b5f5d82e25::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitc2b7f28ada6fc699bcea15b5f5d82e25::$classMap;

        }, null, ClassLoader::class);
    }
}