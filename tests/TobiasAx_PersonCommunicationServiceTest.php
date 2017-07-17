<?php

namespace Craft;

use PHPUnit_Framework_MockObject_MockObject as Mock;

/**
 * Class TobiasAx_PersonCommunicationServiceTest.
 *
 * @coversDefaultClass Craft\TobiasAx_PersonCommunicationService
 * @covers ::<!public>
 */
class TobiasAx_PersonCommunicationServiceTest extends TobiasAx_AbstractTest
{
    /**
     * @var TobiasAx_PersonCommunicationService
     */
    private $service;

    /**
     * Sets up the service.
     */
    public function setUp()
    {
        $this->service = new TobiasAx_PersonCommunicationService();

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

        $communication = new TobiasAx_CommunicationModel();
        $communication->Secret = true;
        $communication->Value = "0123456789";
        $communication->Type = 'phone';

        $personConnectorService->expects($this->any())->method('extractSingle')->willReturn($communication);

        return $personConnectorService;
    }

    /**
     * Tests the creation of multiple communications
     */
    public function testCreateCommunications()
    {

        $communications = [];

        $communication = new TobiasAx_CommunicationModel();
        $communication->Secret = true;
        $communication->Value = "0123456789";
        $communication->Type = 'phone';

        $personId = 12345;
        $communications[0] = $communication;
        $communications[1] = $communication;
        $communications[2] = $communication;

        $createdCommunications = $this->service->upsertPersonCommunications($communications, $personId);

        $this->assertNotNull($createdCommunications);
        $this->assertTrue(count($communications) == count($createdCommunications));

        foreach ($createdCommunications as $val) {
            $this->assertEquals(true, $val->Secret);
            $this->assertEquals("0123456789", $val->Value);
            $this->assertEquals("phone", $val->Type);
        }
    }

    /**
     * Tests the creation of a single communication
     */
    public function testCreateCommunication()
    {
        $personId = 12345;

        $communication = new TobiasAx_CommunicationModel();
        $communication->Secret = true;
        $communication->Value = "0123456789";
        $communication->Type = 'phone';

        $createdCommunication = $this->service->CreatePersonCommunication($communication, $personId);

        $this->assertNotNull($createdCommunication);
        $this->assertEquals(true, $createdCommunication->Secret);
        $this->assertEquals("0123456789", $createdCommunication->Value);
        $this->assertEquals("phone", $createdCommunication->Type);
    }
}
