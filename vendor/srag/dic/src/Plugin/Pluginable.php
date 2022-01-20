<?php

namespace srag\DIC\UserDefaults\Plugin;

/**
 * Interface Pluginable
 *
 * @package srag\DIC\UserDefaults\Plugin
 */
interface Pluginable
{

    /**
     * @return PluginInterface
     */
    public function getPlugin() : PluginInterface;


    /**
     * @param PluginInterface $plugin
     *
     * @return static
     */
    public function withPlugin(PluginInterface $plugin)/*: static*/ ;
}
