<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\PortfolioTemplate;

use srag\Plugins\UserDefaults\Domain\Model\PortfolioTemplate;

class PortfolioTemplateResponse
{
    private function __construct(
        public int $id, public string $title
    )
    {

    }

    public static function formDomain(PortfolioTemplate $portfolio): PortfolioTemplateResponse
    {
        return new self($portfolio->objId->value, $portfolio->title->value);
    }
}