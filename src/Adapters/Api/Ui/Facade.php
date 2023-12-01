<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\Ui;

use srag\Plugins\UserDefaults\Adapters\Config\Configs;
use srag\Plugins\UserDefaults\Adapters\Ui;

class Facade
{
    private function __construct(private Configs $config)
    {

    }

    public static function new(Configs $config): Facade
    {
        return new self($config);
    }


    public function searchInputElement(string $title, string $postvar, string $dataSrc): Ui\InputElements\FluxEcoSearchInputElement\InputElement
    {
        return Ui\InputElements\FluxEcoSearchInputElement\InputElement::new($title, $postvar,  $this->config->adaptersWebPath."/Ui/InputElements/FluxEcoSearchInputElement/templates", $dataSrc);
    }
}