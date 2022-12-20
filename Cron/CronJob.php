<?php

namespace Task\Avanzar\Cron;

class CronJob
{
	protected $productCollectionFactory;
	protected $productStatus;
	protected $productVisibility;

	public function __construct(
	    \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
	    \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
	    \Magento\Catalog\Model\Product\Visibility $productVisibility,
	    \Magento\Catalog\Model\Product\Action $action,
		\Magento\Store\Model\StoreManagerInterface $storeManager
	)
	{
	    
	    $this->productCollectionFactory = $productCollectionFactory;
	    $this->productStatus = $productStatus;
	    $this->productVisibility = $productVisibility;
	    $this->action = $action;
    	$this->storeManager = $storeManager;
	}

	public function execute()
	{


	    $collection = $this->productCollectionFactory->create();
	    $collection
	        ->addAttributeToSelect('id')
	        //->addFieldToSelect('id')
	        ->addAttributeToFilter('status', ['in' => $this->productStatus->getVisibleStatusIds()])
	        ->addAttributeToFilter('visibility', ['in' => $this->productVisibility->getVisibleInSiteIds()])
	        ;
	        try {
	        	 foreach ($collection->getData() as $prods) {
		        	//$ids[] = $prods['entity_id'];
		        	$id = $prods['entity_id']; //product id
				    $websiteId = $this->storeManager->getWebsite()->getId();
				    $store = $this->storeManager->getStore(); 
				    $storeId = $store->getId();  // Get Store ID
				    $price = rand(10,100);
				    $name = 'Task_'.$price;
				    $desc = 'Description'.$price;
				    $this->action->updateAttributes([$id],['name' => $name,'price' => $price ,'description' => $desc],$storeId);
				}
	        	
	        } catch (Exception $e) {
	        	$writer = new \Zend_Log_Writer_Stream(BP . '/var/log/cron.log');
			    $logger = new \Zend_Log();
			    $logger->addWriter($writer);
			    $logger->info("STARTED");
	        	return false;
	        }

		return $this;
	}
}