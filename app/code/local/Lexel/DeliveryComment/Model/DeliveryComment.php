<?php
/**
 * Created by PhpStorm.
 * User: kieron
 * Date: 25/10/16
 * Time: 10:50
 */
class Lexel_DeliveryComment_Model_DeliveryComment extends Mage_Core_Model_Abstract {

    /**
     * @param $statusHistoryItem
     * @return bool|string
     */
    public function addStatusDeliveryComment ($statusHistoryItem) {

        $data = array();
        $data['order_id'] = $statusHistoryItem->getParentId();
        $data['order_status_history_id'] = $statusHistoryItem->getEntityId();
        $data['sent'] = 0;

        try {
            $write = Mage::getSingleton('core/resource')->getConnection('core_write');
            $write->insert('lexel_delivery_comments', $data);
            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param $orderId
     * @return bool|string
     */
    public function getShippingComments ($orderId) {

        $comment = array();
        $comments = '';
        $priorComment = '';
        $resource = Mage::getSingleton('core/resource');
        $db = $resource->getConnection('core_read');

        $query = $db->select()
            ->from(array ('ldc' => $resource->getTableName('lexel_delivery_comments')),
                array ('ldc.id', 'ldc.order_id', 'ldc.order_status_history_id', 'ldc.sent', 'sfosh.comment'))
            ->join(array (
                'sfosh' => $resource->getTableName('sales_flat_order_status_history')),
                'ldc.order_status_history_id = sfosh.entity_id', '')
            ->where('ldc.order_id = ?', $orderId)
            ->where('ldc.sent = ?', 0);

        //$test = $query->__toString();
        $results = $db->fetchAll($query);

        $priorComment = 'Dear customer, below are any requests you made during production of your order.' . PHP_EOL;
        $priorComment .= 'The comments do not appear in the original order shown here but we have made the changes you have asked for.' . PHP_EOL;
        $priorComment .= 'We hope you enjoy your latest Libidex purchase.' . PHP_EOL;

        foreach($results as $result) {
            $comments .= isset($result['comment']) ? $result['comment'] . PHP_EOL : '';
            $comment['id'][] = $result['id'];
        }

        if($comments != '') {
            $comment['comment'] = $priorComment;
        }
        $comment['comment'] .= $comments;

        return (count($comment) != 0) ? $comment : false;
    }

    /**
     * @param $commentIds
     * @return bool
     */
    public function markCommentAsSent($commentIds) {

        if($commentIds == '') {
            return false;
        }
        else {
            try {

                $db = Mage::getSingleton('core/resource')->getConnection('core_write');
                $table = Mage::getSingleton('core/resource')->getTableName('lexel_delivery_comments');
                $where = str_replace("'", '', $db->quoteInto("id in (?)", $commentIds));
                $db->update($table, array('sent' => 1), $where);

            } catch (Exception $e) {

            }
            return true;
        }
    }
}