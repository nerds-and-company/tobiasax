<?php

namespace Craft;

use Exception;

/**
 * Tobias AX asset service
 */
class TobiasAx_AssetService extends BaseApplicationComponent
{
    /**
     * @var AssetSourceModel
     */
    protected $assetSource = null;

    /**
     * @var string
     */
    protected $assetModel = TobiasAx_AssetModel::class;

    /**
     * Service init
     * @return void
     * @codeCoverageIgnore
     */
    public function init()
    {
        $this->setAssetSource(craft()->config->get('tobiasAxAssetSource'));
        $this->registerPluginAssetModel();
    }

    /**
     * @param string $sourceHandle
     * @codeCoverageIgnore
     */
    public function setAssetSource($sourceHandle)
    {
        $this->assetSource = $this->getAssetSource($sourceHandle);

        if ($this->assetSource == null) {
            throw new TobiasAx_SystemException(Craft::t('No asset source configured for handle {handle}”.', array('handle' => $sourceHandle)));
        }
    }

    /**
     * Gets all assets for the given publication
     * @param TobiasAx_PublicationModel $publication
     * @return TobiasAx_PublicationAssetsModel
     */
    public function getPublicationAssets($publication)
    {
        $service = craft()->tobiasAx_assetConnector;

        try {
            $request = $service->getAssets($publication->PropertyRegistration->RealEstateObject, $this->getAssetsEntityName());
            $envelope = $service->sendRequest($request);
            $assets = $service->extract($envelope, 'Body/xmlns:GetDocuRefsByEntityNameResponse/xmlns:GetDocuRefsByEntityNameResult/xmlns:DocumentReference');
        } catch (Exception $e) {
            throw new TobiasAx_SoapException('Unkown error fetching publication assets', null, $e);
        }

        $model = TobiasAx_PublicationAssetsModel::populateModel([
            'Assets' => $this->populateAssets($assets),
            'Publication' => $publication,
        ]);

        return $model;
    }

    /**
     * Downloads files and creates assets
     * @param TobiasAx_PublicationAssetsModel $assetsModel
     * @param TobiasAx_ImportModel $importModel
     * @param bool $resizeImages
     * @param array $types
     */
    public function getPublicationAssetIds($assetsModel, $importModel, $types, $resizeImages = true)
    {
        $folderId = $this->ensureFolderExists($importModel->getFoldername());
        $files = craft()->tobiasAx_assetDownload->downloadAssets($assetsModel, $types, $resizeImages);

        return $this->createAssets($files, $folderId);
    }

    /**
     * Creates assets for given files
     * @param array $files
     * @param int $folderId
     * @param AssetConflictResolution $conflictResolution
     * @return int[] asset file id's
     */
    public function createAssets($files, $folderId, $conflictResolution = AssetConflictResolution::Replace)
    {
        $assetIds = [];

        foreach ($files as $filePath) {
            $filename = basename($filePath);
            try {
                $response = craft()->assets->insertFileByLocalPath($filePath, $filename, $folderId, $conflictResolution);
                $assetIds[] = $response->getDataItem('fileId');
            } catch (Exception $e) {
                TobiasAxPlugin::log($e->getMessage(), LogLevel::Error);
            }
        }

        return $assetIds;
    }

    /**
     * @param string $handle
     * @return AssetSourceModel
     */
    protected function getAssetSource($handle)
    {
        $sources = craft()->assetSources->getAllSources('handle');
        $source = null;

        if (isset($sources[$handle])) {
            $source = $sources[$handle];
        }

        return $source;
    }

    /**
     * Ensures asset folder exists
     * @param string $folderName
     * @param bool $updateIndexes
     * @return int folder id
     */
    protected function ensureFolderExists($folderName, $updateIndexes = true)
    {
        $folderId = null;

        // Create folder if it doesn't exist
        if ($folder = craft()->assets->findFolder(['sourceId' => $this->assetSource->id, 'name' => $folderName])) {
            $folderId = $folder->id;
        } else {
            $parentFolder = craft()->assets->getRootFolderBySourceId($this->assetSource->id);
            $response = craft()->assets->createFolder($parentFolder->id, $folderName);
            $folderId = $response->getDataItem('folderId');

            // Craft index seems to be outdated. Update index and try again
            if ($folderId == null && $updateIndexes) {
                $this->indexAssetSource($this->assetSource);
                $folderId = $this->ensureFolderExists($folderName, false);
            }
        }

        return $folderId;
    }

    /**
     * Registers plugin asset model
     * @return void
     */
    protected function registerPluginAssetModel()
    {
        $assetModel = null;
        $pluginAssetModels = craft()->plugins->call('registerTobiasAxAssetModel');

        foreach ($pluginAssetModels as $classname) {
            $assetModel = $classname;
        }

        if ($assetModel == null) {
            throw new TobiasAx_SystemException('No plugin asset model configured');
        }

        $this->assetModel = $assetModel;
    }

    /**
     * Populates assets using configured asset model
     * @param array $assets
     * @return TobiasAx_AssetModel[]
     */
    public function populateAssets($assets)
    {
        $className = $this->assetModel;

        return $className::populateModels($assets);
    }

    /**
     * Syncs Craft asset source index with bucket
     * @param AssetSourceModel $assetSource
     * @return array
     */
    protected function indexAssetSource($assetSource)
    {
        $sessionId = craft()->assetIndexing->getIndexingSessionId();
        craft()->assetIndexing->getIndexListForSource($sessionId, $assetSource->id);

        return craft()->assetIndexing->processIndexForSource($sessionId, 0, $assetSource->id);
    }

    /**
     * @return string
     */
    protected function getAssetsEntityName()
    {
        return craft()->config->get('tobiasAxAssetsEntityName') ?? 'Realestateobject';
    }
}
