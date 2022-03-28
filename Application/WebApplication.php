<?php

namespace Msgframework\Lib\Application;

use Msgframework\Lib\Language\LanguageFactoryInterface;
use Msgframework\Lib\Language\Language;
use Msgframework\Lib\Route\Router;
use Msgframework\Lib\Config\Config;
use Msgframework\Lib\Document\Document;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class WebApplication extends AbstractApplication implements WebApplicationInterface
{
    protected string $title;
    public string $charSet = 'utf-8';
    public string $httpVersion = '1.1';

    protected Document $document;
    protected Router $router;
    protected Language $language;

    public function __construct($name, $title, $factory, ConfigurationInterface $configuration)
    {
        parent::__construct($name, $factory, $configuration);

        $this->title = $title;
    }

    public function beforeStart(): void
    {
    }

    public function start(): void
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDir(): string
    {
        return $this->factory->getRootDir() . DIRECTORY_SEPARATOR . $this->getName();
    }

    public function getRouter(): Router
    {
        if (!isset($this->router)) {
            $config = $this->getConfig();
            $request = $this->factory->getRequest();
            $type = $config->get('route_type', 'simple');

            $builder = $this->factory->getContainer()->get('router.' . $type);
            $this->router = new Router($this, $request, $builder->buildRules($this));
        }
        return $this->router;
    }

    public function getConfig(): Config
    {
        return $this->config;
    }

    public function getLanguage(): Language
    {
        if (!isset($this->language)) {
            $config = $this->getConfig();
            $container = $this->factory->getContainer();

            $factory = $container->get(LanguageFactoryInterface::class);
            $this->language = $factory->createLanguage($this, $config->get('lang', 'ru-RU'), $config->get('debug', false));
        }

        return $this->language;
    }

    public function error()
    {
        $this->getRouter()->setError();
    }
}