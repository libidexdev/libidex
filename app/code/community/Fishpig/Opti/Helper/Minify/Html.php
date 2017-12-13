<?php
/**
 * @category	Fishpig
 * @package		Fishpig_Opti
 * @license		http://fishpig.co.uk/license.txt
 * @author		Ben Tideswell <help@fishpig.co.uk>
 * @info			http://fishpig.co.uk/magento-optimisation.html
 */

class Fishpig_Opti_Helper_Minify_Html extends Fishpig_Opti_Helper_Minify_Abstract
{
	/**
	 * Minify a HTML string
	 *
	 * @param string $html
	 * @return string
	 */
	public function minify($html)
	{
		$protect = array(
			'pre' => array(),
			'script' => array(),
			'style' => array(),
			'textarea' => array(),
		);
		
		$tagString = '#____%s-%d____#';
		
		foreach($protect as $tag => $storage) {
			if (strpos($html, '<' . $tag) === false) {
				continue;
			}
			
			if (preg_match_all('/(<' . $tag . '.*>.*<\/' . $tag . '>)/iUs', $html, $matches)) {
				foreach($matches[1] as $it => $code) {
					$protect[$tag][$it] = $code;

					$html = str_replace($code, sprintf($tagString, $tag, $it), $html);
				}
			}
		}

		$protect['script'] = Mage::helper('opti/minify_js')->minifyScriptsArray($protect['script']);

		$html  = preg_replace('/ (class|id|target|rel|title)(="[ ]{0,}")/U', '', $html);
		$html = preg_replace('/[\s]+/', ' ', $html);
		$html = preg_replace('/ \/>/U', '/>', $html);

		foreach($protect as $tag => $storage) {
			foreach($storage as $it => $code) {
				$html = str_replace(sprintf($tagString, $tag, $it), $code, $html);
			}
		}
		
		return $html;
	}	
	
	/**
	 * Clean HTML after minifying
	 * This fixes CSS/JS filenames
	 *
	 * @param string $html
	 * @return string
	 */
	public function clean($html)
	{
		if (strpos($html, '../media/') === false) {
			return $html;
		}

		if (preg_match_all('/(src|href)="([^"]{1,}\/\.\.\/media[^"]{1,})"/U', $html, $matches)) {
			foreach($matches[0] as $key => $value) {
				$url = $matches[2][$key];
				$base = substr($url, 0, strpos($url, '../'));
				$after = substr($url, strrpos($url, '../')+3);
				$it = substr_count($url, '../');
				
				while($it-- > 0) {
					$base = dirname($base);
				}

				$html = str_replace($value, sprintf('%s="%s"', $matches[1][$key], rtrim($base, '/') . '/' . $after), $html);
			}
		}
		
		return $html;
	}
}
