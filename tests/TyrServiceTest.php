<?php

namespace CanalTP\TyrComponent\Tests;

use CanalTP\TyrComponent\TyrService;

class TyrServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TyrService
     */
    private $tyrService;

    public function __construct()
    {
        $this->tyrService = new TyrService('http://tyr.dev.canaltp.fr/v0/', 2, 'sncf');
    }
}
