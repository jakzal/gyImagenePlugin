<?php 
/*
 * (c) 2007-2010 Jakub Zalas
 * (c) 2007-2010 GOYELLO IT Services 
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

include(dirname(__FILE__) . '/../../../../test/bootstrap/unit.php');

/**
 * @package     gyImagenePlugin
 * @subpackage  test.unit
 * @author      Jakub Zalas <jakub@zalas.pl>
 */
class gyImageneFileRouteTest extends gyLimeTest
{
  public function testGenerateGeneratesUrlWithGivenParameters()
  {
    $route = new gyImageneFileRoute('/imagene/:file_name', array(), array());

    $this->is($route->generate(array('file_name' => 'logo-goyello-160x80.png')), '/imagene/logo-goyello-160x80.png', '->generate() generates an URL with the given parameters');
    $this->todo('Generating with parameters');
  }

  public function testMatchesParameters()
  {
    $route = new gyImageneFileRoute('/imagene/:file_name', array(), array());

    $this->ok($route->matchesParameters(array('file_name' => 'logo-goyello-160x80.png', 'sf_method' => 'GET')), '->matchesParameters() matches when file name is used alone');
    $this->ok($route->matchesParameters(array('file_name' => 'logo-goyello-160x80(w:20)(h:30).png', 'sf_method' => 'GET')), '->matchesParameters() removes parameters from file name');
  }

  public function testMatchesUrl()
  {
    $route = new gyImageneFileRoute('/imagene/:file_name', array(), array('file_name' => '.*\.(jpg|jpeg|png|gif|JPG|PNG|GIF|JPEG)'));

    $parameters = $route->matchesUrl('/imagene/logo-goyello-160x80.png', array('sf_method' => 'GET'));
    $this->cmp_ok($parameters, '===', array('file_name' => 'logo-goyello-160x80.png'), '->matchesUrl() matches when file name is used alone');

    $parameters = $route->matchesUrl('/imagene/logo-goyello-160x80(w:20)(h:30).png', array('sf_method' => 'GET'));
    $this->cmp_ok($parameters, '===', array('file_name' => 'logo-goyello-160x80.png', 'width' => 20, 'height' => 30), '->matchesUrl() extracts parameters from file name');
  }

  public function testMatchesUrlThrowsExceptionForInvalidFormatter()
  {
    $route = new gyImageneFileRoute('/imagene/:file_name', array(), array('file_name' => '.*\.(jpg|jpeg|png|gif|JPG|PNG|GIF|JPEG)'));

    try
    {
      $parameters = $route->matchesUrl('/imagene/logo-goyello-160x80(w:20)(h:30)(a:123).png', array('sf_method' => 'GET'));

      $this->fail('->matchesUrl() throws an exception for invalid formatting parameter');
    }
    catch (InvalidArgumentException $exception)
    {
      $this->pass('->matchesUrl() throws an exception for invalid formatting parameter');
    }
  }
}

$test = new gyImageneFileRouteTest(7);
$test->run();

