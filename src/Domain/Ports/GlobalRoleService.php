<?php

namespace srag\Plugins\UserDefaults\Domain\Ports;

use srag\Plugins\UserDefaults\Domain\Model;

class GlobalRoleService
{
    private function __construct(private readonly Repository $repository)
    {
    }

    public static function new(Repository $repository): self
    {
        return new self($repository);
    }

    /**
     * @return Model\GlobalRole[]
     */
    public function findAll(): array
    {
        return $this->repository->findAll();
    }

}
