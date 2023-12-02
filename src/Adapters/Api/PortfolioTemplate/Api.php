<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\PortfolioTemplate;
use srag\Plugins\UserDefaults\Adapters\Config\Configs;

class Api
{

    private function __construct(
        private Facade $portfolioTemplates
    )
    {

    }

    public static function new(Configs $configs): Api
    {
        return new self(Facade::new($configs->portfolioTemplateService));
    }


    public function findAll(): array
    {
        return $this->portfolioTemplates->findAll();
    }
}