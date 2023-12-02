<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\PortfolioTemplate\Responses;

use srag\Plugins\UserDefaults\Domain\Model;

class PortfolioTemplate
{
    private function __construct(
        public int $id, public string $title
    )
    {

    }

    public static function formDomain(Model\PortfolioTemplate $portfolio): PortfolioTemplate
    {
        return new self($portfolio->objId->value, $portfolio->title->value);
    }
}