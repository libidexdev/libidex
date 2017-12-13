<?php
/**
 * @category	Fishpig
 * @package		Fishpig_Opti
 * @license		http://fishpig.co.uk/license.txt
 * @author		Ben Tideswell <help@fishpig.co.uk>
 * @info			http://fishpig.co.uk/magento-optimisation.html
 */

class Fishpig_Opti_Helper_Minify_Css extends Fishpig_Opti_Helper_Minify_Abstract
{
	/**
	 * Minify the CSS files in the head
	 *
	 * @param Varien_Event_Observer $observer)
	 * @return $this
	 */	
	public function minifyHeadItems(array $items)
	{
		$cssDir = Mage::getBaseDir('media') 
			. DS . (Mage::app()->getStore()->isCurrentlySecure() ? 'css_secure' : 'css');
		
		if (!is_dir($cssDir)) {
			@mkdir($cssDir);
			
			if (!is_dir($cssDir)) {
				return $items;
			}
		}

		$refresh = $this->_isRefresh();
		$storeId = $this->_getStoreId();
		$_design = Mage::getDesign();

		foreach($items as $key => $item) {
			if ($item['if'] || $item['type'] !== 'skin_css') {
				continue;
			}

			$originalUrl = $_design->getSkinUrl($item['name']);
			$originalFile = $_design->getFilename($item['name'], array('_type' => 'skin'));
			$minifiedFile = $cssDir  . DS . 'opti-' . $storeId . '-' . str_replace(DS, '-', substr($originalFile, strlen(Mage::getBaseDir('skin'))+1));

			$item['name'] = '../../../../media/' . basename($cssDir) . '/' . basename($minifiedFile);

			if (!$refresh && is_file($minifiedFile)) {
				$items[$key] = $item;
			}
			else if (($css = @file_get_contents($originalFile)) !== false) {
				if (($css = $this->minify($css, dirname($originalUrl) . '/')) !== '') {
					if (@file_put_contents($minifiedFile, $css) && is_file($minifiedFile)) {
						$items[$key] = $item;
					}
				}
			}
		}

		return $items;
	}

	/**
	 * Minify the CSS string
	 *
	 * @param string $css
	 * @param string $baseUrl
	 * @return string
	 */
	public function minify($css, $baseUrl)
	{
		if ($this->_includeLibrary('CSSmin')) {
			$lib = new  CSSmin(true);
			
			$css = $lib->run($css);
		}

		$baseUrl = rtrim($baseUrl, '/') . '/';

		if (preg_match_all('/url\((.*)\)/iU', $css, $matches)) {
			foreach($matches[0] as $it => $find) {
				$url = trim($matches[1][$it], "'\"");

				if (strpos($url, 'http://') === false && strpos($url, 'https://') === false && substr($url, 0, 1) !== '/') {
					$url = $baseUrl . trim($url, '" \'/');

					$css = str_replace($find, "url('{$url}')", $css);
				}
			}
		}

		return trim($css);
	}
}
