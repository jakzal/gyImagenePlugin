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
class gyImageneFileRoute extends sfRoute
{
  /**
   * @see    sfRoute
   * @param  string  $url     The URL
   * @param  array   $context The context
   * @return array   An array of parameters
   */
  public function matchesUrl($url, $context = array())
  {
    $parameters = parent::matchesUrl($url, $context);

    $matches = array();
    if (preg_match('/^([^(]+)(\(.*\))(\..*?)$/', $parameters['file_name'], $matches))
    {
      $parameters['file_name'] = $matches[1] . $matches[3];
      preg_match_all('/\((.*?):(.*?)\)/', $matches[2], $matches);

      foreach ($matches[1] as $i => $key) 
      {
        if ('w' == $key)
        {
          $parameters['width'] = (int) $matches[2][$i];
        }
        elseif ('h' == $key)
        {
          $parameters['height'] = (int) $matches[2][$i];
        }
        else
        {
          throw new InvalidArgumentException(sprintf('Invalid formatting paramter: "%s"', $key));
        }
      }
    }

    return $parameters;
  }
}

