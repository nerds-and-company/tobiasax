<?php

namespace Craft;

use PHPUnit_Framework_MockObject_MockObject as Mock;

/**
 * Class TobiasAx_RequestServiceTest.
 *
 * @coversDefaultClass Craft\TobiasAx_RequestService
 * @covers ::<!public>
 */
class TobiasAx_RequestServiceTest extends TobiasAx_AbstractTest
{
    /**
     * @var TobiasAx_RequestService
     */
    private $service;

    /**
     * Sets up the service.
     */
    public function setUp()
    {
        $this->service = new TobiasAx_RequestService();
    }

    /**
     * Test render envelope
     *
     * @covers ::renderEnvelope
     */
    public function testRenderEnvelope()
    {
        $envelope = $this->service->renderEnvelope('tobiasax/templates/soap/attribute/get_attributes', [
            'endpoint' => 'http://endpoint',
            'companyId' => '123x',
            'actionName' => 'GetAttributes',
            'addressingEndpoint' => 'http://endpoint',

            'id' => '123x',
            'objectType' => 'Building',
            'category' => 'Verhuur',
        ]);

        $this->assertStringStartsWith('<soap:Envelope', $envelope);
        $this->assertStringEndsWith('</soap:Envelope>
', $envelope);
    }
}
