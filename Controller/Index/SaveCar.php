<?php 
/**
 * @author Hanying Dong
 */

namespace Razoyo\CarProfile\Controller\Index;

use Razoyo\CarProfile\Model\CarResource;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Controller\Result\JsonFactory;

class SaveCar extends \Magento\Framework\App\Action\Action { 
    /**
     * @var CarResource
     */
    protected $carResource;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var JsonFactory
     */
    protected $jsonResultFactory;

    public function __construct(
        Context $context,
        CarResource $carResource,
        Session $customerSession,
        JsonFactory $jsonResultFactory
    ) {
        $this->carResource = $carResource; 
        $this->customerSession = $customerSession;
        $this->jsonResultFactory = $jsonResultFactory;
        parent::__construct($context);
    }

    public function execute() { 
        $params = $this->getRequest()->getParams();
        if (isset($params['carid'])) {
            $carId = $params['carid'];
            $customerId = $this->customerSession->getCustomerId();
            $response = $this->carResource->saveCar($customerId, $carId);
            
            if ($response) {
                $response = $this->jsonResultFactory->create();
                $data = [
                    'success' => true,
                    'CarId' => $carId
                ];
                $response->setData($data);

                return $response;
            }
        } else {
            return null;
        }
    } 
}
