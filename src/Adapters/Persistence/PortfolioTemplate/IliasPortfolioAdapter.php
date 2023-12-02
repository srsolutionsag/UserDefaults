<?php

namespace srag\Plugins\UserDefaults\Adapters\Persistence\PortfolioTemplate;

use srag\Plugins\UserDefaults\Domain\Model\OrgUnit;
use srag\Plugins\UserDefaults\Domain\Model\PortfolioTemplate;

class IliasPortfolioAdapter
{

    private function __construct(public int $objId, public string $title)
    {

    }

    public static function new(int $objId, string $title): self
    {
        return new self($objId, $title);
    }

    public function toDomain(): PortfolioTemplate
    {
        return PortfolioTemplate::new($this->objId, $this->title);
    }
}