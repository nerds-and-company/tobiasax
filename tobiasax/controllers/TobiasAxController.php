<?php
namespace Craft;

/**
 * Tobias AX controller.
 */
class TobiasAxController extends BaseController
{
    /**
     * Element index.
     */
    public function actionAdvertisementIndex()
    {
        $variables['types'] = craft()->tobiasAx_advertType->getAllTypes();

        $this->renderTemplate('tobiasax/advertisements/_index', $variables);
    }

    /**
     * Edit an element.
     *
     * @param array $variables
     *
     * @throws HttpException
     */
    public function actionEditElement(array $variables = array())
    {
        $variables = $this->setType($variables);
        $user = craft()->userSession->getUser();

        if (empty($variables['type'])) {
            throw new HttpException(404);
        }

        // Check user edit permission
        if (craft()->request->isCpRequest() && !$user->can('tobiasax:edit:' . $variables['type']->handle)) {
            throw new HttpException(403);
        }

        $variables = $this->setupElement($variables);

        // Check user edit peer permission
        $isPeerEntry = $variables['element']->authorId != null && $variables['element']->authorId != $user->id;
        if (craft()->request->isCpRequest() && !$user->can('tobiasax:editPeerEntries') && $isPeerEntry) {
            throw new HttpException(403);
        }

        $variables = $this->addTabs($variables);
        $variables = $this->setTitle($variables);

        // Breadcrumbs
        $variables['crumbs'] = array(
            array('label' => Craft::t('Advertisements'), 'url' => UrlHelper::getUrl('advertisements')),
        );

        // Set the "Continue Editing" URL
        $variables['continueEditingUrl'] = 'advertisements/{id}';

        // Render the template!
        $this->renderTemplate('tobiasax/advertisements/_edit', $variables);
    }

    /**
     * Saves an element.
     */
    public function actionSaveElement()
    {
        $this->requirePostRequest();

        $elementId = craft()->request->getPost('elementId');

        if ($elementId) {
            $element = craft()->tobiasAx_advert->getElementById($elementId);

            if (!$element) {
                throw new Exception(Craft::t('No element exists with the ID “{id}”', array('id' => $elementId)));
            }
        } else {
            $elementType = craft()->tobiasAx_advert->getElementTypeComponent();
            $element = $elementType->populateElementModel();
        }

        // Set the element attributes, defaulting to the existing values for whatever is missing from the post data
        if (craft()->request->getPost('status')) {
            $element->status = craft()->request->getPost('status');
        }

        $element->advertTypeId = craft()->request->getPost('advertTypeId', $element->advertTypeId);
        $element->setContentFromPost('fields');

        if (craft()->tobiasAx_advert->saveElement($element)) {
            craft()->userSession->setNotice(Craft::t('Element saved.'));
            $this->redirectToPostedUrl($element);
        } else {
            craft()->userSession->setError(Craft::t('Couldn’t save element.'));

            // Send the element back to the template
            craft()->urlManager->setRouteVariables(array(
                'element' => $element,
            ));
        }
    }

    /**
     * Start 'manual' update task.
     */
    public function actionStartImport()
    {
        $redirect = craft()->request->getRequiredParam('redirect');

        // start task
        $checkPendingTask = false;
        craft()->tobiasAx_import->startTask($checkPendingTask);

        // set CP notice
        craft()->userSession->setNotice(Craft::t('Started importing Tobias advertisements'));

        // redirect
        $this->redirect($redirect);
        $this->end();
    }

    /**
     * @param array $variables
     * @return array $variables with element added
     */
    private function setupElement(array $variables)
    {
        if (empty($variables['element'])) {
            if (!empty($variables['elementId'])) {
                $variables['element'] = craft()->tobiasAx_advert->getElementById($variables['elementId']);

                if (!$variables['element']) {
                    throw new HttpException(404);
                }
            } else {
                $variables['element'] = new TobiasAx_AdvertisementModel();
            }
        }
        return $variables;
    }

    /**
     * @param array $variables
     * @return array $variables with tabs added
     */
    private function addTabs($variables)
    {
        $variables['tabs'] = array();

        foreach ($variables['type']->getFieldLayout()->getTabs() as $index => $tab) {
            // Do any of the fields on this tab have errors?
            $hasErrors = false;

            if ($variables['element']->hasErrors()) {
                foreach ($tab->getFields() as $field) {
                    if ($variables['element']->getErrors($field->getField()->handle)) {
                        $hasErrors = true;
                        break;
                    }
                }
            }

            $variables['tabs'][] = array(
                'label' => $tab->name,
                'url' => '#tab' . ($index + 1),
                'class' => ($hasErrors ? 'error' : null),
            );
        }
        return $variables;
    }

    /**
     * @param array $variables
     * @return array $variables with title set
     */
    private function setTitle($variables)
    {
        if (!$variables['element']->id) {
            $variables['title'] = Craft::t('Create a new element');
        } else {
            $variables['title'] = $variables['element']->title;
        }
        return $variables;
    }

    /**
     * @param array $variables
     * @return array $variables with type added
     */
    private function setType($variables)
    {
        if (!empty($variables['typeHandle'])) {
            $variables['type'] = craft()->tobiasAx_advertType->getTypeByHandle($variables['typeHandle']);
        } elseif (!empty($variables['typeId'])) {
            $variables['type'] = craft()->tobiasAx_advertType->getTypeById($variables['typeId']);
        }
        return $variables;
    }
}
