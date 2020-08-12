<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit183490e78d83859b56169e68ad046d9b
{
    public static $files = array (
        '7b11c4dc42b3b3023073cb14e519683c' => __DIR__ . '/..' . '/ralouphie/getallheaders/src/getallheaders.php',
        'a0edc8309cc5e1d60e3047b5df6b7052' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/functions_include.php',
    );

    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'Psr\\Log\\' => 8,
            'Psr\\Http\\Message\\' => 17,
        ),
        'G' => 
        array (
            'GuzzleHttp\\Psr7\\' => 16,
        ),
        'B' => 
        array (
            'Buzz\\' => 5,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Psr\\Log\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/log/Psr/Log',
        ),
        'Psr\\Http\\Message\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/http-message/src',
        ),
        'GuzzleHttp\\Psr7\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/psr7/src',
        ),
        'Buzz\\' => 
        array (
            0 => __DIR__ . '/..' . '/kriswallsmith/buzz/lib/Buzz',
        ),
    );

    public static $prefixesPsr0 = array (
        'A' => 
        array (
            'Admitad\\Api\\' => 
            array (
                0 => __DIR__ . '/..' . '/admitad/api/src',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit183490e78d83859b56169e68ad046d9b::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit183490e78d83859b56169e68ad046d9b::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit183490e78d83859b56169e68ad046d9b::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}