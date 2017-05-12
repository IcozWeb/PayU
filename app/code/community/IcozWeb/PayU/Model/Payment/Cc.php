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

class IcozWeb_PayU_Model_Payment_Cc extends IcozWeb_PayU_Model_Payment_Method {

    protected $_code                        = 'icozweb_payu_cc';
    protected $_formBlockType               = 'icozweb_payu/form_cc';
    protected $_infoBlockType               = 'icozweb_payu/form_info_cc';
    protected $_canCapture                  = true;
    

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
        // Adjusting month formatting
        $expMonth = $data->getPayuCcExpMonth();
        if (strlen($expMonth) == 1) {
            $expMonth = '0' . $expMonth;
        }
        $info->setAdditionalInformation('payu_device_session_id', $data->getPayuDeviceSessionId())
            ->setAdditionalInformation('credit_card_owner', $data->getPayuCreditCardOwner())
            ->setAdditionalInformation('credit_card_cpf', $data->getPayuCreditCardCpf())
            ->setAdditionalInformation('credit_card_dob', $data->getPayuCreditCardDob())
            ->setAdditionalInformation('credit_card_phone', $data->getPayuCreditCardPhone())
            ->setAdditionalInformation('credit_card_hash', $info->encrypt($data->getPayuCreditCardNumber())) // only temporarily to pass it below
            ->setAdditionalInformation('credit_card_exp_month', $info->encrypt($expMonth))
            ->setAdditionalInformation('credit_card_exp_year', $info->encrypt($data->getPayuCcExpYear()))
            ->setAdditionalInformation('credit_card_cvv', $info->encrypt($data->getPayuCreditCardCvv()))
            ->setCcType($data->getPayuCreditCardType())
            ->setCcLast4(substr($data->getPayuCreditCardNumber(), -4));

        //Installments
        if ($data->getPayuCreditCardInstallments()) {
            $installments = explode('|', $data->getPayuCreditCardInstallments());
            if ($installments !== false && count($installments) == 2) {
                $info->setAdditionalInformation('installment_quantity', (int)$installments[0]);
                $info->setAdditionalInformation('installment_value', $installments[1]);
            }
        }

        return $this;
    }

    /**
     * Validate payment method information object
     *
     * @return Mage_Payment_Model_Abstract
     */
    public function validate() {
        parent::validate();
        $info                = $this->getInfoInstance();
        if ($info->getAdditionalInformation('order_placed')) {
            return; // no validation needed, order already placed
        }
        $creditCardHash      = $info->getAdditionalInformation('credit_card_hash');
        $creditCardHolder    = $info->getAdditionalInformation('credit_card_owner');
        $creditCardDob       = $info->getAdditionalInformation('credit_card_dob');
        $creditCardPhone     = $info->getAdditionalInformation('credit_card_phone');
        $installmentQuantity = $info->getAdditionalInformation('installment_quantity');
        $dataHelper          = Mage::helper('icozweb_payu');
        if (empty($creditCardHash) || empty($installmentQuantity)) {
            $message = $dataHelper->__('There was a problem retrieving your payment data. Please, contact our team.');
            $dataHelper->log($message);
            Mage::throwException($message);
        }
        if (empty($creditCardHolder)) {
            $message = $dataHelper->__('The card holder name is a required field.');
            $dataHelper->log($message);
            Mage::throwException($message);
        }
        if (empty($creditCardDob)) {
            $message = $dataHelper->__('The card holder date of birth is a required field.');
            $dataHelper->log($message);
            Mage::throwException($message);
        }
        if (empty($creditCardPhone)) {
            $message = $dataHelper->__('The card holder phone is a required field.');
            $dataHelper->log($message);
            Mage::throwException($message);
        }
        return $this;
    }

    /**
     * Order payment method
     *
     * @param Varien_Object $payment
     * @param float $amount
     *
     * @return IcozWeb_PayU_Model_Payment_Cc
     */
    public function order(Varien_Object $payment, $amount) {
        $dataHelper        = Mage::helper('icozweb_payu');
        $httpHelper        = Mage::helper('core/http');
        $storeCountry      = $dataHelper->getStoreCountry();
        PayU::$apiKey      = $dataHelper->getApiKey();
        PayU::$apiLogin    = $dataHelper->getApiLogin();
        PayU::$merchantId  = $dataHelper->getMerchantId();
        PayU::$isTest      = false;
        if ($storeCountry == PayUCountries::BR) {
            PayU::$language = SupportedLanguages::PT;
        } else if ($storeCountry == PayUCountries::US) {
            PayU::$language = SupportedLanguages::EN;
        } else {
            PayU::$language = SupportedLanguages::ES;
        }
        Environment::$test = ($dataHelper->isSandbox()) ? true : false;

        try {
            $orderData  = $dataHelper->prepareOrderData($payment);
            $parameters = array(
                PayUParameters::DEVICE_SESSION_ID           => $payment->getAdditionalInformation('payu_device_session_id'),
                PayUParameters::ACCOUNT_ID                  => $dataHelper->getAccountId($orderData['billing_country']),
                PayUParameters::REFERENCE_CODE              => $orderData['increment_id'],
                PayUParameters::DESCRIPTION                 => $dataHelper->__('Payment relative to order #%s', $orderData['increment_id']),
                PayUParameters::VALUE                       => number_format($amount, 2, '.', ''),
                PayUParameters::CURRENCY                    => $orderData['currency_code'],
                PayUParameters::BUYER_NAME                  => $orderData['customer_name'],
                PayUParameters::BUYER_EMAIL                 => $orderData['customer_email'],
                PayUParameters::BUYER_CONTACT_PHONE         => $orderData['billing_phone'],
                PayUParameters::BUYER_DNI                   => $orderData['customer_cpf'],
                PayUParameters::BUYER_STREET                => $orderData['billing_street'],
                PayUParameters::BUYER_STREET_2              => $orderData['billing_number'],
                PayUParameters::BUYER_STREET_3              => $orderData['billing_complement'],
                PayUParameters::BUYER_CITY                  => $orderData['billing_city'],
                PayUParameters::BUYER_STATE                 => $orderData['billing_state'],
                PayUParameters::BUYER_COUNTRY               => $orderData['billing_country'],
                PayUParameters::BUYER_POSTAL_CODE           => $orderData['billing_postcode'],
                PayUParameters::BUYER_PHONE                 => $orderData['billing_phone'],
                PayUParameters::BUYER_CONTACT_PHONE         => $orderData['billing_phone'],
                PayUParameters::PAYER_NAME                  => $orderData['customer_name'], 
                PayUParameters::PAYER_EMAIL                 => $orderData['customer_email'],
                PayUParameters::PAYER_DNI                   => $orderData['customer_cpf'], 
                PayUParameters::PAYER_STREET                => $orderData['billing_street'],
                PayUParameters::PAYER_STREET_2              => $orderData['billing_number'],
                PayUParameters::PAYER_STREET_3              => $orderData['billing_complement'],
                PayUParameters::PAYER_CITY                  => $orderData['billing_city'],
                PayUParameters::PAYER_STATE                 => $orderData['billing_state'],
                PayUParameters::PAYER_COUNTRY               => $orderData['billing_country'],
                PayUParameters::PAYER_POSTAL_CODE           => $orderData['billing_postcode'],
                PayUParameters::PAYER_PHONE                 => $orderData['billing_phone'],  
                PayUParameters::PAYER_CONTACT_PHONE         => $orderData['billing_phone'],
                PayUParameters::CREDIT_CARD_NUMBER          => $payment->decrypt($payment->getAdditionalInformation('credit_card_hash')),
                PayUParameters::CREDIT_CARD_EXPIRATION_DATE => 
                    $payment->decrypt($payment->getAdditionalInformation('credit_card_exp_year')) 
                    . '/' . $payment->decrypt($payment->getAdditionalInformation('credit_card_exp_month')),
                PayUParameters::CREDIT_CARD_SECURITY_CODE   => $payment->decrypt($payment->getAdditionalInformation('credit_card_cvv')),
                PayUParameters::PAYMENT_METHOD              => $payment->getCcType(),
                PayUParameters::INSTALLMENTS_NUMBER         => $payment->getAdditionalInformation('installment_quantity'),
                PayUParameters::COUNTRY                     => $dataHelper->getStoreCountry(),
                PayUParameters::IP_ADDRESS                  => $httpHelper->getRemoteAddr(),
                PayUParameters::USER_AGENT                  => $httpHelper->getHttpUserAgent(),
                PayUParameters::NOTIFY_URL                  => $dataHelper->getNotificationURL(),
                PayUParameters::PARTNER_ID                  => $dataHelper->getPartnerId(),
            );

            $cookies = Mage::app()->getRequest()->getCookie();
            if (isset($cookies['frontend']) && !empty($cookies['frontend'])) {
                $parameters[PayUParameters::PAYER_COOKIE] = $cookies['frontend'];
            }

            $payment->unsAdditionalInformation('credit_card_hash'); // erasing card number before order creation
            $payment->unsAdditionalInformation('credit_card_exp_year');
            $payment->unsAdditionalInformation('credit_card_exp_month');
            $payment->unsAdditionalInformation('credit_card_cvv');

            $dateFormat = "d/m/Y";
            if (Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE) == 'en_US') {
                $dateFormat = "m/d/Y";
            }
            if (isset($orderData['customer_dob']) && !empty($orderData['customer_dob'])) {
                $formattedDate = DateTime::createFromFormat($dateFormat, $orderData['customer_dob']);
                $parameters[PayUParameters::PAYER_BIRTHDATE] = $formattedDate->format('Y-m-d');
            }

            // Check if juridical person
            if (!empty($orderData['customer_cnpj'])) {
                unset($parameters[PayUParameters::BUYER_DNI]);
                unset($parameters[PayUParameters::PAYER_DNI]);
                $parameters[PayUParameters::BUYER_CNPJ] = $orderData['customer_cnpj'];
                $parameters[PayUParameters::PAYER_CNPJ] = $orderData['customer_cnpj'];
                if (!empty($orderData['customer_company_name'])) {
                    $parameters[PayUParameters::BUYER_NAME] = $orderData['customer_company_name'];
                    $parameters[PayUParameters::PAYER_BUSINESS_NAME] = $orderData['customer_company_name'];
                }
            }

            $parameters[PayUParameters::PAYER_NAME] = $payment->getAdditionalInformation('credit_card_owner');
            if ($payment->getAdditionalInformation('credit_card_phone') != '') {
                $holderPhone      = preg_replace("/[^0-9]/", "", $payment->getAdditionalInformation('credit_card_phone'));
                $holderDDD        = '';
                if (strlen($holderPhone) > 8) {
                    $holderDDD    = substr($holderPhone, 0, 2);
                    $holderPhone  = substr($holderPhone, 2);
                }
                $parameters[PayUParameters::PAYER_PHONE] = '(' . $holderDDD . ')' . $holderPhone;
            }
            if ($payment->getAdditionalInformation('credit_card_cpf') != '') {
                $parameters[PayUParameters::PAYER_DNI] = $payment->getAdditionalInformation('credit_card_cpf');
            }
            if ($payment->getAdditionalInformation('credit_card_dob') != '') {
                $formattedDate = DateTime::createFromFormat($dateFormat, $payment->getAdditionalInformation('credit_card_dob'));
                $parameters[PayUParameters::PAYER_BIRTHDATE] = $formattedDate->format('Y-m-d');
            }
            
            $newInformation = $this->processPayUPayment($parameters);
            $newInformation['order_placed'] = true;
            if (!empty($newInformation)) {
                $previousInformation = $payment->getAdditionalInformation();
                if ($previousInformation && is_array($previousInformation)) {
                    $newInformation = array_merge($newInformation, $previousInformation);
                }
                $payment->setAdditionalInformation($newInformation);
            }
                
        } catch (Exception $e) {
            $message = $dataHelper->__('There was a problem processing your payment: ') . $e->getMessage();
            $dataHelper->log($message);
            Mage::throwException($message);
        }

        $payment->setSkipOrderProcessing(true);

        return $this;
    }

}
