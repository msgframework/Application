<?php

namespace Msgframework\Lib\Application;

use Joomla\DI\Container;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\HttpFoundation\Request;

interface WebApplicationFactoryInterface
{
    public function getRootDir(): string;
    public function getCacheDir(): string;
    public function getRequest(): Request;
    public function getContainer(): Container;
}