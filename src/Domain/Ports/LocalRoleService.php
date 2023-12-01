<?php

namespace srag\Plugins\UserDefaults\Domain\Ports;

use srag\Plugins\UserDefaults\Domain\Model;

class LocalRoleService
{
    private function __construct(
        private Repository $repository
    )
    {

    }

    public static function new(Repository $repository): self
    {
        return new self($repository);
    }

    /**
     * @return Model\LocalRole[]
     */
    public function findAll(): array
    {
        return $this->repository->findAll();
    }

}