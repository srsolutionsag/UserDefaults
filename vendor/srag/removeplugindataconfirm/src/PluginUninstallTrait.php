<?php

namespace srag\RemovePluginDataConfirm\UserDefaults;

/**
 * Trait PluginUninstallTrait
 *
 * @package srag\RemovePluginDataConfirm\UserDefaults
 */
trait PluginUninstallTrait
{

    use BasePluginUninstallTrait;

    /**
     * @internal
     */
    protected final function afterUninstall() : void
    {

    }


    /**
     * @return bool
     *
     * @internal
     */
    protected final function beforeUninstall() : bool
    {
        return $this->pluginUninstall();
    }
}
