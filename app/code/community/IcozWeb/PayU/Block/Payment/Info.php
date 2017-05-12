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

class IcozWeb_PayU_Block_Payment_Info extends Mage_Core_Block_Template {

    public function getPaymentInfo() {
        $orderID = Mage::getSingleton('checkout/session')->getLastOrderId();
        if ($orderID) {
            $order         = Mage::getModel('sales/order')->load($orderID);
            $payment       = $order->getPayment();
            $paymentMethod = $payment->getMethod();
            $information   = $payment->getAdditionalInformation();
            $dataHelper    = Mage::helper('icozweb_payu');
            $url           = '';
            $text          = $dataHelper->__('There was a problem retrieving your PayU payment information');
            switch($paymentMethod) {
                case 'icozweb_payu_boleto':
                    $text = $dataHelper->__('There was a problem generating your PayU bank slip');
                    if (isset($information['boleto_url']) && !empty($information['boleto_url'])) {
                        $url  = $information['boleto_url'];
                        $text = $dataHelper->__('Click here to print your bank slip');
                    }
                    break;
                case 'icozweb_payu_cash_argentina':
                case 'icozweb_payu_cash_colombia':
                case 'icozweb_payu_cash_mexico':
                case 'icozweb_payu_cash_peru':
                    if (isset($information['cash_payment_receipt_html']) && !empty($information['cash_payment_receipt_html'])) {
                        $url  = $information['cash_payment_receipt_html'];
                        $text = $dataHelper->__('Click here to proceed to payment');
                    }
                    break;
                default:
                    return false;
            }
            return array(
                'url'  => $url,
                'text' => $text,
            );
        }
        return false;
    }

}
