<?php

namespace Craft;

use PHPUnit_Framework_MockObject_MockObject as Mock;

/**
 * Class TobiasAx_AssetDownloadServiceTest.
 *
 * @coversDefaultClass Craft\TobiasAx_AssetDownloadService
 * @covers ::<!public>
 */
class TobiasAx_AssetDownloadServiceTest extends TobiasAx_AbstractTest
{
    /**
     * @var TobiasAx_AssetDownloadService
     */
    private $service;

    /**
     * @var string
     */
    private $endpoint = 'https://assets.someurl.com';

    /**
     * Sets up the service.
     */
    public function setUp()
    {
        $this->service = new TobiasAx_AssetDownloadService();

        craft()->config->set('tobiasAxAssetEndpoint', $this->endpoint);
        craft()->config->set('tobiasAxAllowedImageExtensions', ['jpg', 'jpeg']);
    }

    /**
     * Test downloading asset file
     *
     * @covers ::downloadFile
     */
    public function testDownloadExistingFile()
    {
        $url = 'https://koppelingenaanbod.triada.nl/Website-fotos/EMST/VHE/Ds%20van%20Rhijnstraat/19/Advertentie/8166AK-19-01.JPG';
        $basename = basename($url);
        $this->service->downloadFile($url, $basename);

        $this->assertFileExists($basename, 'File should exist on local filesytem');
        unlink($basename);
    }

    /**
     * Test downloading missing asset file
     *
     * @covers ::downloadFile
     * @expectedException Craft\TobiasAx_SystemException
     */
    public function testDownloadMissingFile()
    {
        $url = 'https://koppelingenaanbod.triada.nl/foto.jpg';
        $basename = basename($url);
        $this->service->downloadFile($url, $basename);

        $this->assertFileNotExists($basename, 'File should not exist on local filesystem');
    }

    /**
     * Test formatting asset urls
     *
     * @covers ::formatAssetUrls
     */
    public function testFormatUrls()
    {
        $assetsModel = $this->getPublicationAssetModel();
        $formattedUrls = $this->service->formatAssetUrls($assetsModel, ['sometype1']);
        $this->assertCount(1, $formattedUrls, 'Should contain 1 asset after filtering');

        foreach ($formattedUrls as $formattedUrl) {
            $this->assertStringStartsWith($this->endpoint, $formattedUrl, 'Formatted url should start with configured endpoint');
            $this->assertTrue(filter_var($formattedUrl, FILTER_VALIDATE_URL) !== false, 'Formatted url should be valid');
        }
    }

    /**
     * Test filter images
     *
     * @covers ::filterImages
     */
    public function testFilterImages()
    {
        $files = [
            'somepath/test.jpg',
            'somepath/test.jpeg',
            'somepath/test.pdf',
        ];
        $images = $this->service->filterImages($files);
        $this->assertCount(2, $images, 'Should contain 2 files after filtering images');
    }

    /**
     * @return TobiasAx_PublicationAssetsModel
     */
    protected function getPublicationAssetModel()
    {
        $assets = [
            [
                "CompanyId" => "1",
                "Filename" => "8172BE-9A-01.jpg",
                "Id" => "5637244334",
                "Name" => "8172BE-9A-01",
                "Type" => "sometype1",
            ],
            [
                "CompanyId" => "1",
                "Filename" => "Krugerstraat 11A.pdf",
                "Id" => "5637244335",
                "Name" => "Krugerstraat 11A",
                "Type" => "sometype2",
            ],
        ];

        $publication = new TobiasAx_PublicationModel([
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

        return new TobiasAx_PublicationAssetsModel([
            'Assets' => $assets,
            'Publication' => $publication,
        ]);
    }
}
