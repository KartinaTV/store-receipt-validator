<?php

namespace ReceiptValidator\WindowsStore;

use DOMDocument;
use ReceiptValidator\Abstracts\AbstractValidator;
use ReceiptValidator\RunTimeException;
use RobRichards\XMLSecLibs\XMLSecEnc;
use RobRichards\XMLSecLibs\XMLSecurityDSig;

class Validator extends AbstractValidator
{
    protected $cache;

    public function __construct(CacheInterface $cache = null)
    {
        $this->cache = $cache;
    }

    /**
     * @throws RunTimeException
     *
     * @return Response
     */
    public function validate()
    {
        // Load the receipt that needs to verified as an XML document.
        $receipt = $this->getPurchaseToken();
        $dom = new \DOMDocument();
        if (false === @$dom->loadXML($receipt)) {
            throw new RunTimeException('Invalid XML');
        }

        // The certificateId attribute is present in the document root, retrieve it.
        $certificateId = $dom->documentElement->getAttribute('CertificateId');
        if (empty($certificateId)) {
            throw new RunTimeException('Missing CertificateId in receipt');
        }

        // Retrieve the certificate from the official site.
        $certificate = $this->retrieveCertificate($certificateId);

        $isValid = $this->validateXml($dom, $certificate);

        return Response::factory($dom, $isValid);
    }

    /**
     * @param string $certificateId
     *
     * @return resource
     */
    protected function retrieveCertificate($certificateId)
    {
        // Retrieve from cache if a cache handler has been set.
        $cacheKey = 'store-receipt-validate.windowsstore.'.$certificateId;
        $certificate = null !== $this->cache ? $this->cache->get($cacheKey) : null;

        if (null === $certificate) {
            $maxCertificateSize = 10000;

            // We are attempting to retrieve the following url. The getAppReceiptAsync website at
            // http://msdn.microsoft.com/en-us/library/windows/apps/windows.applicationmodel.store.currentapp.getappreceiptasync.aspx
            // lists the following format for the certificate url.
            $certificateUrl = '/fwlink/?LinkId=246509&cid='.$certificateId;

            // Make an HTTP GET request for the certificate.
            $client = new \GuzzleHttp\Client(['base_uri' => 'https://go.microsoft.com']);
            $response = $client->request('GET', $certificateUrl);

            // Retrieve the certificate out of the response.
            $certificate = $response->getBody();

            // Write back to cache.
            if (null !== $this->cache) {
                $this->cache->put($cacheKey, $certificate, 3600);
            }
        }

        return openssl_x509_read($certificate);
    }

    /**
     * @param resource $certificate
     *
     * @throws RunTimeException
     *
     * @return bool
     */
    protected function validateXml(DOMDocument $dom, $certificate)
    {
        $secDsig = new XMLSecurityDSig();

        // Locate the signature in the receipt XML.
        $dsig = $secDsig->locateSignature($dom);
        if (null === $dsig) {
            throw new RunTimeException('Cannot locate receipt signature');
        }

        $secDsig->canonicalizeSignedInfo();
        $secDsig->idKeys = ['wsu:Id'];
        $secDsig->idNS = [
            'wsu' => 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd',
        ];

        if (!$secDsig->validateReference()) {
            throw new RunTimeException('Reference validation failed');
        }

        $key = $secDsig->locateKey();
        if (null === $key) {
            throw new RunTimeException('Could not locate key in receipt');
        }

        $keyInfo = XMLSecEnc::staticLocateKeyInfo($key, $dsig);
        if (!$keyInfo->key) {
            $key->loadKey($certificate);
        }

        return 1 == $secDsig->verify($key);
    }
}
