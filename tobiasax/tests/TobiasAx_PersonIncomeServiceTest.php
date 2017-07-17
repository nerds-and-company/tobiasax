<?php

namespace Craft;

use PHPUnit_Framework_MockObject_MockObject as Mock;

/**
 * Class TobiasAx_PersonIncomeServiceTest.
 *
 * @coversDefaultClass Craft\TobiasAx_PersonIncomeService
 * @covers ::<!public>
 */
class TobiasAx_PersonIncomeServiceTest extends TobiasAx_AbstractTest
{
    /**
     * @var TobiasAx_PersonIncomeService
     */
    private $service;

    /**
     * Sets up the service.
     */
    public function setUp()
    {
        $this->service = new TobiasAx_PersonIncomeService();

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

        $income = new TobiasAx_IncomeModel();
        $income->GrossNet = 'Gross';
        $income->Amount = 123456789;
        $income->Type = 'Money';

        $personConnectorService->expects($this->any())->method('extractSingle')->willReturn($income);

        return $personConnectorService;
    }

    /**
     * Tests the creation of multiple incomes
     */
    public function testCreateIncomes()
    {
        $incomes = [];

        $income = new TobiasAx_IncomeModel();
        $income->GrossNet = 'Gross';
        $income->Amount = 123456789;
        $income->Type = 'Money';

        $personId = 12345;
        $incomes[0] = $income;
        $incomes[1] = $income;
        $incomes[2] = $income;

        $createdIncomes = $this->service->CreatePersonIncomes($incomes, $personId);

        $this->assertNotNull($createdIncomes);
        $this->assertTrue(count($incomes) == count($createdIncomes));

        foreach ($createdIncomes as $val) {
            $this->assertEquals('Gross', $val->GrossNet);
            $this->assertEquals(123456789, $val->Amount);
            $this->assertEquals("Money", $val->Type);
        }
    }

    /**
     * Tests the creation of a single income
     */
    public function testCreateIncome()
    {
        $personId = 12345;

        $income = new TobiasAx_IncomeModel();
        $income->GrossNet = 'Gross';
        $income->Amount = 123456789;
        $income->Type = 'Money';

        $createdIncome = $this->service->CreatePersonIncome($income, $personId);

        $this->assertNotNull($createdIncome);
        $this->assertEquals('Gross', $createdIncome->GrossNet);
        $this->assertEquals(123456789, $createdIncome->Amount);
        $this->assertEquals("Money", $createdIncome->Type);
    }
}
