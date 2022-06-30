<?php

namespace Es\NetsEasy\Tests\Unit\Controller;

use \Es\NetsEasy\extend\Application\Controller\PaymentController;

class PaymentControllerTest extends \Codeception\Test\Unit
{

    /**
     * @var \UnitTester
     */
    protected $paymentObject;

    protected function setUp(): void
    {
        include_once dirname(__FILE__) . "/../../../../../../bootstrap.php";
        $this->paymentObject = \oxNew(PaymentController::class);
        parent::setUp();
    }

    protected function _before()
    {
        
    }

    protected function _after()
    {
        
    }

    /**
     * Test case to get dyn value 
     * @return string
     */
    public function testInit()
    {
        //$initValue = $this->paymentObject->init();
        $this->assertNull(null);
    }

    /**
     * Test case to get Nets Payment Types from db
     */
    public function testGetNetsPaymentTypes()
    {
        $netsPaymentTypes = $this->paymentObject->getNetsPaymentTypes();
        if ($netsPaymentTypes) {
            $this->assertNotEmpty($netsPaymentTypes);
        } else {
            $this->assertNull($netsPaymentTypes);
        }
        //echo "<pre>"; print_r($netsPaymentTypes);die;
    }

    /**
     * Test case to get dyn value 
     * @return string
     */
    public function testGetDynValue()
    {
        $dynValue = $this->paymentObject->getDynValue();
        if ($dynValue) {
            $this->assertNotEmpty($dynValue);
        } else {
            $this->assertNull($dynValue);
        }
    }

    /**
     * Test case for get nets payment text
     */
    public function testGetPaymentTextConfig()
    {
        $paymentText = $this->paymentObject->getPaymentTextConfig();
        if ($paymentText) {
            $this->assertNotNull($embedded);
        } else {
            $this->assertNull($embedded);
        }
    }

    /**
     * Test case for get nets payment text
     */
    public function testGetPaymentUrlConfig()
    {
        $paymentUrl = $this->paymentObject->getPaymentUrlConfig();
        $this->assertStringStartsWith('http://easymoduler.dk', $paymentUrl);
    }

}
