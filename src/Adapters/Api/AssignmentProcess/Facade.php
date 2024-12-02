<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\AssignmentProcess;

use ILIAS\DI\UIServices;
use srag\Plugins\UserDefaults\Adapters\Api\AssignmentProcess\Requests\RenderTable;
use srag\Plugins\UserDefaults\Adapters\Api\AssignmentProcess\Responses\Table;
use srag\Plugins\UserDefaults\Adapters\Api\AssignmentProcess\Requests\RenderForm;
use srag\Plugins\UserDefaults\Adapters\Api\AssignmentProcess\Responses\Form;
use srag\Plugins\UserDefaults\Adapters\Api\AssignmentProcess\Requests\HandleFormSubmission;
use srag\Plugins\UserDefaults\Adapters\Config\Configs;
use srag\Plugins\UserDefaults\Domain\Ports\AssignmentProcessService;

class Facade
{
    private function __construct(
        private readonly AssignmentProcessService $service,
        private readonly UIServices $iliasUiServices
    ) {
    }

    public static function new(Configs $configs): Facade
    {
        return new self($configs->assignmentProcessService, $configs->iliasUiServices);
    }

    public function findAll(): array
    {
        $assignmentProcesses = $this->service->findAll();
        $assignmentProcessResponses = [];

        foreach ($assignmentProcesses as $assignmentProcess) {
            $assignmentProcessResponses[] = "";
        }
        return $assignmentProcessResponses;
    }

    /**
     * @throws \ilCtrlException
     * @throws \ilException
     */
    public function renderTable(RenderTable $request): void
    {
        $this->iliasUiServices->mainTemplate()->setContent(Table::new($request->parentIliasGui)->getHtml());
    }

    public function renderForm(RenderForm $request): void
    {
        $this->iliasUiServices->mainTemplate()->setContent(
            Form::new($request->parentIliasGui, $request->assignmentProcessId)->getHtml()
        );
    }

    public function handleFormSubmission(HandleFormSubmission $request): void
    {
        $form = Form::new($request->parentIliasGui, $request->assignmentProcessId);
        $form->setValuesByPost();

        //todo checkInput; saveObject to $service
        if ($form->saveObject()) {
            $request->onSuccess();
        }
        $this->iliasUiServices->mainTemplate()->setContent($form->getHTML());
    }
}
