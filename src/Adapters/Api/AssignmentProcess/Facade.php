<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\AssignmentProcess;

use srag\Plugins\UserDefaults\Adapters\Ui;
use srag\Plugins\UserDefaults\Domain\Ports\AssignmentProcessService;

class Facade
{
    private function __construct(private AssignmentProcessService $service)
    {

    }

    public static function new(AssignmentProcessService $service): Facade
    {
        return new self($service);
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
     * @return string
     */
    public function getTableHtml(GetTableHtmlRequest $request): string
    {
        return Ui\AssignmentProcess\Table::new($request->parentIliasGui)->getHtml();
    }

    /**
     * @return string
     */
    public function getFormHtml(GetFormHtmlRequest $request): string
    {
        return Ui\AssignmentProcess\Form::new($request->parentIliasGui, $request->assignmentProcessId)->getHtml();
    }

    public function handleFormSubmission(HandleFormSubmissionRequest $request): string
    {
        $ilUserSettingsFormGUI = Ui\AssignmentProcess\Form::new($request->parentIliasGui, $request->assignmentProcessId);
        $ilUserSettingsFormGUI->setValuesByPost();

        //todo checkInput; saveObject to $service
        if ($ilUserSettingsFormGUI->saveObject()) {
            $request->onSuccess();
        }
        return $ilUserSettingsFormGUI->getHTML();
    }
}