<?php
class Lexel_MalaysiaBulkUpdate_Model_Observer extends Mage_Core_Model_Abstract
{

    protected $_debug = '';

    protected $_logFile = 'Lexel_MalaysiaBulkUpdate.log';

    public function run()
    {
        if (Mage::getStoreConfig('lexel/malaysiabulkupdate/ready') == '1') {
            
            // Set to 0 immediatly so we dont trigger again
            Mage::getConfig()->saveConfig('lexel/malaysiabulkupdate/ready', '0');

            // Set to 1 to disable the settings white this is running
            Mage::getConfig()->saveConfig('lexel/malaysiabulkupdate/running', 1);

            // Clear config cache
            Mage::app()->getStore()->resetConfig();

            $factor = (float)Mage::getStoreConfig('lexel/malaysiabulkupdate/factor');

            $this->_debug .= 'Starting update with factor of ' . $factor . PHP_EOL;
            $this->_runJob($factor);
        }
    }
    
    public function _runJob($factor)
    {
        // Run backup of existing Malaysia Prices
        $backupPath = Mage::getBaseDir('var') . DS . 'export' . DS . 'lexel_malaysia_price_backup' . DS;
        $backupFile =  $backupPath . 'malaysia-prices-' . date('d-m-Y') . '_' . date('H:i:s') . '.csv';
        $csv = Mage::getModel('lexel_malaysiabulkupdate/prices');
        $csv->generateCSV($backupFile);

        // Get all products and Malaysia Prices ready for update
        $products = Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToSelect('malaysia_price')
            ->addAttributeToSelect('price');

        // Get count of products for logging purposes and create counter
        $totalProducts = $products->count();
        $i = 0;

        foreach ($products as $product) {
            $i++;

            $this->_debug .= "Processing  {$product->getEntityId()} ({$i} of {$totalProducts})". PHP_EOL;
            $price = $product->getPrice();
            $newPrice = number_format(($price * $factor), 2, '.', ''); //$price * $factor;
            //Mage::log($newPrice,null,'holo.log',true);
            $this->_debug .= $product->getEntityId() . ' WAS: ' . $price . ', NOW: ' . $newPrice . PHP_EOL;

            $product->setMalaysiaPrice($newPrice);
            if(empty($price)) {
                $product->setMalaysiaPrice("");
            }
            Mage::log($this->_debug, Zend_Log::INFO, $this->_logFile, true);
            $this->_debug = '';
            $product->getResource()->saveAttribute($product, 'malaysia_price');


        }
        //try {
            //Mage::log('Saving', Zend_Log::INFO, $this->_logFile, true);
            //$products->save();
            //Mage::log('Finished Saving', Zend_Log::INFO, $this->_logFile, true);
        //}
        Mage::log('Finished Saving', Zend_Log::INFO, $this->_logFile, true);
        //catch (Exception $e) {
        //    Mage::log('Error Saving, error was: ' . $e->getMessage(), Zend_Log::ERR, $this->_logFile, true);
        //}

        $this->_sendStatus('Bulk update complete');

        // Set running status to 0 so prices are not chnages until someone starts another job
        Mage::getConfig()->saveConfig('lexel/malaysiabulkupdate/running', 0);

        // Clear config cache
        Mage::app()->getStore()->resetConfig();
    }

    protected function _sendStatus($status)
    {

        $mail = Mage::getModel('core/email');
        $mail->setToName('Libidex');
        $mail->setToEmail('sales@libidex.co.uk');
        $mail->setBody($status);
        $mail->setSubject('Malaysia Prices Updated');
        $mail->setFromEmail('sales@libidex.co.uk');
        $mail->setFromName("Malaysia Prices Update Job");
        $mail->setType('text');

        try {
            $mail->send();
            Mage::log('Success email sent', Zend_Log::INFO, $this->_logFile, true);
        }
        catch (Exception $e) {
            Mage::log('Unable to send success email, error given was:', Zend_Log::ERR, $this->_logFile, true);
            Mage::log($e->getMessage(), Zend_Log::ERR, $this->_logFile, true);

        }
    }
}