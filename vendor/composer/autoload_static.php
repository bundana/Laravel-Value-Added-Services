<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit9b69897118271c55fb76d2c8e9af7e49
{
    public static $prefixLengthsPsr4 = array (
        'B' => 
        array (
            'Bundana\\Services\\' => 17,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Bundana\\Services\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
            1 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit9b69897118271c55fb76d2c8e9af7e49::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit9b69897118271c55fb76d2c8e9af7e49::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit9b69897118271c55fb76d2c8e9af7e49::$classMap;

        }, null, ClassLoader::class);
    }
}
