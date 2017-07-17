<?php

namespace Craft;

use Exception;
use \Guzzle\Http\Client as GuzzleClient;

/**
 * Tobias AX request service
 *
 * Contains logics for sending SOAP requests
 */
class TobiasAx_RequestService extends BaseApplicationComponent
{
    /**
     * Creates SOAP request client using given action, template and data
     * @param  string $actionName SOAP action name
     * @param  string $template   template path
     * @param  array $data        template data
     * @return GuzzleClient
     */
    public function createRequest($actionName, $template, $data = [])
    {
        $endpoint = $this->getUrlEndpoint();
        $companyId = $this->getCompanyId();
        $addressingEndpoint = $this->getAddressingEndpoint();
        $url = $endpoint.$actionName;

        $data = array_merge([
            'endpoint' => $endpoint,
            'companyId' => $companyId,
            'actionName' => $actionName,
            'addressingEndpoint' => $addressingEndpoint,
            'wsaAction' => null
        ], $data);

        $envelope = $this->renderEnvelope($template, $data);

        error_log($envelope);

        $request = $this->createRequestClient($url, $envelope);

        return $request;
    }

    /**
     * Creates SOAP request client
     * @param  string $endpoint   URL endpoint
     * @param  string $body       request body
     * @param  array $options     request options
     * @return GuzzleClient
     */
    public function createRequestClient($endpoint, $body, $options = [])
    {
        $client = new GuzzleClient();

        $options = array_merge([
            'timeout'         => 60,
            'connect_timeout' => 15,
            'allow_redirects' => 1,
        ], $options);

        $request = $client->post($endpoint, null, null, $options);

        // use HTTP basic authentication
        $request->setAuth(
            craft()->config->get('tobiasAxUsername'),
            craft()->config->get('tobiasAxPassword')
        );

        $request->setBody($body, 'application/soap+xml; charset=utf-8');

        return $request;
    }

    /**
     * Renders SOAP envelope using given template and data
     * @param  string $template template path
     * @param  array $data      template data
     * @return string
     */
    public function renderEnvelope($template, $data)
    {
        $oldPath = craft()->templates->getTemplatesPath();

        try {
            $newPath = craft()->path->getPluginsPath();
            craft()->templates->setTemplatesPath($newPath);
            $envelope = craft()->templates->render($template, $data);

        } catch (TemplateLoaderException $e) {
            throw new TobiasAx_SystemException("Unable to locate SOAP template: " . $template, null, $e);
        } catch (Exception $e) {
            throw new TobiasAx_SystemException("Unknown error rendering SOAP template: " . $e->getMessage(), null, $e);
        } finally {
            craft()->templates->setTemplatesPath($oldPath);
        }

        return $envelope;
    }

    /**
     * Returns webservice endpoint URL
     * @return string
     */
    protected function getUrlEndpoint()
    {
        return craft()->config->get('tobiasAxEndpoint');
    }

    /**
     * Returns webservice addressing endpoint
     * @return string
     */
    protected function getAddressingEndpoint()
    {
        return craft()->config->get('tobiasAxAddressingEndpoint');
    }

    /**
     * Returns TobiasAx company id
     * @return string
     */
    protected function getCompanyId()
    {
        return craft()->config->get('tobiasAxCompanyId');
    }
}
