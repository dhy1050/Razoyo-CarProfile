<?php
/**
 * @author Hanying Dong
 */

namespace Razoyo\CarProfile\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Model\Session;
use Psr\Log\LoggerInterface;
use Razoyo\CarProfile\Model\CarResource;

class CarProfile extends Template
{
    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var CarResource
     */
    protected $carResource;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(
        Session $session,
        CarResource $carResource,
        LoggerInterface $logger,
        Context $context,
        array $data = []
    ) {
        $this->customerSession = $session;
        $this->carResource = $carResource;
        $this->logger = $logger;
        parent::__construct($context, $data);
    }

    public function checkSavedCar()
    {
        $customerId = $this->customerSession->getCustomerId();  
        $carData = $this->carResource->getCarDataByCustomerId($customerId);

        return $carData;
    }
}
