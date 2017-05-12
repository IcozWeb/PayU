<?php
/**
 * IcozWeb
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to support@icozweb.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * THIS  SOFTWARE  IS PROVIDED "AS IS" AND ANY EXPRESSED OR IMPLIED WARRANTIES,
 * INCLUDING,  BUT NOT LIMITED TO,  THE IMPLIED  WARRANTIES  OF MERCHANTABILITY
 * AND FITNESS  FOR A  PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * REGENTS OR  CONTRIBUTORS  BE LIABLE  FOR ANY  DIRECT,  INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;  LOSS OF USE, DATA, OR PROFITS;
 * OR  BUSINESS  INTERRUPTION)  HOWEVER  CAUSED AND ON ANY THEORY OF LIABILITY,
 * WHETHER  IN CONTRACT,  STRICT LIABILITY,  OR TORT  (INCLUDING  NEGLIGENCE OR
 * OTHERWISE)  ARISING  IN  ANY  WAY OUT OF  THE USE OF THIS SOFTWARE,  EVEN IF
 * ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension for your
 * needs please refer to http://icozweb.com or contact us directly at support@icozweb.com
 * for more information.
 *
 * @copyright  Copyright (c) 2017 IcozWeb (http://icozweb.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class IcozWeb_PayU_Model_Observer {

    public function paymentStartBefore($observer) {
        $dataHelper = Mage::helper('icozweb_payu');
        $payment    = $observer->getEvent()->getPayment();
        $order      = $payment->getOrder();
        $method     = $payment->getMethodInstance();
        $methodCode = $method->getCode();
        if (!$dataHelper->isPayUEnabled() || !$dataHelper->isMethodPayU($methodCode)) {
            return $this;
        }

        $payment->setAmountOrdered($order->getTotalDue());
        $payment->setBaseAmountOrdered($order->getBaseTotalDue());
        $payment->setShippingAmount($order->getShippingAmount());
        $payment->setBaseShippingAmount($order->getBaseShippingAmount());

        $amount = Mage::app()->getStore()->roundPrice($order->getBaseTotalDue());
        $method->setStore($order->getStoreId());
        $method->validate();
        $method->setStore($order->getStoreId())->order($payment, $amount);

        return $this;
    }

    public function addPayUStatusCheckButton($event) {
        $dataHelper = Mage::helper('icozweb_payu');
        $block      = $event->getBlock();
        if (!$dataHelper->isPayUEnabled() || !($block instanceof Mage_Adminhtml_Block_Sales_Order_View)) {
            return;
        }

        $order      = $block->getOrder();
        $methodCode = $order->getPayment()->getMethodInstance()->getCode();
        if (!$dataHelper->isMethodPayU($methodCode)) {
            return;
        }

        $message = Mage::helper('core')->jsQuoteEscape(
            $dataHelper->__('Do you wish to check the status of order #%s on PayU?', $order->getIncrementId())
        );
        $block->addButton('icozweb_payu_check_status', array(
            'label'   => $dataHelper->__('Update Status (PayU)'),
            'onclick' => 'deleteConfirm(\'' . $message . '\', \'' . $block->getUrl('adminhtml/payu_status/update') . '\')',
        ), 1, 100, 'header', 'header');
    }

    public function addPayUStatusCheckMassAction($event) {
        $dataHelper = Mage::helper('icozweb_payu');
        $block      = $event->getBlock();
        if ($dataHelper->isPayUEnabled() && $block instanceof Mage_Adminhtml_Block_Widget_Grid_Massaction && $block->getRequest()->getControllerName() == 'sales_order') {
            $block->addItem('icozweb_payu_mass_check_status', array(
                'label' => $dataHelper->__('Update Status (PayU)'),
                'url'   => $block->getUrl('adminhtml/payu_status/update')
            ));
        }
    }

}
