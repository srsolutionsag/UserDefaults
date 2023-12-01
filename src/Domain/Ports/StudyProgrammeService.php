<?php

namespace srag\Plugins\UserDefaults\Domain\Ports;

use srag\Plugins\UserDefaults\Domain\Model;

class StudyProgrammeService
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
     * @return Model\StudyProgramme[]
     */
    public function findAll(): array
    {
        return $this->repository->findAll();
    }

}