<?php
/*
 * (c) 2007-2010 Jakub Zalas
 * (c) 2007-2010 GOYELLO IT Services 
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @package     gyImagenePlugin
 * @subpackage  config
 * @author      Jakub Zalas <jakub@zalas.pl>
 */
class gyImagenePluginConfiguration extends sfPluginConfiguration
{
  /**
   * @see sfPluginConfiguration
   */
  public function initialize()
  {
    if (sfConfig::get('app_gy_imagene_plugin_routes_register', true) && in_array('gyImagene', sfConfig::get('sf_enabled_modules', array())))
    {
      $this->dispatcher->connect('routing.load_configuration', array('gyImageneRouting', 'listenToRoutingLoadConfigurationEvent'));
    }
  }
}

