<?php

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Dunglas\ApiBundle\DunglasApiBundle;
use Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle;
use SwitchUserStatelessBundle\SwitchUserStatelessBundle;
use SwitchUserStatelessBundle\Tests\UserBundle\UserBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        $bundles = [
            new FrameworkBundle(),
            new SecurityBundle(),
            new SensioFrameworkExtraBundle(),
            new SwitchUserStatelessBundle(),
        ];

        if ('api_platform' === $this->getEnvironment()) {
            $bundles[] = new DoctrineBundle();
            $bundles[] = new DunglasApiBundle();
            $bundles[] = new UserBundle();
        }

        return $bundles;
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(sprintf('%s/config_%s.yml', $this->getRootDir(), $this->getEnvironment()));
    }
}
