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
    $matches    = array();

    if (preg_match('/^([^(]+)(\(.*\))(\..*?)$/', $parameters['file_name'], $matches))
    {
      $parameters['file_name'] = $matches[1] . $matches[3];

      preg_match_all('/\((.*?):(.*?)\)/', $matches[2], $matches);

      $parameters = array_merge($parameters, $this->matchParameters($matches[1], $matches[2]));
    }

    return $parameters;
  }

  /**
   * @param array $names 
   * @param array $values 
   * @return array
   */
  private function matchParameters($names, $values)
  {
    $parameterMap = array('w' => 'width', 'h' => 'height', 's' => 'scale', 'i' => 'inflate', 'p' => 'path');
    $parameters   = array();

    foreach ($names as $i => $key) 
    {
      if (!array_key_exists($key, $parameterMap))
      {
        throw new InvalidArgumentException(sprintf('Invalid formatting paramter: "%s"', $key));
      }

      $name        = $parameterMap[$key];
      $cleanMethod = sprintf('clean%s', ucfirst($name));

      $parameters[$name] = $this->$cleanMethod($values[$i]);
    }

    return $parameters;
  }

  /**
   * @param string $width 
   * @return int
   */
  protected function cleanWidth($width)
  {
    return (int) $width;
  }

  /**
   * @param string $height
   * @return int
   */
  protected function cleanHeight($height)
  {
    return (int) $height;
  }

  /**
   * @param string $scale 
   * @return boolean
   */
  protected function cleanScale($scale)
  {
    return $scale == 1 ? true : false;
  }

  /**
   * @param string $inflate 
   * @return boolean
   */
  protected function cleanInflate($inflate)
  {
    return $inflate == 1 ? true : false;
  }

  /**
   * @param string $path 
   * @return string
   */
  protected function cleanPath($path)
  {
    return implode(DIRECTORY_SEPARATOR, explode(',', $path));
  }
}

