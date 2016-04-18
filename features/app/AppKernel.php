<?php

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
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
        return [
            new FrameworkBundle(),
            new SecurityBundle(),
            new SensioFrameworkExtraBundle(),
            new SwitchUserStatelessBundle(),
            new DoctrineBundle(),
            new UserBundle(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(sprintf('%s/config.yml', $this->getRootDir()));
    }
}
