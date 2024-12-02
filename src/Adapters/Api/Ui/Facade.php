<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\Ui;

use srag\Plugins\UserDefaults\Adapters\Ui\InputElements\FluxEcoSearchInputElement\InputElement;
use srag\Plugins\UserDefaults\Adapters\Config\Configs;

class Facade
{
    private function __construct(private readonly Configs $config)
    {
    }

    public static function new(Configs $config): Facade
    {
        return new self($config);
    }

    public function searchInputElement(string $title, string $postvar, string $dataSrc): InputElement
    {
        return InputElement::new(
            $title,
            $postvar,
            $this->config->adaptersWebPath . "/Ui/InputElements/FluxEcoSearchInputElement/templates",
            $dataSrc
        );
    }
}
