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

require_once(Mage::getBaseDir("lib") . "/PayU/PayU.php");

class IcozWeb_PayU_Model_Payment_Boleto extends IcozWeb_PayU_Model_Payment_Method {

    protected $_code                        = 'icozweb_payu_boleto';
    protected $_formBlockType               = 'icozweb_payu/form_boleto';
    protected $_infoBlockType               = 'icozweb_payu/form_info_boleto';


    /**
     * Check whether payment method can be used
     *
     * @param Mage_Sales_Model_Quote|null $quote
     *
     * @return bool
     */
    public function isAvailable($quote = null) {
        $isAvailable    = parent::isAvailable($quote);
        if ($quote != null && $quote->getBillingAddress()->getCountry() != PayUCountries::BR) {
            return false;
        }

        return $isAvailable;
    }


    /**
     * Assign data to info model instance
     *
     * @param   mixed $data
     * @return  Mage_Payment_Model_Info
     */
    public function assignData($data) {
        if (!($data instanceof Varien_Object)) {
            $data = new Varien_Object($data);
        }

        $info = $this->getInfoInstance();
        $info->setAdditionalInformation('payu_device_session_id', $data->getPayuDeviceSessionId());

        return $this;
    }


    /**
     * Order payment method
     *
     * @param Varien_Object $payment
     * @param float $amount
     *
     * @return IcozWeb_PayU_Model_Payment_Boleto
     */
    public function order(Varien_Object $payment, $amount) {
        $dataHelper    = Mage::helper('icozweb_payu');
        try {
            $paymentMethod  = 'BOLETO_BANCARIO';
            $parameters     = $this->prepareParameters($payment, $paymentMethod, $amount);
            $expiration     = new DateTime('now +' . Mage::helper('icozweb_payu')->getBoletoExpiration() . ' day');
            $parameters[PayUParameters::EXPIRATION_DATE] = $expiration->format('Y-m-d\TH:i:s');
            $newInformation = $this->processPayUPayment($parameters);

            if (!isset($newInformation['expiration'])) {
                $newInformation['expiration'] = $expiration->format('d/m/Y');
            }
            $previousInformation = $payment->getAdditionalInformation();
            if ($previousInformation && is_array($previousInformation)) {
                $newInformation = array_merge($newInformation, $previousInformation);
            }
            $payment->setAdditionalInformation($newInformation);
        } catch (Exception $e) {
            $message = $dataHelper->__('There was a problem processing your payment: ') . $e->getMessage();
            $dataHelper->log($message);
            Mage::throwException($message);
        }

        $payment->setSkipOrderProcessing(true);

        return $this;
    }

}
