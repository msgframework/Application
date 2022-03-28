<?php

namespace Msgframework\Lib\Application;

use Joomla\DI\Container;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\HttpFoundation\Request;

class WebApplicationFactory implements WebApplicationFactoryInterface
{
    protected string $root_dir;
    protected string $cache_dir;
    protected Request $request;
    protected Container $container;

    public function __construct(Request $request, Container $container, string $root_dir, string $cache_dir)
    {
        $this->request = $request;
        $this->container = $container;
        $this->root_dir = $root_dir;
        $this->cache_dir = $cache_dir;
    }

    public function getRootDir(): string
    {
        return $this->root_dir;
    }

    public function getCacheDir(): string
    {
        return $this->cache_dir;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * Create a new WebApplication object
     *
     * @param string $type
     * @param string $name
     * @param string $title
     * @param ConfigurationInterface $configuration
     *
     * @return WebApplicationInterface
     */
    public function createApplication(string $type, string $name, string $title, ConfigurationInterface $configuration): WebApplicationInterface
    {
        $application = new WebApplication($name, $type, $title, $this, $configuration);

        return $application;
    }
}