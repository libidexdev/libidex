<?php
/**
 * Created by PhpStorm.
 * User: alejandrofrenandez
 * Date: 07/08/2017
 * Time: 13:19
 */
$prefix = '';
if (class_exists('Mage', false)) {
    $prefix = Mage::getBaseDir() . DS . 'shell' . DS;
}

require_once $prefix . 'abstract.php';

/**
 * This shell will launch the observer of malaysia price change manually.
 * Be sure to put the new factor in the backend, Inventory->Malaysia Bulk Update
 *
 */
class Mage_Shell_Malaysiaprice extends Mage_Shell_Abstract
{

    public function run()
    {
        $factor=Mage::getStoreConfig('lexel/malaysiabulkupdate/factor');
        $model=Mage::getModel('lexel_malaysiabulkupdate/observer');
        echo ' with the factor '.$factor.' ';
        $model->_runJob($factor);

    }


}
echo 'script start ';
$shell = new Mage_Shell_Malaysiaprice();
$shell->run();
echo 'script end ';