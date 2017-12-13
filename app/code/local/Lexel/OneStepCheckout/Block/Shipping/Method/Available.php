<?php
class Lexel_OneStepCheckout_Block_Shipping_Method_Available extends Webshopapps_Dropship_Block_Checkout_Onepage_Shipping_Method_Available
{
	/**
	 * Show only the courier matrix rate if the customer's quote total weight
	 * exceeds 2000 grams.
	 *
	 * Codes may changes upon Matrixrates re-import. Don't think there is anything
	 * we can do about this at this time.
	 */
	const MATRIXRATE_GROUP_CODE       = 'matrixrate';
	const MATRIXRATE_COURIER_UK_CODE  = 'matrixrate_matrixrate_2431';
	const MATRIXRATE_COURIER_EU_CODE  = 'matrixrate_matrixrate_2456';
	const MATRIXRATE_COURIER_ROW_CODE = 'matrixrate_matrixrate_4189';
	const SHIPPING_WEIGHT_LIMIT       = 2000;

	/**
	 * @var bool
	 */
	protected $_isCourierOnlyMethodAvailable = false;

	/**
	 * @var array
	 */
	protected $_courierRateArray = array();

	/**
	 * Return shipping rates
	 *
	 * @return array
	 */
	public function getShippingRates()
	{
		if (empty($this->_rates)) {
			$this->getAddress()->collectShippingRates()->save();

			$groups = $this->getAddress()->getGroupedAllShippingRates();
			$weight = $this->getQuote()->getShippingAddress()->getWeight();

			foreach ($groups as $code => $groupItems) {
				if ($code === self::MATRIXRATE_GROUP_CODE) {
					if ($weight >= self::SHIPPING_WEIGHT_LIMIT) {
						$rateCounter = 0;
						foreach ($groupItems as $rate) {
							/* Additional check on method title in case rate IDs change */
							if (stripos($rate->getMethodTitle(), 'courier') !== false) {
								$this->_isCourierOnlyMethodAvailable = true;
							} else {
								if ($rate->getCode() === self::MATRIXRATE_COURIER_UK_CODE
									|| $rate->getCode() === self::MATRIXRATE_COURIER_EU_CODE
									|| $rate->getCode() === self::MATRIXRATE_COURIER_ROW_CODE
								) {
									$this->_isCourierOnlyMethodAvailable = true;
								}
							}

							if ($this->_isCourierOnlyMethodAvailable) {
								$this->_courierRateArray[$code][] = $groups[$code][$rateCounter];
								return $this->_rates = $this->_courierRateArray;
							}
							$rateCounter++;
						}
					}
				}
			}
			return $this->_rates = $groups;
		}
		return $this->_rates;
	}

	/**
	 * @return bool
	 */
	public function isCourierOnlyMethodAvailable()
	{
		return $this->_isCourierOnlyMethodAvailable;
	}
}