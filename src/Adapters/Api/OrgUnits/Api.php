<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\OrgUnits;
use srag\Plugins\UserDefaults\Adapters\Config\Configs;

class Api
{

    private function __construct(
        private Facade $orgUnits
    )
    {

    }

    public static function new(Configs $configs): Api
    {
        return new self(Facade::new($configs->orgUnitService));
    }


    public function findAll(): array
    {
        return $this->orgUnits->findAll();
    }

    public function findAllPositions(): array {
        return $this->orgUnits->findAllPositions();
    }
}