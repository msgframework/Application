<?php

namespace Msgframework\Lib\Application;

use Msgframework\Lib\Config\Config;
use Msgframework\Lib\Language\Language;
use Symfony\Component\HttpFoundation\Session\Session;

interface WebApplicationInterface
{
    public function start(): void;
    public function beforeStart(): void;
    public function getName(): string;
    public function getType(): string;
    public function getDir(): string;
    public function getConfig(): Config;
    public function getLanguage(): Language;
}