<?php
namespace Es\NetsEasy\Tests\Unit;

 class OrderControllerTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    
    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testValidation()
    {
        $user = true;
        $this->assertTrue($user);
    }
}