<?php

	if (!defined('__FP_UNIT')) {
		return;
	}

	_title('Options', 2);

	foreach(array('telephone', 'images') as $key) {
		$option = Mage::helper('wp_addon_acf')->getOptionValue($key);
		
		if (is_array($option)) {
			foreach($option as $it => $row) {
				foreach($row as $attr => $value) {
					if (is_object($value)) {
						_title('(' . $it . ') ' . $attr . ': <em>object</em>', 3);
					}	
					else if (is_array($value)) {
						_title('(' . $it . ') ' . $attr . ': <em>array</em>', 3);
					}
					else {
						_title('(' . $it . ') ' . $attr . ': <em>' . htmlentities($value) . '</em>', 3);
					}			
				}
			}

		}
		else {
			_title($key . ': <em>' . htmlentities($option) . '</em>', 3);
		}
	}
