<?php

class IcozWeb_PayU_Adminhtml_Payu_StatusController extends Mage_Adminhtml_Controller_Action {

    public function updateAction() {
        $dataHelper   = Mage::helper('icozweb_payu');
        $session      = Mage::getSingleton('adminhtml/session');
        $orderId      = trim(stripslashes($this->getRequest()->getParam('order_id')));
        $ordersIds    = $this->getRequest()->getParam('order_ids');
        $returnToGrid = false;
        if (!is_array($ordersIds) && !$orderId) {
            $session->addError($dataHelper->__('You must select at least one order to update.'));
        } else {
            if (!is_array($ordersIds)) {
                $ordersIds = array($orderId);
            } else {
                $returnToGrid = true;
            }

            $payment      = Mage::getModel('icozweb_payu/payment_method');
            $countErrors  = 0;
            $countUpdated = 0;
            $countTotal   = count($ordersIds);
            foreach ($ordersIds as $id) {
                try {
                    $orderUpdated = $payment->checkOrderPayUStatus($id);
                    if ($orderUpdated) {
                        $countUpdated++;
                    }
                } catch (Exception $e) {
                    $dataHelper->log($dataHelper->__('There was a problem when manually updating order %s: ', $id) . $e->getMessage());
                    $countErrors++;
                }
            }

            $session->addSuccess($dataHelper->__('Verification complete. A total of %d (out of %d) orders were successfully updated.', $countUpdated, $countTotal));
            if ($countErrors > 0) {
                $session->addSuccess($dataHelper->__('A total of %d orders encountered some errors. Please review the log files.', $countErrors));
            }
        }

        if ($returnToGrid) {
            $this->_redirect('adminhtml/sales_order');
        } else {
            $this->_redirect('adminhtml/sales_order/view', array('order_id' => $orderId));
        }
    }

}
