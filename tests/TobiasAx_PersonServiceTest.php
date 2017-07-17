<?php

namespace Craft;

use PHPUnit_Framework_MockObject_MockObject as Mock;

/**
 * Class TobiasAx_PersonServiceTest.
 *
 * @coversDefaultClass Craft\TobiasAx_PersonService
 * @covers ::<!public>
 */
class TobiasAx_PersonServiceTest extends TobiasAx_AbstractTest
{
    /**
     * @var TobiasAx_PersonService
     */
    private $service;

    /**
     * Sets up the service.
     */
    public function setUp()
    {
        $this->service = new TobiasAx_PersonService();

        $this->getMockTobiasAxPersonConnectorService();
        $this->getMockTobiasAxPersonAddressService();
        $this->getMockTobiasAxPersonCommunicationService();
        $this->getMockTobiasAxPersonIncomeService();
    }

    /**
     * Sets a mock Person Connector Service on the craft() object and returns the mock.
     *
     * @return Mock
     */
    protected function getMockTobiasAxPersonConnectorService()
    {
        $personConnectorService = $this->getMockCraftService(TobiasAx_PersonConnectorService::class, 'tobiasAx_personConnector');

        $person = new TobiasAx_PersonModel();
        $person->Infix = "de";
        $person->Initials = "T";
        $person->Nationality = "Dutch";
        $person->Type = 'Person';
        $person->Birthdate = new DateTime('01-01-1990');
        $person->Firstname = "Test";
        $person->Gender = "Male";


        $personConnectorService->expects($this->any())->method('extractSingle')->willReturn($person);

        return $personConnectorService;
    }

    /**
     * Sets a mock Person Address Service on the craft() object and returns the mock.
     *
     * @return Mock
     */
    protected function getMockTobiasAxPersonAddressService()
    {
        $personAddressService = $this->getMockCraftService(TobiasAx_PersonAddressService::class, 'tobiasAx_personAddress');

        $addresses = [];
        $address = new TobiasAx_AddressModel();
        $address->Street = 'teststreet';
        $addresses[0] = $address;
        $addresses[1] = $address;
        $addresses[2] = $address;

        $personAddressService->expects($this->any())->method('createPersonAddresses')->willReturn($addresses);

        return $personAddressService;
    }

    /**
     * Sets a mock Person Communication Service on the craft() object and returns the mock.
     *
     * @return Mock
     */
    protected function getMockTobiasAxPersonCommunicationService()
    {
        $personCommunicationService = $this->getMockCraftService(TobiasAx_PersonCommunicationService::class, 'tobiasAx_personCommunication');

        $communications = [];
        $communication = new TobiasAx_CommunicationModel();
        $communication->Secret = true;
        $communication->Value = "0123456789";
        $communication->Type = 'phone';
        $communications[0] = $communication;
        $communications[1] = $communication;
        $communications[2] = $communication;

        $personCommunicationService->expects($this->any())->method('upsertPersonCommunications')->willReturn($communications);

        return $personCommunicationService;
    }

    /**
     * Sets a mock Person Income Service on the craft() object and returns the mock.
     *
     * @return Mock
     */
    protected function getMockTobiasAxPersonIncomeService()
    {
        $personIncomeService = $this->getMockCraftService(TobiasAx_PersonincomeService::class, 'tobiasAx_personIncome');

        $incomes = [];
        $income = new TobiasAx_IncomeModel();
        $income->GrossNet = 'Gross';
        $income->Amount = 123456789;
        $income->Type = 'Money';
        $incomes[0] = $income;
        $incomes[1] = $income;
        $incomes[2] = $income;

        $personIncomeService->expects($this->any())->method('createPersonIncomes')->willReturn($incomes);

        return $personIncomeService;
    }

    /**
     * Tests the creation of a person
     */
    public function testCreatePerson()
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

        $createdPerson = $this->service->CreatePerson($person);

        $this->assertNotNull($createdPerson);
        $this->assertEquals(new DateTime('01-01-1990'), $createdPerson->Birthdate);
        $this->assertEquals('de', $createdPerson->Infix);
        $this->assertEquals('T', $createdPerson->Initials);
        $this->assertEquals('Dutch', $createdPerson->Nationality);
        $this->assertEquals('Test', $createdPerson->Firstname);
        $this->assertEquals('Male', $createdPerson->Gender);

        $this->assertNotNull($createdPerson->Addresses);
        foreach ($createdPerson->Addresses as $key => $val) {
            $this->assertEquals('teststreet', $val->Street);
        }

        $this->assertNotNull($createdPerson->Communications);
        foreach ($createdPerson->Communications as $key => $val) {
            $this->assertEquals(true, $val->Secret);
            $this->assertEquals('0123456789', $val->Value);
            $this->assertEquals('phone', $val->Type);
        }

        $this->assertNotNull($createdPerson->Incomes);
        foreach ($createdPerson->Incomes as $key => $val) {
            $this->assertEquals('Gross', $val->GrossNet);
            $this->assertEquals(123456789, $val->Amount);
            $this->assertEquals('Money', $val->Type);
        }
    }
}
