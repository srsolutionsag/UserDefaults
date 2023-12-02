<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\AssignmentProcess;

use srag\Plugins\UserDefaults\Adapters\Config\Configs;
use srag\Plugins\UserDefaults\Adapters\Ui;
use srag\Plugins\UserDefaults\Domain\Ports\AssignmentProcessService;

class Facade
{
    private function __construct(private AssignmentProcessService $service, private \ILIAS\DI\UIServices $iliasUiServices)
    {

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
    public function renderTable(Requests\RenderTable $request): void
    {
        $this->iliasUiServices->mainTemplate()->setContent(Responses\Table::new($request->parentIliasGui)->getHtml());
    }

    public function renderForm(Requests\RenderForm $request): void
    {
        $this->iliasUiServices->mainTemplate()->setContent(Responses\Form::new($request->parentIliasGui, $request->assignmentProcessId)->getHtml());
    }

    public function handleFormSubmission(Requests\HandleFormSubmission $request): void
    {
        $form = Responses\Form::new($request->parentIliasGui, $request->assignmentProcessId);
        $form->setValuesByPost();

        //todo checkInput; saveObject to $service
        if ($form->saveObject()) {
            $request->onSuccess();
        }
        $this->iliasUiServices->mainTemplate()->setContent($form->getHTML());
    }
}