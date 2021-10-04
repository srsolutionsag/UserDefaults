<?php

namespace srag\CustomInputGUIs\UserDefaults;

/**
 * Trait CustomInputGUIsTrait
 *
 * @package srag\CustomInputGUIs\UserDefaults
 */
trait CustomInputGUIsTrait
{

    /**
     * @return CustomInputGUIs
     */
    protected static final function customInputGUIs() : CustomInputGUIs
    {
        return CustomInputGUIs::getInstance();
    }
}
