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
 * @subpackage  lib.routing
 * @author      Jakub Zalas <jakub@zalas.pl>
 */
class gyImageneRouting
{
  /**
   * Listens to the routing.load_configuration event.
   *
   * @param sfEvent An sfEvent instance
   */
  static public function listenToRoutingLoadConfigurationEvent(sfEvent $event)
  {
    $routing = $event->getSubject();
    $routing->prependRoute(
      'gy_imagene_show', 
      new gyImageneFileRoute(
        '/imagene/:file_name', 
        array('module' => 'gyImagene', 'action' => 'show'),
        array('file_name' => '.*\.(jpg|jpeg|png|gif|JPG|PNG|GIF|JPEG)')
      )
    );
  }
}

