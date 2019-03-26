<?php

namespace plugins\NovaPoshta\classes;

use plugins\NovaPoshta\classes\base\Base;
use plugins\NovaPoshta\classes\repository\AreaRepositoryFactory;

/**
 * Class AjaxRoute
 * @package plugins\NovaPoshta\classes
 */
class AjaxRoute extends Base
{

    const GET_CITIES_ROUTE = 'nova_poshta_get_cities_by_area';
    const GET_WAREHOUSES_ROUTE = 'nova_poshta_get_warehouses_by_city';

    const GET_REGIONS_BY_NAME_SUGGESTION = 'get_regions_by_name_suggestion';
    const GET_CITIES_BY_NAME_SUGGESTION = 'get_cities_by_suggestion';
    const GET_WAREHOUSES_BY_NAME_SUGGESTION = 'get_warehouses_by_suggestion';
    const MARK_PLUGIN_AS_RATED = 'woo_shipping_with_nova_poshta_rated';

    /**
     * @var string
     */
    private $route;

    /**
     * @var array
     */
    private $handler;

    /**
     * @return void
     */
    public static function init()
    {
        foreach (self::getHandlers() as $key => $handler) {
            $ajaxRoute = new self($key, $handler);
            $ajaxRoute->handleRequest();
        }
        foreach (self::getAdminHandlers() as $key => $handler) {
            add_action('wp_ajax_' . $key, $handler);
        }
    }

    /**
     * @return array
     */
    public static function getHandlers()
    {
        $factory = AreaRepositoryFactory::instance();
        return array(
            self::GET_CITIES_ROUTE => array($factory->cityRepo(), 'ajaxGetAreasByNameSuggestion'),
            self::GET_WAREHOUSES_ROUTE => array($factory->warehouseRepo(), 'ajaxGetAreasByNameSuggestion'),
        );
    }

    /**
     * @return array
     */
    public static function getAdminHandlers()
    {
        $factory = AreaRepositoryFactory::instance();
        return array(
            self::MARK_PLUGIN_AS_RATED => array(NP()->options, 'ajaxPluginRate'),
            self::GET_REGIONS_BY_NAME_SUGGESTION => array($factory->regionRepo(), 'ajaxGetAreasByNameSuggestion'),
            self::GET_CITIES_BY_NAME_SUGGESTION => array($factory->cityRepo(), 'ajaxGetAreasByNameSuggestion'),
            self::GET_WAREHOUSES_BY_NAME_SUGGESTION => array($factory->warehouseRepo(), 'ajaxGetAreasByNameSuggestion'),
        );
    }

    /**
     * AjaxRoute constructor.
     * @param string $route
     * @param string $handler
     */
    public function __construct($route, $handler)
    {
        $this->route = $route;
        $this->handler = $handler;
    }

    /**
     * Handle ajax request
     */
    public function handleRequest()
    {
        add_action('wp_ajax_' . $this->route, $this->handler);
        add_action('wp_nopriv_ajax_' . $this->route, $this->handler);

        if (isset($_REQUEST['action']) && $_REQUEST['action'] == $this->route) {
            do_action('wp_ajax_' . $_REQUEST['action']);
            do_action('wp_ajax_nopriv_' . $_REQUEST['action']);
        }
    }
}