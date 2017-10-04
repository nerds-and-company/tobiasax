<?php
namespace Craft;

/**
 * TobiasAX console commands
 */
class TobiasAxCommand extends BaseCommand
{
    /**
     * Create import task
     * @return void
     */
    public function actionCreateImportTask()
    {
        if (craft()->tobiasAx_import->startTask()) {
            echo "Created TobiasAX import task";
        } else {
            echo "Unable to create TobiasAX import task";
        }
    }

    /**
     * Clear registration record data
     * @return void
     */
    public function actionClearRegistrationData()
    {
        $affectedRows = craft()->tobiasAx_registrationStore->clearRecordsData();
        echo Craft::t('Cleared registration data in “{affectedRows}” rows', array('affectedRows' => $affectedRows));
    }
}
