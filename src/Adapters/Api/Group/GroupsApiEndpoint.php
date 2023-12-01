<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\Group;
use srag\Plugins\UserDefaults\Adapters\Config\Configs;

class GroupsApiEndpoint
{

    private function __construct(
        private GroupsFacade $groups
    )
    {

    }

    public static function new(Configs $configs): GroupsApiEndpoint
    {
        return new self(GroupsFacade::new($configs->groupService));
    }


    public function get(): array
    {
        return $this->groups->get();
    }
}