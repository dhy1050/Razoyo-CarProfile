<?php
/**
 * @author Hanying Dong
 */

namespace Razoyo\CarProfile\Setup;

use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\Config;
use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;

/**
 * Create new attribute car_id
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var CustomerSetupFactory
     */
    private $customerEavSetupFactory;

    /**
     * @var Config
     */
    protected $eavConfig;

    /**
     * @var AttributeSetFactory
     */
    private $attributeSetFactory;

    /**
     * @param CustomerSetupFactory $customerEavSetupFactory
     * @param Config $eavConfig
     * @param AttributeSetFactory $attributeSetFactory
     */
    public function __construct(
            CustomerSetupFactory $customerEavSetupFactory,
            Config $eavConfig,
            AttributeSetFactory $attributeSetFactory
    ) {
            $this->customerEavSetupFactory = $customerEavSetupFactory;
            $this->eavConfig       = $eavConfig;
            $this->attributeSetFactory = $attributeSetFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $eavSetup = $this->customerEavSetupFactory->create(['setup' => $setup]);

        $customerEntity = $eavSetup->getEavConfig()->getEntityType('customer');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        $eavSetup->addAttribute(
                \Magento\Customer\Model\Customer::ENTITY,
                'car_id',
                [
                        'type'         => 'varchar',
                        'label'        => 'Car ID',
                        'input'        => 'text',
                        'required'     => false,
                        'visible'      => true,
                        'user_defined' => true,
                        'position'     => 999,
                        'system'       => 0,
                ]
        );

        $newAttribute = $this->eavConfig->getAttribute(Customer::ENTITY, 'car_id');
        $newAttribute->setData('used_in_forms', ['adminhtml_customer']);
        $newAttribute->setData('attribute_set_id', $attributeSetId);
        $newAttribute->setData('attribute_group_id', $attributeGroupId);

        $newAttribute->save();
    }
}
