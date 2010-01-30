<?php

include(dirname(__FILE__) . '/../../../../test/bootstrap/functional.php');

$browser = new sfTestFunctional(new sfBrowser());

$browser->
  get('/gyImagene/index')->

  with('request')->begin()->
    isParameter('module', 'gyImagene')->
    isParameter('action', 'index')->
  end()->

  with('response')->begin()->
    isStatusCode(200)->
  end()
;
