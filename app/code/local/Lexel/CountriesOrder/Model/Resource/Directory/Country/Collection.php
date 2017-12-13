<?php

class Lexel_CountriesOrder_Model_Resource_Directory_Country_Collection
    extends Mage_Directory_Model_Resource_Country_Collection
{

    public function toOptionArray($emptyLabel = '') {

        // clear existing country list
        $options = null;

        // set array index to 0
        $i = 0;

        // these are the countries to add to the top of the list
        $options[$i++] = array('value'=>'GB', 'label'=>'United Kingdom');
        $options[$i++] = array('value'=>'US', 'label'=>'United States');
        $options[$i++] = array('value'=>'DE', 'label'=>'Germany');
        $options[$i++] = array('value'=>'FR', 'label'=>'France');
        $options[$i++] = array('value'=>'BE', 'label'=>'Belgium');
        $options[$i++] = array('value'=>'NL', 'label'=>'Netherlands');
        $options[$i++] = array('value'=>'JP', 'label'=>'Japan');
        $options[$i++] = array('value'=>'CA', 'label'=>'Canada');

        /**
         * add an item to the array with no value for value attribute,
         * this causes magento to select it by default and also places
         * a divider between the top/popular countries and the rest of the list
         */
        $options[$i++] = array('value'=>'', 'label'=>'--- Choose a Country ---');

        // include the list of all countries
        include 'Base_Countries.php';

        return $options;
    }
}
