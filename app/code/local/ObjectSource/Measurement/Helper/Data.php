<?php

class ObjectSource_Measurement_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function isMadeToMeasure($item)
    {
        if (!$item)
        {
            return false;
        }

        $options = $item->getProductOptions();
        if (!$options)
        {
            return false;
        }

       if (array_key_exists('options', $options))
       {
           $options = $options['options'];
           foreach($options as $option)
           {
               if (array_key_exists('label', $option) && array_key_exists('value', $option))
               {
                   if ($option['label'] == 'Size Size' && ($option['value'] == 'MADE TO MEASURE' || $option['value'] == 'MADE TO FIT'))
                   {
                       return true;
                   }
               }
           }
           return false;
       }
        else
        {
            return false;
        }
    }
}
