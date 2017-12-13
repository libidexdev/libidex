<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Addon_ACF_Model_Resource_Field extends Fishpig_Wordpress_Model_Resource_Abstract
{
	/**
	 * Fields to be unserialized after load
	 *
	 * @var array
	 */
	protected $_serializableFields = array(
		'meta_value' => array(null, array()),
	);
	
	/**
	 * Initialise the model
	 *
	 */
	public function _construct()
	{
		$this->_init('wp_addon_acf/field', 'meta_key');
	}
	
	/**
	 * Custom load SQL to combine required tables
	 *
	 * @param string $field
	 * @param string|int $value
	 * @param Mage_Core_Model_Abstract $object
	 */
	protected function _getLoadSelect($field, $value, $object)
	{
		if ($object->getScope()) {
			return $this->_getScopedLoadSelect($field, $value, $object);
		}
		
		$select = $this->_getReadAdapter()->select()
			->from(array('main_table' => $this->getMainTable()), '');
		
		if (is_array($value)) {
			$select->where('main_table.meta_key=?', '_' . $value[0])
				->where('main_table.post_id=?', $value[1]);
		}
		else {
			$select->where('main_table.meta_key=?', '_' . $value);
		}
		
		$select->join(
			array('_table' => $this->getMainTable()),
			'`_table`.`meta_key`=`main_table`.`meta_value`',
			'*'
		);
		
		return $select;
	}
	
	/**
	 * Custom load SQL to combine required tables
	 *
	 * @param string $field
	 * @param string|int $value
	 * @param Mage_Core_Model_Abstract $object
	 */
	protected function _getScopedLoadSelect($field, $value, $object)
	{
		$select = $this->_getReadAdapter()->select()
			->from(array('main_table' => $this->getTable('wordpress/option')), '');
		
		if (is_array($value)) {
			$select->where('main_table.option_name=?', '_' . $object->getScope() . '_' . $value[0])
				->where('main_table.post_id=?', $value[1]);
		}
		else {
			$select->where('main_table.option_name=?', '_' . $object->getScope() . '_' . $value);
		}
		
		$select->join(
			array('_table' => $this->getMainTable()),
			'`_table`.`meta_key`=`main_table`.`option_value`',
			'*'
		);

		return $select;
	}
		
	/**
	 * Migrate the now unserialized data in meta_key into the root of the
	 * data array
	 *
	 * @param Mage_Core_Model_Abstract $object
	 * @return $this
	 */
	protected function _afterLoad(Mage_Core_Model_Abstract $object)
	{
		parent::_afterLoad($object);
		
		$object->addData($object->getData('meta_value'))
			->unsetData('meta_value');

		return $this;
	}	
}
