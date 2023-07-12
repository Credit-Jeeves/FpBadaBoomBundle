<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function __construct()
    {
        parent::__construct($environment = 'test', $debug = true);
    }

    public function registerBundles(): iterable
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Fp\BadaBoomBundle\FpBadaBoomBundle(),
        );

        return $bundles;
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir();
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir();
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/config/app.yml');
    }
}