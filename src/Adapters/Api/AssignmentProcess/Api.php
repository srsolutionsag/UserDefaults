<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\AssignmentProcess;

use srag\Plugins\UserDefaults\Adapters\Config\Configs;

class Api
{

    private function __construct(
        private Facade $facade
    )
    {

    }

    public static function new(Configs $configs): Api
    {
        return new self(Facade::new($configs));
    }


    public function findAll(): array
    {
        return $this->facade->findAll();
    }

    /**
     * @throws \ilException
     * @throws \ilCtrlException
     */
    public function renderTable(object $parentIliasGui): void
    {
        $this->facade->renderTable(Requests\RenderTable::new($parentIliasGui));
    }

    public function renderForm(object $parentIliasGui, ?int $assignmentProcessId = null): void
    {
        $this->facade->renderForm(Requests\RenderForm::new($parentIliasGui, $assignmentProcessId));
    }

    public function handleFormSubmission(object $parentIliasGui, ?int $assignmentProcessId = null, ?callable $onSuccess = null): void
    {
        $this->facade->handleFormSubmission(Requests\HandleFormSubmission::new($parentIliasGui, $assignmentProcessId, $onSuccess));
    }
}