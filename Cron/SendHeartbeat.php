<?php

declare(strict_types=1);

namespace Lotsofpixels\CronjobMonitor\Cron;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\HTTP\Adapter\Curl;
use Magento\Framework\Logger\Monolog;
use Zend_Uri;
use Exception;

class SendHeartbeat
{

    private $storeConfig;

    private $curlClient;

    private $logger;

    public function __construct(
        ScopeConfigInterface $storeConfig,
        Curl                 $curl,
        Monolog              $logger

    )
    {
        $this->storeConfig = $storeConfig;
        $this->curlClient = $curl;
        $this->logger = $logger;
    }

    public function execute()
    {
        $pingUrl = $this->storeConfig->getValue('cronjobmonitor/general/ping_url');
        if ($this->storeConfig->getValue('cronjobmonitor/general/enabled')) {
            if (Zend_Uri::check($pingUrl)) {
                try {
                    $this->curlClient->setConfig(['timeout' => 5]);
                    $this->curlClient->write('GET', $pingUrl);
                    $this->curlClient->read();
                } catch (Exception $e) {
                    $this->logger->addError($e->getMessage());
                }
            }
        }
    }
}


