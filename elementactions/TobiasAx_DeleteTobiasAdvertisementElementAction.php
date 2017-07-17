<?php
namespace Craft;

class TobiasAx_DeleteTobiasAdvertisementElementAction extends BaseElementAction
{
    public function getName()
    {
        return Craft::t('Delete file');
    }

    public function isDestructive()
    {
        return true;
    }

    public function performAction(ElementCriteriaModel $criteria)
    {
        $elements = $criteria->ids();
        $elementId = array_shift($elements);

        $this->setMessage(Craft::t('Tobias advertentie succesvol verwijderd.'));

        craft()->elements->deleteElementById($elementId);

        return true;
    }
}
