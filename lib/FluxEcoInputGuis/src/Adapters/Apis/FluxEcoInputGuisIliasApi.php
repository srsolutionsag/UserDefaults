<?php

namespace fluxlabs\FluxEcoInputGuis\Adapters\Apis;

use fluxlabs\FluxEcoInputGuis\Adapters\IliasFormProperties\FluxEcoSearchInputGUI;

class FluxEcoInputGuisIliasApi
{
    private string $webPath;

    private function __construct(string $webPath)
    {
        $this->webPath = $webPath;
    }


    public static function new(string $fuxEcoInputGuisWebPath): FluxEcoInputGuisIliasApi
    {
        return new self($fuxEcoInputGuisWebPath);
    }

    public function searchInputGui(string $title, string $postvar, string $dataSrc): FluxEcoSearchInputGUI
    {
        $inputGui = new FluxEcoSearchInputGUI($title, $postvar);
        $inputGui->setDataSrc($dataSrc);
        $inputGui->setTemplatesPath($this->webPath."/templates");
        return $inputGui;
    }
}