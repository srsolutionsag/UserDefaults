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
        return new self(Facade::new($configs->assignmentProcessService));
    }


    public function findAll(): array
    {
        return $this->facade->findAll();
    }

    public function getTableHtml(GetTableHtmlRequest $request): string
    {
        return $this->facade->getTableHtml($request);
    }

    public function getFormHtml(GetFormHtmlRequest $request): string
    {
        return $this->facade->getFormHtml($request);
    }

    public function handleFormSubmission(HandleFormSubmissionRequest $request): string
    {
        return $this->facade->handleFormSubmission($request);
    }
}