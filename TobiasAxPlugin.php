<?php

namespace Craft;

/**
 * Class TobiasAxPlugin
 */
class TobiasAxPlugin extends BasePlugin
{
    /**
     * @return null|string
     */
    public function getName()
    {
        return Craft::t('Tobias AX');
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return '1.0.1';
    }

    /**
     * @return string
     */
    public function getDeveloper()
    {
        return 'Nerds and Company';
    }

    /**
     * @return string
     */
    public function getDeveloperUrl()
    {
        return 'https://www.nerds.company';
    }

    /**
     * @return boolean
     */
    public function hasCpSection()
    {
        return true;
    }

    /**
     * TobiasAX plugin init
     * @return void
     */
    public function init()
    {
        // autoloads custom exceptions and filters (Because Craft won't do it)
        $this->autoloadExceptions();
        $this->autoloadFilters();

        // Import additional classes
        Craft::import('plugins.tobiasax.interfaces.*');
        Craft::import('plugins.tobiasax.etc.sanitizers.*');

        // allow plugins to register custom element type
        craft()->tobiasAx_advert->registerPluginElementType();

        // Check to see if the environment settings for Tobias AX are actually loaded.
        if (!craft()->config->get('tobiasAxUsername') || !craft()->config->get('tobiasAxPassword')) {
            throw new TobiasAx_SystemException("Required Tobias AX config parameters are not set!");
        }
    }

    /**
     * Registeres control panel routes
     * @return array
     */
    public function registerCpRoutes()
    {
        return array(
            'tobiasax'                                             => array('action' => 'tobiasAx/advertisementIndex'),
            'tobiasax/(?P<typeHandle>{handle})/new'                => array('action' => 'tobiasAx/editElement'),
            'tobiasax/(?P<typeHandle>{handle})/(?P<elementId>\d+)' => array('action' => 'tobiasAx/editElement'),
        );
    }

    /**
     * Loads exception classes
     * @return void
     */
    public function autoloadExceptions()
    {
        $exceptions = glob(CRAFT_PLUGINS_PATH . 'tobiasax/etc/errors/*');

        foreach ($exceptions as $exception) {
            require_once $exception;
        }
    }

    /**
     * Loads filter classes
     * @return void
     */
    public function autoloadFilters()
    {
        $filters = glob(CRAFT_PLUGINS_PATH . 'tobiasax/etc/filters/*');

        foreach ($filters as $filter) {
            require_once $filter;
        }
    }

    /**
     * @param string $msg
     * @param LogLevel $level
     * @param boolean $force
     * @param string  $category
     */
    public static function log($msg, $level = LogLevel::Info, $force = false, $category = 'application')
    {
        Craft::log($msg, $level, $force, $category, 'tobiasax');
    }

    /**
     * Register custom Craft permissions
     * @return array
     */
    public function registerUserPermissions()
    {
        return craft()->tobiasAx_advertType->getPermissions();
    }

    /**
     * Registers schematic migration services.
     *
     * @return array
     */
    public function registerMigrationService()
    {
        return array(
            'tobiasax' => craft()->tobiasAx_migrations,
        );
    }
}
