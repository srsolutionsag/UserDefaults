<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\Ui;

use srag\Plugins\UserDefaults\Adapters\Ui\InputElements\FluxEcoSearchInputElement\InputElement;
use srag\Plugins\UserDefaults\Adapters\Config\Configs;

class Api
{
    private function __construct(private readonly Facade $facade)
    {
    }

    public static function new(Configs $configs): Api
    {
        return new self(Facade::new($configs));
    }

    public function searchInputElementHtml(string $title, string $postvar, string $dataSrc): InputElement
    {
        return $this->facade->searchInputElement($title, $postvar, $dataSrc);
    }
}
