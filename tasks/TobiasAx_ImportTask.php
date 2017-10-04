<?php

namespace Craft;

class TobiasAx_ImportTask extends BaseTask
{
    /**
     * List of active publications
     * @var array
     */
    protected $publications = [];

    /**
     * List of updated or created element ids
     * @var int[]
     */
    protected $elementIds = [];

    /**
     * Total steps
     * @var integer
     */
    protected $total = 0;

    /**
     * @return string
     */
    public function getDescription()
    {
        return Craft::t('TobiasAX publications import task');
    }

    /**
     * @return int
     */
    public function getTotalSteps()
    {
        // Fetch publications
        $this->publications = array_values(craft()->tobiasAx_publication->getActivePublications());

        // Take a step for every row
        $this->total = count($this->publications);

        TobiasAxPlugin::log('Total active publications: '.$this->total);

        // Run cleanup if there are no new publications
        if ($this->total == 0) {
            TobiasAxPlugin::log('Running cleanup because there are no publications');
            $this->cleanup();
        }

        return $this->total;
    }

    /**
     * @param  int $step
     * @return bool   whether to continue task
     */
    public function runStep($step)
    {
        TobiasAxPlugin::log('Run step '.$step);

        $continueTask = true;
        $publication = $this->publications[$step];
        $finalStep = ($step + 1) == $this->total;
        $model = craft()->tobiasAx_import->populateElement($publication);

        try {
            craft()->tobiasAx_advert->saveElement($model);
            $this->elementIds[] = $model->id;
        } catch (Exception $e) {
            TobiasAxPlugin::log(Craft::t('Unable to save advertisement #{tobiasId}: {message}', ['message' => $e->getMessage(), 'tobiasId' => $model->tobiasId]), LogLevel::Error);
        }

        // Run cleanup after import finished
        if ($finalStep) {
            TobiasAxPlugin::log('Running cleanup in final step');
            $this->cleanup();
        }

        return $continueTask;
    }

    /**
     * Removes elements not created or updated in this task
     * @return bool Whether the element(s) were deleted successfully.
     */
    protected function cleanup()
    {
        return craft()->tobiasAx_advert->deleteElementsByFilter($this->elementIds, 'rent');
    }
}
