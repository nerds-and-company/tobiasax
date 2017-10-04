<?php

namespace Craft;

use PHPUnit_Framework_MockObject_MockObject as Mock;

/**
 * Class TobiasAx_PersonRegistrationServiceTest.
 *
 * @coversDefaultClass Craft\TobiasAx_RegistrationService
 * @covers ::<!public>
 */
class TobiasAx_RegistrationServiceTest extends TobiasAx_AbstractTest
{
    /**
     * @var TobiasAx_RegistrationService
     */
    private $service;

    /**
     * Sets up the service.
     */
    public function setUp()
    {
        $this->service = new TobiasAx_RegistrationService();

        $this->getMockTobiasAxRegistrationConnectorService();
        $this->getMockTobiasAxRegistrationStoreService();
    }

    /**
     * Sets a mock Registration Connector Service on the craft() object and returns the mock.
     *
     * @return Mock
     */
    protected function getMockTobiasAxRegistrationConnectorService()
    {
        $registrationConnectorService = $this->getMockCraftService(TobiasAx_RegistrationConnectorService::class, 'tobiasAx_registrationConnector');

        $registration = $this->generateRegistration();

        $registrationConnectorService->expects($this->any())->method('extractSingle')->willReturn($registration);

        return $registrationConnectorService;
    }

    /**
     * Sets a mock Registration Store Service on the craft() object and returns the mock.
     *
     * @return Mock
     */
    protected function getMockTobiasAxRegistrationStoreService()
    {
        $registrationStoreService = $this->getMockCraftService(TobiasAx_RegistrationStoreService::class, 'tobiasAx_registrationStore');

        return $registrationStoreService;
    }

    /**
     * Tests the creation of a registration
     */
    public function testCreateRegistrationWithPartner()
    {
        $registrationData = $this->generateRegistration();

        $registration = new TobiasAx_RegistrationModel();
        $registration->setAttributes($registrationData);

        $comparisonRegistration = clone $registration;

        $createdRegistration = $this->service->createRegistration(0, $registration);

        $this->assertNotNull($createdRegistration);
        $this->assertNotNull($createdRegistration->PropertySeeker);
        $this->assertEquals($comparisonRegistration->PropertySeeker->PersonId, $createdRegistration->PropertySeeker->PersonId);
        $this->assertEquals(count($comparisonRegistration->CoRegistrants), count($createdRegistration->CoRegistrants));
        $this->assertEquals($comparisonRegistration->InvoiceMethod, $comparisonRegistration->InvoiceMethod);
    }

    private function generateRegistration()
    {
        $registrationData = [];
        $registrationData['FamilySize'] = 2;

        $registrant = $this->createPerson();
        $partner = $this->createPerson();

        $seeker = new TobiasAx_PropertySeekerModel();
        $seeker->PersonId = $registrant->Id;
        $seeker->Status = 'Active';

        $coRegistrant = [];
        $coRegistrant['CoRegistrantType'] = 'Partner';
        $coRegistrant['PersonId'] = $partner->Id;
        $coRegistrant['Birthdate'] = $partner->Birthdate;
        $coRegistrant['Gender'] = $partner->Gender;
        $coRegistrant['Type'] = 'Person';
        $coRegistrant['CoContractor'] = true;

        $incomes = $partner->Incomes;
        $coRegistrant['Income'] = array_shift($incomes)->Amount;
        $coRegistrantData[] = $coRegistrant;

        $incomes = $registrant->Incomes;
        $registrationData['Income'] = array_shift($incomes)->Amount;
        $registrationData['CurrentHousing'] = 'None';
        $registrationData['InvoiceMethod'] = 'PIN';
        $registrationData['NumberOfChildren'] = 0;
        $registrationData['PropertySeeker'] = $seeker;
        $registrationData['TypeId'] = 'Woning';
        $registrationData['CoRegistrants'] = $coRegistrantData;
        $registrationData['BuyRent'] = 'Rent';

        return $registrationData;
    }

    private function createPerson()
    {
        $person = new TobiasAx_PersonModel();

        //Create addresses
        $addresses = [];
        $address = new TobiasAx_AddressModel();
        $address->Street = 'teststreet';
        $addresses[0] = $address;
        $addresses[1] = $address;
        $addresses[2] = $address;
        $person->Addresses = $addresses;

        $person->Birthdate = new DateTime('01-01-1990');

        //Create communications
        $communications = [];
        $communication = new TobiasAx_CommunicationModel();
        $communication->Secret = true;
        $communication->Value = "0123456789";
        $communication->Type = 'phone';
        $communications[0] = $communication;
        $communications[1] = $communication;
        $communications[2] = $communication;
        $person->Communications = $communications;

        $person->Firstname = "Test";
        $person->Gender = "Male";

        //Create incomes
        $incomes = [];
        $income = new TobiasAx_IncomeModel();
        $income->GrossNet = 'Gross';
        $income->Amount = 123456789;
        $income->Type = 'Money';
        $incomes[0] = $income;
        $incomes[1] = $income;
        $incomes[2] = $income;
        $person->Incomes = $incomes;

        $person->Infix = "de";
        $person->Initials = "T";
        $person->Nationality = "Dutch";
        $person->Type = 'Person';
        $person->FocusGroupId = 2;

        return $person;
    }
}
