<?php

namespace Craft;

use SimpleXMLElement;
use Exception;
use Guzzle\Http\Client as GuzzleClient;
use Guzzle\Http\Exception\BadResponseException;
use Guzzle\Http\Exception\CurlException;

/**
 * Tobias AX base connector service
 */
abstract class TobiasAx_ConnectorService extends BaseApplicationComponent
{
    /**
     * Send and handle SOAP request
     * @param  GuzzleClient $request
     * @return SimpleXMLElement
     * @throws Exception
     */
    public function sendRequest($request)
    {

        try {
            $response = $request->send();
        } catch (BadResponseException $e) {
            $envelope = $this->handleResponse($e->getResponse()->getBody());
            $message = $this->getSoapFaultMessage($envelope);
            throw new TobiasAx_SoapException("Unable to send SOAP request: ". $message, 0, $e);
        } catch (CurlException $e) {
            throw new TobiasAx_SoapException("Unable to send SOAP request", 0, $e);
        }

        $envelope = $this->handleResponse($response->getBody());

        return $envelope;
    }

    /**
     * Deserializes SOAP response body
     * @param  string $responseBody     containing soap envelope
     * @return array
     * @throws Exception
     */
    public function handleResponse($responseBody)
    {
        $envelope = $this->deserializeSoapResponse($responseBody);

        if (empty($responseBody)) {
            throw new TobiasAx_SoapException("No response from SOAP webservice");
        } elseif ($envelope == null) {
            throw new TobiasAx_SoapException("Unable to process SOAP envelope");
        } elseif ($soapFault = $this->getSoapFaultMessage($envelope)) {
            throw new TobiasAx_SoapException($soapFault);
        }

        return $envelope;
    }

    /**
     * Extract elements from SOAP envelope
     * @param  SimpleXMLElement $envelope
     * @param  string $xpath
     * @return array|null
     */
    public function extract($envelope, $xpath)
    {
        $results = $envelope->xpath($xpath);

        return $this->xmlToArray($results);
    }

    /**
     * Extract single element from SOAP envelope
     * @param  SimpleXMLElement $envelope
     * @param  string $xpath
     * @return array|null
     */
    public function extractSingle($envelope, $xpath)
    {
        $data = null;
        $results = $this->extract($envelope, $xpath);

        if (count($results) > 0) {
            $data = array_shift($results);
        }

        return $data;
    }

    /**
     * Deserializes XML Soap response
     * @param  string $responseBody
     * @return SimpleXMLElement|null
     */
    public function deserializeSoapResponse($responseBody)
    {
        $envelope = null;

        // SimpleXML seems to have problems using namespaced and non-namespaced nodes in the same query
        $responseBody = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$3", $responseBody);

        if (!empty($responseBody) && ($soapEnvelope = simplexml_load_string($responseBody)) !== false) {
            $soapEnvelope->registerXPathNamespace('xmlns:s', 'http://www.w3.org/2003/05/soap-envelope');
            $soapEnvelope->registerXPathNamespace('xmlns', 'http://www.SG.nl/services/20121122/CustomerPortalAX');
            $soapEnvelope->registerXPathNamespace('', 'http://schemas.datacontract.org/2004/07/SG.Models.Global');
            $soapEnvelope->registerXPathNamespace('xmlns:b', 'http://schemas.datacontract.org/2004/07/SG.Models.Global');
            $envelope = $soapEnvelope;
        }

        return $envelope;
    }

    /**
     * Returns SOAP response fault message
     * @param  SimpleXMLElement  $envelope
     * @return string
     */
    public function getSoapFaultMessage($envelope)
    {
        $message = null;
        $faulString = $envelope->xpath('Body/Fault/Reason/Text');

        if (count($faulString) > 0) {
            $message = (string) array_shift($faulString);
        }

        return $message;
    }

    /**
     * Converts SimpleXml to array
     * @param  SimpleXml $xml
     * @param  array     $out
     * @return array
     */
    protected function xmlToArray($xml, $out = [])
    {
        foreach ((array) $xml as $index => $node) {
            if (is_object($node) || is_array($node)) {
                $value = $this->xmlToArray($node);
            } else {
                $value = $node;
            }

            if (!empty($value)) {
                $out[$index] = $value;
            }
        }

        return $out;
    }
}
