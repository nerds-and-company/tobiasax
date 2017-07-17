<?php
namespace Craft;

/**
 * User errors
 */
abstract class TobiasAX_UserError extends BaseEnum
{
    /**
     * @var integer
     */
    const UNKOWN = 1;

    /**
     * @var integer
     */
    const INVALID_TEMP_PASSWORD = 2;

    /**
     * @var integer
     */
    const INVALID_PASSWORD = 3;

    /**
     * @var integer
     */
    const INVALID_CREDENTIALS = 4;
}
