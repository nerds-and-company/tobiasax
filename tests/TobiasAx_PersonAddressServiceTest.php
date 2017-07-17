<?php

namespace Craft;

use PHPUnit_Framework_MockObject_MockObject as Mock;

/**
 * Class TobiasAx_PersonAddressServiceTest.
 *
 * @coversDefaultClass Craft\TobiasAx_PersonAddressService
 * @covers ::<!public>
 */
class TobiasAx_PersonAddressServiceTest extends TobiasAx_AbstractTest
{
    /**
     * @var TobiasAx_PersonAddressService
     */
    private $service;

    /**
     * Sets up the service.
     */
    public function setUp()
    {
        $this->service = new TobiasAx_PersonAddressService();

        $this->getMockTobiasAxPersonConnectorService();
    }

    /**
     * Sets a mock Person Connector Service on the craft() object and returns the mock.
     *
     * @return Mock
     */
    protected function getMockTobiasAxPersonConnectorService()
    {
        $personConnectorService = $this->getMockCraftService(TobiasAx_PersonConnectorService::class, 'tobiasAx_personConnector');

        $address = [];
        $address['Id'] = 12345;
        $address['Street'] = "teststreet";

        $personConnectorService->expects($this->any())->method('extractSingle')->willReturn($address);

        return $personConnectorService;
    }

    /**
     * Tests the creation of multiple addresses
     */
    public function testCreateAddresses()
    {
        $addresses = [];
        $address = new TobiasAx_AddressModel();
        $address->Street = 'teststreet';
        $personId = 12345;
        $addresses[0] = $address;
        $addresses[1] = $address;
        $addresses[2] = $address;

        $createdAddresses = $this->service->CreatePersonAddresses($addresses, $personId);

        $this->assertNotNull($createdAddresses);
        $this->assertTrue(count($addresses) == count($createdAddresses));

        foreach ($createdAddresses as $val) {
            $this->assertEquals($address->Street, $val->Street);
            $this->assertNotNull($val->Id);
        }
    }

    /**
     * Tests the creation of a single address
     */
    public function testCreateAddress()
    {
        $address = new TobiasAx_AddressModel();
        $address->Street = 'teststreet';
        $personId = 12345;

        $createdAddress = $this->service->CreatePersonAddress($address, $personId);

        $this->assertNotNull($createdAddress);
        $this->assertEquals($address->Street, $createdAddress->Street);
        $this->assertNotNull($createdAddress->Id);
    }
}
