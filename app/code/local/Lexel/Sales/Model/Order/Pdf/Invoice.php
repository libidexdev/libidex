<?php
class Lexel_Sales_Model_Order_Pdf_Invoice extends Mage_Sales_Model_Order_Pdf_Invoice
{
    /**
     * Return PDF document
     * Modification: Add line: $this->insertOscComments($page, $order);
     *
     * @param  array $invoices
     * @return Zend_Pdf
     */
    public function getPdf($invoices = array())
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('invoice');

        $pdf = new Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new Zend_Pdf_Style();
        $this->_setFontBold($style, 10);

        foreach ($invoices as $invoice) {
            if ($invoice->getStoreId()) {
                Mage::app()->getLocale()->emulate($invoice->getStoreId());
                Mage::app()->setCurrentStore($invoice->getStoreId());
            }
            $page  = $this->newPage();
            $order = $invoice->getOrder();
            /* Add image */
            $this->insertLogo($page, $invoice->getStore());
            /* Add address */
            $this->insertAddress($page, $invoice->getStore());
            /* Add head */
            $this->insertOrder(
                $page,
                $order,
                Mage::getStoreConfigFlag(self::XML_PATH_SALES_PDF_INVOICE_PUT_ORDER_ID, $order->getStoreId())
            );
            /* Add document text and number */
            $this->insertDocumentNumber(
                $page,
                Mage::helper('sales')->__('Invoice # ') . $invoice->getIncrementId()
            );
            /* Add table */
            $this->_drawHeader($page);
            /* Add body */
            foreach ($invoice->getAllItems() as $item){
                if ($item->getOrderItem()->getParentItem()) {
                    continue;
                }
                /* Draw item */
                $this->_drawItem($item, $page, $order);
                $page = end($pdf->pages);
            }
            /* Add totals */
            $this->insertTotals($page, $invoice);

            /* Add OneStepCheckout Customer Comments */
            $this->insertOscComments($page, $order);

            if ($invoice->getStoreId()) {
                Mage::app()->getLocale()->revert();
            }
        }
        $this->_afterGetPdf();
        return $pdf;
    }

    /**
     * Inserts One Step Checkout comments from order record into Invoice PDF
     * @param $page
     * @param $order
     */
    public function insertOscComments(&$page, $order)
    {
        if( !$order->getOnestepcheckoutCustomercomment() ) { return; }

        $this->y -= 20;
        $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
        $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $this->y, 570, $this->y - 20);
        $page->setFillColor(new Zend_Pdf_Color_Rgb(1, 1, 1));
        $page->drawRectangle(25, $this->y - 20, 570, $this->y - 40);
        $page->setFillColor(new Zend_Pdf_Color_RGB(0.1, 0.1, 0.1));
        $page->drawText(Mage::helper('onestepcheckout')->__('Customer Comments'), 35, $this->y - 13, 'UTF-8');
        $page->drawText($order->getOnestepcheckoutCustomercomment(), 33, $this->y - 33, 'UTF-8');
        $this->y -= 50;
    }
}