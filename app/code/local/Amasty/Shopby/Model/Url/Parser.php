<?php

class Amasty_Shopby_Model_Url_Parser
{
    /** @var  string */
    protected $params;

    /** @var  array */
    protected $query;

    protected $optionChar;

    /**
     * @param string $params
     * @return array $query
     */
    public function parseParams($params)
    {
        /** @var Amasty_Shopby_Helper_Url $helper */
        $helper = Mage::helper('amshopby/url');
        $params = trim($helper->checkRemoveSuffix($params), '/');
        $reservedKey = Mage::getStoreConfig('amshopby/seo/key');

        if ($params == $reservedKey) {
            // Just root page
            return array();
        }

        $this->params = $params;
        $this->query = array();
        $this->optionChar = Mage::getStoreConfig('amshopby/seo/option_char');

        while ($this->params != '') {
            $stepSuccess = false;
            $stepSuccess |= $this->matchPrice();
            $stepSuccess |= $this->matchDecimal();
            $stepSuccess |= $this->matchAttribute();

            if (!$stepSuccess) return false;
        }

        return $this->query;
    }

    protected function matchPrice()
    {
        $ocq = preg_quote($this->optionChar, '/');
        $pattern = '/^price'.$ocq.'(\d+)-(\d*)/'; // 'price-10-20', 'price-20-'
        $success = preg_match($pattern, $this->params, $matches);
        if ($success)
        {
            $this->query['price'] = substr($matches[0], strlen('price' . $this->optionChar));
            $this->params = substr($this->params, strlen($matches[0]));
        }

        if (!$success) {
            $pattern = '/^price'.$ocq.'-(\d+)/'; // 'price--10'
            $success = preg_match($pattern, $this->params, $matches);
            if ($success)
            {
                $this->query['price'] = substr($matches[0], strlen('price' . $this->optionChar));
                $this->params = substr($this->params, strlen($matches[0])+1);
            }
        }

        return $success;
    }

    protected function matchDecimal()
    {
        $ocq = preg_quote($this->optionChar, '/');
        $pattern = '/^(\w[^'.$ocq.']+)'.$ocq.'(\d+\.?\d*[-,]\d+\.?\d*)/'; // 'length-10-20', 'length-2,10
        $success = preg_match($pattern, $this->params, $matches);
        if ($success)
        {
            $attribute = $matches[1];
            $value = $matches[2];
            $this->query[$attribute] = $value;
            $this->params = substr($this->params, strlen($matches[0] . $this->optionChar));
        }
        return $success;
    }

    protected function matchAttribute()
    {
        $ocq = preg_quote($this->optionChar, '/');
        $pattern = '/^([^'.$ocq.']+)/'; // 'red'
        $success = preg_match($pattern, $this->params, $matches);
        if ($success)
        {
            $key = $matches[1];

            /** @var Amasty_Shopby_Helper_Url $helper */
            $helper = Mage::helper('amshopby/url');
            foreach ($helper->getAllFilterableOptionsAsHash() as $code => $values){
                if (isset($values[$key])){
                    $value = $values[$key];
                    if (isset($this->query[$code])) {
                        $this->query[$code].= ',' . $value;
                    } else {
                        $this->query[$code] = $value;
                    }
                    $this->params = substr($this->params, strlen($matches[0] . $this->optionChar));
                    return true;
                }
            }
        }
        return false;
    }
}
