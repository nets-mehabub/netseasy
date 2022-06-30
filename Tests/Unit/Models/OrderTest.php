<?php

namespace Es\NetsEasy\Tests\Unit\Models;

use OxidEsales\Eshop\Application\Model\Basket;
use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\Eshop\Application\Model\User;
use \Es\NetsEasy\extend\Application\Models\Order as NetsOrder;
use Es\NetsEasy\Api\NetsLog;
use Es\NetsEasy\Core\CommonHelper;

class OrderTest extends \Codeception\Test\Unit {

    /**
     * @var \UnitTester
     */
    protected $orderObject;

    const ENDPOINT_TEST = 'https://test.api.dibspayment.eu/v1/payments/';
    const ENDPOINT_LIVE = 'https://api.dibspayment.eu/v1/payments/';

    protected function setUp(): void {
        parent::setUp();
        include_once dirname(__FILE__) . "/../../../../../../bootstrap.php";
        $this->orderObject = \oxNew(NetsOrder::class);
    }

    protected function _before() {
        
    }

    protected function _after() {
        
    }

    /**
     * Test case to get product item
     */
    public function testGetProductItem() {
        $oBasket = $this->getBasket();
        $basketcontents = $oBasket->getContents();
        foreach ($basketcontents as $item) {
            $items[] = $itemArray = $this->orderObject->getProductItem($item);
        }
        $this->assertArrayHasKey('reference', $items[0]);
        $this->assertNotEmpty($items[0]);
    }

    /**
     * Test case to set language
     */
    public function testSetLanguage() {
        $oBasket = $this->getBasket();
        $oUser = \oxNew("oxuser", "core");
        $oUser->Load($sUserID);
        $result = $this->orderObject->setLanguage($oUser, $sTranslation, $oBasket);
        $this->assertArrayHasKey('language', $result);
        $this->assertNotEmpty($result['checkout_type']);
        $this->assertEquals('embedded', $result['checkout_type']);
    }

    /**
     * Test case to get payment response
     */
    public function testGetPaymentResponse() {
        $modus = \oxRegistry::getConfig()->getConfigParam('nets_blMode');
        if ($modus == 0) {
            $apiUrl = self::ENDPOINT_TEST;
        } else {
            $apiUrl = self::ENDPOINT_LIVE;
        }
        $datastring = $this->getDatastring();
        $api_return = CommonHelper::getCurlResponse($apiUrl, 'POST', $datastring);
        $response = json_decode($api_return, true);
        $this->assertNotEmpty($response['paymentId']);
    }

    /**
     * Test case to get dDelivery address array
     */
    public function testGetDeliveryAddress() {
        $oMockOrder = $this->getMockBuilder(Order::class)->setMethods(['Load'])->getMock();
        $mockBuilder = $this->getMockBuilder(\OxidEsales\Eshop\Core\DatabaseProvider::class);
        $mockBuilder->setMethods(['getDb', 'getOne']);
        $mockDB = $mockBuilder->getMock();
        $oMockUser = $this->getMockBuilder(User::class)->setMethods(['Load'])->getMock();
        $oMockUser->expects($this->any())->method('Load')->willReturn(2);
        $result = $this->orderObject->getDeliveryAddress($oMockOrder, $mockDB, $oMockUser);
        $this->assertObjectHasAttribute('firstname', $result);
    }

    /**
     * Test case to get discount item array
     */
    public function testGetDiscountItem() {
        $result = $this->orderObject->getDiscountItem(100, null);
        $this->assertArrayHasKey('grossTotalAmount', $result[0]);
        $this->assertContains(100, $result[0]);
    }

    /**
     * Test case to get current order from basket
     */
    public function testGetDiscountSum() {
        $oBasket = $this->getBasket();
        $result = $this->orderObject->getDiscountSum($oBasket);
        $this->assertEmpty($result);
    }

    /**
     * Test case to log Order ID
     */
    public function testLogOrderID() {
        $responce = NetsLog::log('test log');
        $this->assertTrue($responce);
    }

    /**
     * Test case for OrderController::isEmbedded()
     */
    public function testIsEmbedded() {
        $embedded = $this->orderObject->isEmbedded();
        if ($embedded) {
            $this->assertTrue($embedded);
        } else {
            $this->assertFalse($embedded);
        }
    }

    /**
     * Function set basket item
     * @return object
     */
    public function setUpBasket($oBasket, $productsIds) {
        $oBasket->setDiscountCalcMode(true);
        \oxRegistry::getConfig()->getConfigParam('blAllowUnevenAmounts', true);
        foreach ($productsIds as $id) {
            $oBasket->addToBasket($id, 1);
        }
        $oBasket->calculateBasket(true);
        //basket name in session will be "basket"
        \oxRegistry::getConfig()->setConfigParam('blMallSharedBasket', 1);
        return $oBasket;
    }

    /**
     * Function get basket item
     * @return object
     */
    public function getBasket() {
        $orderLines = $this->getOrderLinesData(1);
        $ids = ['ed6573c0259d6a6fb641d106dcb2faec'];
        /** @var Basket|KlarnaBasket $oBasket */
        $oBasket = oxNew(Basket::class);
        return $this->setUpBasket($oBasket, $ids);
    }

    /**
     * Function to Get Order Lines Data
     * @return array
     */
    protected function getOrderLinesData($anonOn = 0, $wrapping = 0) {
        $homeUrl = \oxRegistry::getConfig()->getConfigParam('sShopURL');
        $lines = [
            'order_lines' => [
                [
                    'type' => 'physical',
                    'reference' => ($anonOn ? '7b1ce3d73b70f1a7246e7b76a35fb552' : '2103'),
                    'quantity' => 1,
                    'unit_price' => 32900,
                    'tax_rate' => 1900,
                    'total_amount' => 32900,
                    'total_tax_amount' => 5253,
                    'quantity_unit' => 'pcs',
                    'name' => ($anonOn ? 'Produktname 1' : 'Wakeboard LIQUID FORCE GROOVE 2010'),
                    'product_url' => $homeUrl . 'index.php',
                    'image_url' => $homeUrl . 'out/pictures/generated/product/1/540_340_75/lf_groove_2010_1.jpg',
                    'product_identifiers' => [
                        'category_path' => '',
                        'global_trade_item_number' => '',
                        'manufacturer_part_number' => '',
                        'brand' => '',
                    ],
                ],
                [
                    'type' => 'shipping_fee',
                    'reference' => 'SRV_DELIVERY',
                    'name' => 'Standard',
                    'quantity' => 1,
                    'total_amount' => 0,
                    'total_discount_amount' => 0,
                    'total_tax_amount' => 0,
                    'unit_price' => 0,
                    'tax_rate' => 0,
                ],
            ],
            'order_amount' => 32900,
            'order_tax_amount' => 5253,
        ];
        if ($anonOn) {
            unset($lines['order_lines'][0]['product_url']);
            unset($lines['order_lines'][0]['image_url']);
            unset($lines['order_lines'][0]['product_identifiers']);
        }
        if ($wrapping) {
            //$lines['order_lines'];
        }
        return $lines;
    }

    /**
     * Function to get data string response
     * @return array
     */
    public function getDatastring() {
        return $datastring = '{
                        "order":{
                           "items":[
                              {
                                 "reference":"demo_6",
                                 "name":"The best is yet to come Framed poster",
                                 "quantity":2,
                                 "unit":"pcs",
                                 "unitPrice":2900,
                                 "taxRate":2500,
                                 "taxAmount":1450,
                                 "grossTotalAmount":7250,
                                 "netTotalAmount":5800
                              }
                           ],
                           "amount":7250,
                           "currency":"DKK",
                           "reference":"ps_iCbEuzIsdVcW"
                        },
                        "checkout":{
                           "charge":"false",
                           "publicDevice":"false",
                           "integrationType":"HostedPaymentPage",
                           "returnUrl":"http:\/\/localhost:8081\/en\/module\/netseasy\/return?id_cart=23",
                           "cancelUrl":"http:\/\/localhost:8081\/en\/order",
                           "termsUrl":"http:\/\/localhost:8081\/en\/",
                           "merchantTermsUrl":"http:\/\/localhost:8081\/en\/",
                           "merchantHandlesConsumerData":true,
                           "consumerType":{
                              "default":"B2C",
                              "supportedTypes":[
                                 "B2C"
                              ]
                           }
                        },
                        "customer":{
                           "email":"test@shop.com",
                           "shippingAddress":{
                              "addressLine1":"1234567890",
                              "addressLine2":"",
                              "postalCode":"1234",
                              "city":"Test",
                              "country":"DK"
                           },
                           "company":{
                              "name":"test",
                              "contact":{
                                 "firstName":"TEst",
                                 "lastName":"TEst"
                              }
                           },
                           "phoneNumber":{
                              "prefix":"+45",
                              "number":"12345678"
                           }
                        }
                     }';
    }

}
