<?php

namespace Craft;

use PHPUnit_Framework_MockObject_MockObject as Mock;

/**
 * Class designed to help out mocking TobiasAX objects, made abstract so PHPUnit ignores it.
 *
 * Class TobiasAx_AbstractTest
 */
abstract class TobiasAx_AbstractTest extends BaseTest
{
    /**
     * {@inheritdoc}
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        // classes
        require_once __DIR__ . '/../../../../vendor/nerds-and-company/schematic/src/Services/Base.php';
        require_once __DIR__ . '/../../../../vendor/nerds-and-company/schematic/src/Models/Result.php';

        // plugin
        require_once __DIR__ . '/../TobiasAxPlugin.php';

        //enums
        require_once __DIR__ . '/../enums/TobiasAX_ModelScenario.php';

        // variables
        require_once __DIR__ . '/../variables/TobiasAxVariable.php';
        require_once __DIR__ . '/../etc/filters/TobiasAx_ScenarioFilter.php';

        // service classes
        require_once __DIR__ . '/../services/TobiasAx_RequestService.php';
        require_once __DIR__ . '/../services/TobiasAx_ConnectorService.php';
        require_once __DIR__ . '/../services/TobiasAx_PersonService.php';
        require_once __DIR__ . '/../services/TobiasAx_PersonConnectorService.php';
        require_once __DIR__ . '/../services/TobiasAx_PersonAddressService.php';
        require_once __DIR__ . '/../services/TobiasAx_PersonCommunicationService.php';
        require_once __DIR__ . '/../services/TobiasAx_PersonIncomeService.php';
        require_once __DIR__ . '/../services/TobiasAx_RegistrationService.php';
        require_once __DIR__ . '/../services/TobiasAx_RegistrationConnectorService.php';
        require_once __DIR__ . '/../services/TobiasAx_AssetService.php';
        require_once __DIR__ . '/../services/TobiasAx_AssetConnectorService.php';
        require_once __DIR__ . '/../services/TobiasAx_AssetDownloadService.php';

        // models
        require_once __DIR__ . '/../models/TobiasAx_BaseModel.php';
        require_once __DIR__ . '/../models/TobiasAx_EntityModel.php';
        require_once __DIR__ . '/../models/TobiasAx_AssetModel.php';
        require_once __DIR__ . '/../models/TobiasAx_PublicationModel.php';
        require_once __DIR__ . '/../models/TobiasAx_PropertyRegistrationModel.php';
        require_once __DIR__ . '/../models/TobiasAx_RealEstateModel.php';
        require_once __DIR__ . '/../models/TobiasAx_PublicationAssetsModel.php';
        require_once __DIR__ . '/../models/TobiasAx_AddressModel.php';
        require_once __DIR__ . '/../models/TobiasAx_CommunicationModel.php';
        require_once __DIR__ . '/../models/TobiasAx_IncomeModel.php';
        require_once __DIR__ . '/../models/TobiasAx_PersonModel.php';
        require_once __DIR__ . '/../models/TobiasAx_PropertySeekerModel.php';
        require_once __DIR__ . '/../models/TobiasAx_RegistrationModel.php';
        require_once __DIR__ . '/../models/TobiasAx_CoRegistrantModel.php';

        // exceptions
        require_once __DIR__ . '/../etc/errors/TobiasAx_SystemException.php';
        require_once __DIR__ . '/../etc/errors/TobiasAx_SoapException.php';
    }

    /**
     * @param string $class
     * @param string $service
     *
     * @return Mock
     */
    protected function getMockCraftService($class, $service)
    {
        $mock = $this->getMockBuilder($class)->getMock();

        $this->setComponent(craft(), $service, $mock);

        return $mock;
    }

    /**
     * Sets a mock config on the Craft() object and returns the mock.
     *
     * @return Mock
     */
    protected function getMockConfig()
    {
        return $this->getMockCraftService(ConfigService::class, 'config');
    }

    /**
     * Sets a mock path on the Craft() object and returns the mock.
     *
     * @return Mock
     */
    protected function getMockPath()
    {
        return $this->getMockCraftService(PathService::class, 'path');
    }

    /**
     * Sets a mock elements service on the craft() object and returns the mock.
     *
     * @return Mock
     */
    protected function getMockElementsService()
    {
        $mockCriteria = $this->getMockCriteria();

        $mockElementsService = $this->getMockCraftService(ElementsService::class, 'elements');
        $mockElementsService->expects($this->any())->method('getCriteria')->willReturn($mockCriteria);

        return $mockElementsService;
    }

    /**
     * Sets a mock Request Service on the craft() object and returns the mock.
     *
     * @return Mock
     */
    protected function getMockTobiasAxRequestService()
    {
        return $this->getMockCraftService(TobiasAx_RequestService::class, 'tobiasAx_request');
    }
}
