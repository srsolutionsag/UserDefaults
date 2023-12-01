<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\OrgUnits;
use srag\Plugins\UserDefaults\Adapters\Config\Configs;

class OrgUnitsApiEndpoint
{

    private function __construct(
        private OrgUnitsFacade $orgUnits
    )
    {

    }

    public static function new(Configs $configs): OrgUnitsApiEndpoint
    {
        return new self(OrgUnitsFacade::new($configs->orgUnitService));
    }


    public function get(): array
    {
        return $this->orgUnits->get();
    }
}