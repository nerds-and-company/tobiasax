<?php

namespace Craft;

use PHPUnit_Framework_MockObject_MockObject as Mock;

/**
 * Class TobiasAx_RequestServiceTest.
 *
 * @coversDefaultClass Craft\TobiasAx_RequestService
 * @covers ::<!public>
 */
class TobiasAx_ScenarioFilterTest extends TobiasAx_AbstractTest
{
    private $filter;

    public function setUp()
    {
        $this->filter = new TobiasAx_ScenarioFilter(TobiasAX_ModelScenario::CREATE);
    }

    /**
     * Tests the scenario filter with an empty array
     */
    public function testEmpty()
    {
        $this->assertTrue($this->filter->filter([]));
    }

    /**
     * Tests the scenario filter with an empty array
     */
    public function testNull()
    {
        $this->assertTrue($this->filter->filter(null));
    }

    /**
     * Tests the scenario filter with an empty array
     */
    public function testWithoutExcludes()
    {
        $testArray = ['test' => null, 'test2' => []];

        $this->assertTrue($this->filter->filter($testArray));
    }

    /**
     * Tests the scenario filter with an empty array
     */
    public function testWithExcludesWithoutScenario()
    {
        $testArray = ['test' => null, 'test2' => [], 'exclude' => [TobiasAX_ModelScenario::GET]];

        $this->assertTrue($this->filter->filter($testArray));
    }

    /**
     * Tests the scenario filter with an empty array
     */
    public function testWithExcludesWithScenario()
    {
        $testArray = ['test' => null, 'test2' => [], 'exclude' => [TobiasAX_ModelScenario::GET, TobiasAX_ModelScenario::CREATE]];

        $this->assertFalse($this->filter->filter($testArray));
    }
}
