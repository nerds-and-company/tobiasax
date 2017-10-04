<?php
namespace Craft;

/**
 * The class name is the UTC timestamp in the format of mYYMMDD_HHMMSS_pluginHandle_migrationName
 */
class m170905_112823_tobiasax_OrderRegistrationId extends BaseMigration
{
    /**
     * Any migration code in here is wrapped inside of a transaction.
     *
     * @return bool
     */
    public function safeUp()
    {
        // Add columns
        $this->addColumnAfter('tobiasax_registration', 'registrationId', array(ColumnType::Varchar, 'default' => null), 'paymentStatus');
    }
}
