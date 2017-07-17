<?php
namespace Craft;

/**
 * Model variable scenarios
 */
abstract class TobiasAX_ModelScenario extends BaseEnum
{
    /**
     * @var integer
     */
    const GET = 1;

    /**
     * @var integer
     */
    const CREATE = 2;

    /**
     * @var integer
     */
    const UPDATE = 3;

    /**
     * @var integer
     */
    const DELETE = 4;
}
