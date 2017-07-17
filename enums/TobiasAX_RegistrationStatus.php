<?php
namespace Craft;

/**
 * Registration statusses
 */
abstract class TobiasAX_RegistrationStatus extends BaseEnum
{
    /**
     * @var string
     */
    const ACTIVE = 'Active';

    /**
     * @var string
     */
    const NOT_ACTIVE = 'NotActive';

    /**
     * @var string
     */
    const DROPPED = 'Dropped';

    /**
     * @var string
     */
    const SERVED = 'Served';

    /**
     * @var string
     */
    const NEW = 'New';
}
