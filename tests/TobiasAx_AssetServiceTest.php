<?php

namespace Craft;

use PHPUnit_Framework_MockObject_MockObject as Mock;
use Exception;

/**
 * Class TobiasAx_AssetServiceTest.
 *
 * @coversDefaultClass Craft\TobiasAx_AssetService
 * @covers ::<!public>
 */
class TobiasAx_AssetServiceTest extends TobiasAx_AbstractTest
{
    /**
     * @var TobiasAx_AssetService
     */
    private $service;

    /**
     * @var Mock
     */
    private $connectorService;

    /**
     * Sets up the service.
     */
    public function setUp()
    {
        $this->service = new TobiasAx_AssetService();
        $this->connectorService = $this->getMockTobiasAxAssetConnectorService();
    }

    /**
     * Test get publication assets with exception
     *
     * @covers ::getPublicationAssets
     * @expectedException Craft\TobiasAx_SoapException
     */
    public function testGetPublicationAssetsException()
    {
        $this->connectorService->expects($this->any())->method('extract')->will($this->throwException(new Exception));
        $publication = $this->getPublicationModel();
        $this->service->getPublicationAssets($publication);
    }

    /**
     * Test get publication assets
     *
     * @covers ::getPublicationAssets
     */
    public function testGetPublicationAssets()
    {
        $assets = [
            new TobiasAx_AssetModel([
                'Id' => '1',
                'Filename' => 'property1.jpg',
                'Type' => 'sometype1',
            ]),
            new TobiasAx_AssetModel([
                'Id' => '2',
                'Filename' => 'property2.jpg',
                'Type' => 'sometype1',
            ]),
        ];

        $this->connectorService->expects($this->any())->method('extract')->willReturn($assets);
        $publication = $this->getPublicationModel();
        $publicationAssets = $this->service->getPublicationAssets($publication);
        $this->assertInstanceOf(TobiasAx_PublicationAssetsModel::class, $publicationAssets);
    }

    /**
     * Sets a mock Asset Connector Service on the craft() object and returns the mock.
     *
     * @return Mock
     */
    protected function getMockTobiasAxAssetConnectorService()
    {
        $service = $this->getMockCraftService(TobiasAx_AssetConnectorService::class, 'tobiasAx_assetConnector');

        return $service;
    }

    /**
     * @return TobiasAx_PublicationModel
     */
    protected function getPublicationModel()
    {
        return new TobiasAx_PublicationModel([
            'Id' => '2017_24',
            'PropertyRegistration' => [
                'RealEstateObject' => [
                    'AddressCity' => 'ENSCHEDE',
                    'AddressHouseNumber' => '15',
                    'AddressHouseNumberAddition' => 'a',
                    'AddressStreet' => 'Capitool',
                ],
            ],
        ]);
    }
}
