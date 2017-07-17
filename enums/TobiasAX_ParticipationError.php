<?php
namespace Craft;

/**
 * Participation errors
 */
abstract class TobiasAX_ParticipationError extends BaseEnum
{
    /**
     * @var integer
     */
    const MISSING_PUBLICATION = 1;

    /**
     * @var integer
     */
    const MISSING_REGISTRATION = 2;

    /**
     * @var integer
     */
    const UNKOWN = 3;

    /**
     * @var integer
     */
    const MISSING_ID = 4;

    /**
     * @var integer
     */
    const NOT_FOUND = 5;
}
