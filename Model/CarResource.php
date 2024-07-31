<?php
/**
 * @author Hanying Dong
 */

namespace Razoyo\CarProfile\Model;

use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session as CustomerSession;

class CarResource
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    const CAR_ATTRIBUTE = "car_id";

    /**
     * 
     * @param Client $client
     * @param LoggerInterface $logger
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        Client $client,
        LoggerInterface $logger,
        CustomerRepositoryInterface $customerRepository,
        CustomerSession $customerSession
    ) {
        $this->client = $client;
        $this->logger = $logger;
        $this->customerRepository = $customerRepository;
        $this->customerSession = $customerSession;
    }

    public function getCarId($customerId)
    {
        $customer = $this->getCustomer($customerId);
        $carId = $customer? $customer->getCustomAttribute(self::CAR_ATTRIBUTE)->getValue() : null;

        return $carId;
    }

    /**
     * Get Token for getting car data from API
     * @var void
     */
    private function getToken()
    {
        $client = new Client([
            'base_uri' => 'https://exam.razoyo.com/api/cars/'
        ]);

        $response = $client->request('GET');

        if ($response->getStatusCode() == 200) {
            $data = $response->getHeaders();

            return $data['your-token'][0];
        } else {
            $this->logger->info(
                'API call failed with status code: ' . $response->getStatusCode(),
                [
                    'module' => 'Razoyo_CarProfile',
                    'class' => get_class($this)
                ] 
            );
        }
    }

    /**
     * Get Car info base on car id
     */
    public function getCarDataByCustomerId($customerId)
    {
        // Get token
        $token = $this->getToken();
        // Get car id for login customer
        $id = $this->getCarId($customerId);

        if ($id) {
            $client = new Client([
                'base_uri' => 'https://exam.razoyo.com/api/cars/' . $id,
                'headers' => [
                    'Authorization' => 'Bearer ' . $token
                ]
            ]);

            try {
                $response = $client->request('GET');

                if ($response->getStatusCode() == 200) {
                    $data = json_decode($response->getBody(), true);
                    return $data;
                } else {
                    $this->logger->info(
                        'Cannot get the car data',
                        [
                            'module' => 'Razoyo_CarProfile',
                            'class' => get_class($this)
                        ] 
                    );
                }
            } catch (\Exception $e) {
                $this->logger->info(
                    $e->getMessage(),
                    [
                        'module' => 'Razoyo_CarProfile',
                        'class' => get_class($this)
                    ] 
                );
            }
        }

        return null;
    }

    /**
     * Save car id to login customer
     */
    public function saveCar($customerId, $car)
    {
        try {
            if ($car) {
                $customer = $this->getCustomer($customerId);
                $customer->setCustomAttribute(self::CAR_ATTRIBUTE, $car);
                $this->customerRepository->save($customer);

                return true;
            }
        } catch (\Exception $e) {
            $this->logger->error('cannot save the car profile');
            $this->logger->error(
                $e->getMessage(),
                [
                    'module' => 'Razoyo_CarProfile',
                    'class' => get_class($this)
                ] 
            );
            return false;
        }
    }        

    private function getCustomer($customerId)
    {
        if ($customerId) {
            $customer = $this->customerRepository->getById($customerId);
            return $customer;
        } else {
            $customerId = $this->customerSession->getCustomerId(); 
            return $customerId? $this->customerRepository->getById($customerId) : null;
        }
    }
}
