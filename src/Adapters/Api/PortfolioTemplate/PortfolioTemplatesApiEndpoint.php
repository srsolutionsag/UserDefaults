<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\PortfolioTemplate;
use srag\Plugins\UserDefaults\Adapters\Config\Configs;

class PortfolioTemplatesApiEndpoint
{

    private function __construct(
        private PortfolioTemplatesFacade $portfolioTemplates
    )
    {

    }

    public static function new(Configs $configs): PortfolioTemplatesApiEndpoint
    {
        return new self(PortfolioTemplatesFacade::new($configs->portfolioTemplateService));
    }


    public function get(): array
    {
        return $this->portfolioTemplates->get();
    }
}