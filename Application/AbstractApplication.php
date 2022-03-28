<?php

namespace Msgframework\Lib\Application;

use Msgframework\Lib\Config\Config;
use Symfony\Component\Config\Definition\ConfigurationInterface;

abstract class AbstractApplication
{
    protected WebApplicationFactory $factory;
    protected Config $config;

    public function __construct(WebApplicationFactoryInterface $factory, ConfigurationInterface $configuration)
    {
        $this->factory = $factory;
        $this->config = new Config($configuration, $this->factory->getCacheDir());
        $this->config->load('config', $this->getName() . 'Config', array($this->getDir() . "/config/"));
    }

    public function close($code = 0)
    {
        exit($code);
    }
}