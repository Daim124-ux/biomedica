<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitc2edf8889a63c4b8dee6f8cdb04f7b05
{
    public static $files = array (
        'd1760a1f3e8124a8eaa9d7fe7617cc86' => __DIR__ . '/../..' . '/src/functions.php',
    );

    public static $prefixLengthsPsr4 = array (
        'F' => 
        array (
            'FileUploadTypes\\' => 16,
        ),
        'C' => 
        array (
            'Composer\\Installers\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'FileUploadTypes\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
        'Composer\\Installers\\' => 
        array (
            0 => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitc2edf8889a63c4b8dee6f8cdb04f7b05::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitc2edf8889a63c4b8dee6f8cdb04f7b05::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitc2edf8889a63c4b8dee6f8cdb04f7b05::$classMap;

        }, null, ClassLoader::class);
    }
}