<?php

namespace srag\Plugins\UserDefaults\Domain\Ports;

use  srag\Plugins\UserDefaults\Domain\Model;

interface Repository
{

    /**
     * @return Model\Entities\Entity[]
     */
    public function findAll(): array;

}