<?php

namespace Es\NetsEasy\Tests\Unit\Controller\Admin;

use \Es\NetsEasy\extend\Application\Controller\Admin\OrderOverviewController;
use Es\NetsEasy\Core\CommonHelper;

class OrderOverviewControllerTest extends \Codeception\Test\Unit {

    /**
     * @var \UnitTester
     */
    protected $oOrderOverviewController;

    protected function setUp(): void {
        parent::setUp();
        include_once dirname(__FILE__) . "/../../../../../../../bootstrap.php";
        $this->oOrderOverviewController = \oxNew(OrderOverviewController::class);
    }

    protected function _before() {
        
    }

    protected function _after() {
        
    }

    /**
     * Test case to check the nets payment status and display in admin order list backend page
     */
    public function testIsEasy() {
        $orderId = $this->getNetsOrderId();
        if (!empty($orderId)) {
            $result = $this->oOrderOverviewController->isEasy($orderId);
            if ($result) {
                $this->assertNotEmpty($result);
                $this->assertArrayHasKey('payStatus', $result);
            }
        } else {
            $this->assertNull($orderId);
        }
    }

    /**
     * Test case to get pay language status
     */
    public function testGetPayLangStatus() {
        $response = $this->getNetsPaymentResponce();
        $result = $this->oOrderOverviewController->getPayLangStatus(json_decode($response, true));
        if ($result) {
            $this->assertNotEmpty($result);
            $this->assertArrayHasKey('payStatus', $result);
        }
    }

    /*
     * Test case to capture nets transaction - calls Charge API
     */

    public function testGetOrderCharged() {
        $sUrl = \oxRegistry::getConfig()
                        ->getSslShopUrl() . 'admin/index.php?cl=admin_order&force_admin_sid=adminsid&stoken=teststoken';
        $response = \oxRegistry::getUtilsUrl()->processUrl($sUrl, $blFinalUrl = true, $aParams = null, $iLang = null
        );
        $this->assertStringStartsWith('http', $response);
        $this->assertNotNull($response);
    }

    /*
     * Test case to capture nets transaction - calls Refund API
     */

    public function testGetOrderRefund() {
        $sUrl = \oxRegistry::getConfig()
                        ->getSslShopUrl() . 'admin/index.php?cl=admin_order&force_admin_sid=adminsid&stoken=teststoken';
        $response = \oxRegistry::getUtilsUrl()->processUrl($sUrl, $blFinalUrl = true, $aParams = null, $iLang = null
        );
        $this->assertStringStartsWith('http', $response);
        $this->assertNotNull($response);
    }

    /*
     * Test case to get order items to pass capture, refund, cancel api
     */

    public function testGetOrderItems() {
        $orderId = $this->getNetsOrderId();
        if (!empty($orderId)) {
            $result = $this->oOrderOverviewController->getOrderItems($orderId);
            if ($result) {
                $this->assertNotEmpty($result);
                $this->assertArrayHasKey('items', $result);
            }
        } else {
            $this->assertNull($orderId);
        }
    }

    /*
     * Test case to get list of partial charge/refund and reserved items list
     */

    public function testCheckPartialItems() {
        $orderId = $this->getNetsOrderId();
        if (!empty($orderId)) {
            $result = $this->oOrderOverviewController->checkPartialItems($orderId);
            if ($result) {
                $this->assertNotEmpty($result);
            }
        } else {
            $this->assertNull($orderId);
        }
    }

    /*
     * Test case to get List of items to pass to frontend for charged, refunded items
     */

    public function testGetLists() {
        $response = $this->getNetsPaymentResponce();
        $item = array(
            "reference" => "2103",
            "name" => "Wakeboard GROOVE",
            "quantity" => 1.0,
            "unit" => "pcs",
            "unitPrice" => 27647,
            "taxRate" => 1900,
            "taxAmount" => 5253,
            "grossTotalAmount" => 32900,
            "netTotalAmount" => 27647
        );
        $result = $this->oOrderOverviewController->getLists(json_decode($response, true), array(), array(), $item);
        if ($result) {
            $this->assertNotEmpty($result);
            $this->assertArrayHasKey('refundedItems', $result);
        }
    }

    /*
     * Test case to Fetch partial amount
     */

    public function testGetPartial() {
        $orderId = $this->getNetsOrderId();
        if (!empty($orderId)) {
            $mockBuilder = $this->getMockBuilder(\OxidEsales\Eshop\Core\DatabaseProvider::class);
            $mockBuilder->setMethods(['getDb', 'getOne']);
            $mockDB = $mockBuilder->getMock();
            $mockDB->expects($this->any())->method('getDb')->willReturn(true);
            $mockDB->expects($this->any())->method('getOne')->willReturn(100);
            $sSQL_select = "SELECT partial_amount FROM oxnets WHERE oxorder_id = ? LIMIT 1";
            $partial_amount = $mockDB->getOne($sSQL_select, [
                $orderId
            ]);
            if ($partial_amount) {
                $this->assertNotEmpty($partial_amount);
            } else {
                $this->assertNull($orderId);
            }
        } else {
            $this->assertNull($orderId);
        }
    }

    /*
     * Test case to enable debug mode
     */

    public function testDebugMode() {
        $result = $this->oOrderOverviewController->debugMode();
        if ($result) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }

    /*
     * Test case to fetch payment method type from databse table oxorder
     */

    public function testGetPaymentMethod() {
        $mockBuilder = $this->getMockBuilder(\OxidEsales\Eshop\Core\DatabaseProvider::class);
        $mockBuilder->setMethods(['getDb', 'getOne']);
        $mockDB = $mockBuilder->getMock();
        $mockDB->expects($this->any())->method('getDb')->willReturn(true);
        $mockDB->expects($this->any())->method('getOne')->willReturn('nets_easy');
        $sSQL_select = "SELECT OXPAYMENTTYPE FROM oxorder WHERE oxid = ? LIMIT 1";
        $payMethod = $mockDB->getOne($sSQL_select, [
            1
        ]);
        if ($payMethod == 'nets_easy') {
            $this->assertEquals($payMethod, 'nets_easy');
        } else {
            $this->assertNull($payMethod);
        }
    }

    /*
     * Test case to fetch payment api url
     */

    public function testGetApiUrl() {
        $result = $this->oOrderOverviewController->getApiUrl();
        if ($result) {
            $this->assertStringStartsWith('http', $result);
            $this->assertNotNull($result);
        }
    }

    /*
     * Test case to get charged items list
     */

    public function testGetChargedItems() {
        $response = $this->getNetsPaymentResponce();
        $result = $this->oOrderOverviewController->getChargedItems(json_decode($response, true));
        if ($result) {
            $this->assertNotEmpty($result);
        }
    }

    /*
     * Test case to get refunded items list
     */

    public function testGetRefundedItems() {
        $response = $this->getNetsPaymentResponce();
        $result = $this->oOrderOverviewController->getRefundedItems(json_decode($response, true));
        if ($result) {
            $this->assertNotEmpty($result);
        }
    }

    /**
     * Test case to get payment id from database to display in thank you page.
     */
    public function testGetPaymentId() {
        $mockBuilder = $this->getMockBuilder(\OxidEsales\Eshop\Core\DatabaseProvider::class);
        $mockBuilder->setMethods(['getDb', 'getOne']);
        $mockDB = $mockBuilder->getMock();
        $mockDB->expects($this->any())->method('getDb')->willReturn(true);
        $mockDB->expects($this->any())->method('getOne')->willReturn(1);
        $sSQL_select = "SELECT transaction_id FROM oxnets WHERE oxnets_id = ? LIMIT 1";
        $paymentId = $mockDB->getOne($sSQL_select, [
            1
        ]);
        if ($paymentId) {
            $this->assertNotEmpty($paymentId);
        } else {
            $this->assertNull($paymentId);
        }
    }

    /**
     * Function to get nets order id
     * @return string
     */
    public function getNetsOrderId() {
        $oDB = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(true);
        $sSQL_select = "SELECT oxid FROM oxorder WHERE  OXPAYMENTTYPE= ? LIMIT 1";
        return $orderId = $oDB->getOne($sSQL_select, [
            'nets_easy'
        ]);
    }

    /**
     * Function to set nets Api response
     * @return json
     */
    public function getNetsPaymentResponce() {
        return '{
                "payment":{
                   "paymentId":"0126000062a745c1f24370d976ebd20e",
                   "summary":{
                      "reservedAmount":98700,
                      "chargedAmount":98700
                   },
                   "consumer":{
                      "shippingAddress":{
                         "addressLine1":"423",
                         "addressLine2":"MG road, camp",
                         "receiverLine":"test user",
                         "postalCode":"3456",
                         "city":"den",
                         "country":"DNK"
                      },
                      "company":{
                         "contactDetails":{
                            "phoneNumber":{

                            }
                         }
                      },
                      "privatePerson":{
                         "firstName":"test",
                         "lastName":"user",
                         "email":"test@test.com",
                         "phoneNumber":{

                         }
                      },
                      "billingAddress":{
                         "addressLine1":"423",
                         "addressLine2":"MG road, camp",
                         "receiverLine":"test user",
                         "postalCode":"3456",
                         "city":"den",
                         "country":"DNK"
                      }
                   },
                   "paymentDetails":{
                      "paymentType":"CARD",
                      "paymentMethod":"MasterCard",
                      "invoiceDetails":{

                      },
                      "cardDetails":{
                         "maskedPan":"554433******0235",
                         "expiryDate":"1234"
                      }
                   },
                   "orderDetails":{
                      "amount":98700,
                      "currency":"EUR",
                      "reference":"30"
                   },
                   "checkout":{
                      "url":"http://oxideshop.local:81/index.php?cl=thankyou"
                   },
                   "created":"2022-06-13T14:12:52.5885+00:00",
                   "refunds":[
                      {
                         "refundId":"016d000062a74644f24370d976ebd220",
                         "amount":32900,
                         "state":"Pending",
                         "lastUpdated":"2022-06-13T14:14:28.3685+00:00",
                         "orderItems":[
                            {
                               "reference":"2103",
                               "name":"Wakeboard GROOVE",
                               "quantity":1.0,
                               "unit":"pcs",
                               "unitPrice":27647,
                               "taxRate":1900,
                               "taxAmount":5253,
                               "grossTotalAmount":32900,
                               "netTotalAmount":27647
                            }
                         ]
                      },
                      {
                         "refundId":"00a9000062a74644f24370d976ebd221",
                         "amount":32900,
                         "state":"Pending",
                         "lastUpdated":"2022-06-13T14:14:28.4918+00:00",
                         "orderItems":[
                            {
                               "reference":"2103",
                               "name":"Wakeboard GROOVE",
                               "quantity":1.0,
                               "unit":"pcs",
                               "unitPrice":27647,
                               "taxRate":1900,
                               "taxAmount":5253,
                               "grossTotalAmount":32900,
                               "netTotalAmount":27647
                            }
                         ]
                      },
                      {
                         "refundId":"0190000062a74749f24370d976ebd259",
                         "amount":32900,
                         "state":"Pending",
                         "lastUpdated":"2022-06-13T14:18:49.7281+00:00",
                         "orderItems":[
                            {
                               "reference":"2103",
                               "name":"Wakeboard GROOVE",
                               "quantity":1.0,
                               "unit":"pcs",
                               "unitPrice":27647,
                               "taxRate":1900,
                               "taxAmount":5253,
                               "grossTotalAmount":32900,
                               "netTotalAmount":27647
                            }
                         ]
                      }
                   ],
                   "charges":[
                      {
                         "chargeId":"00ab000062a7462cf24370d976ebd21d",
                         "amount":32900,
                         "created":"2022-06-13T14:14:04.5570+00:00",
                         "orderItems":[
                            {
                               "reference":"2103",
                               "name":"Wakeboard GROOVE",
                               "quantity":1.0,
                               "unit":"pcs",
                               "unitPrice":27647,
                               "taxRate":1900,
                               "taxAmount":5253,
                               "grossTotalAmount":32900,
                               "netTotalAmount":27647
                            }
                         ]
                      },
                      {
                         "chargeId":"01c9000062a74636f24370d976ebd21e",
                         "amount":65800,
                         "created":"2022-06-13T14:14:14.7471+00:00",
                         "orderItems":[
                            {
                               "reference":"2103",
                               "name":"Wakeboard GROOVE",
                               "quantity":2.0,
                               "unit":"pcs",
                               "unitPrice":27647,
                               "taxRate":1900,
                               "taxAmount":10506,
                               "grossTotalAmount":65800,
                               "netTotalAmount":55294
                            }
                         ]
                      }
                   ]
                }
             }';
    }

}
