<?php
class ObjectSource_MalaysiaInvoice_Block_Adminhtml_Widget_Grid_Column_Renderer_NamedInput
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    protected $_values;

    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $html = '<input type="text" ';
        $html .= 'name="' . $this->getColumn()->getId() . '[' . $row->getId() . ']" ';
        $html .= 'value="' . $row->getData($this->getColumn()->getIndex()) . '"';
        $html .= 'class="input-text ' . $this->getColumn()->getInlineCss() . '"/>';
        return $html;
    }
}
