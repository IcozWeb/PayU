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

class IcozWeb_PayU_Helper_Data extends Mage_Core_Helper_Abstract {

    #############################################
    ###       CREDENCIAIS PAYU SANDBOX        ###
    #############################################
    const SANDBOX_MERCHANT_ID                       = '508029';
    const SANDBOX_ACCOUNT_ID_AR                     = '512322';
    const SANDBOX_ACCOUNT_ID_BR                     = '512327';
    const SANDBOX_ACCOUNT_ID_CO                     = '512321';
    const SANDBOX_ACCOUNT_ID_MX                     = '512324';
    const SANDBOX_ACCOUNT_ID_PA                     = '512326';
    const SANDBOX_ACCOUNT_ID_PE                     = '512323';
    const SANDBOX_API_LOGIN                         = 'pRRXKOl8ikMmt9u';
    const SANDBOX_API_KEY                           = '4Vj8eK4rloUd272L48hsrarnUA';
    const SANDBOX_PUBLIC_KEY                        = 'PKaC6H4cEDJD919n705L544kSU';
    #############################################

    const XML_PATH_PAYMENT_PAYU_NEED_JQUERY         = 'payment/icozweb_payu/load_jquery';
    const XML_PATH_PAYMENT_PAYU_CC_ENABLED          = 'payment/icozweb_payu_cc/active';
    const XML_PATH_PAYMENT_PAYU_BOLETO_ENABLED      = 'payment/icozweb_payu_boleto/active';
    const XML_PATH_PAYMENT_PAYU_CASH_AR_ENABLED     = 'payment/icozweb_payu_cash_argentina/active';
    const XML_PATH_PAYMENT_PAYU_CASH_CO_ENABLED     = 'payment/icozweb_payu_cash_colombia/active';
    const XML_PATH_PAYMENT_PAYU_CASH_MX_ENABLED     = 'payment/icozweb_payu_cash_mexico/active';
    const XML_PATH_PAYMENT_PAYU_CASH_PE_ENABLED     = 'payment/icozweb_payu_cash_peru/active';
    const XML_PATH_PAYMENT_BOLETO_EXPIRATION        = 'payment/icozweb_payu_boleto/expiration';
    const XML_PATH_PAYMENT_PAYU_SANDBOX             = 'payment/icozweb_payu/sandbox';
    const XML_PATH_PAYMENT_PAYU_MERCHANT_ID         = 'payment/icozweb_payu/merchant_id';
    const XML_PATH_PAYMENT_PAYU_ACCOUNT_ID_AR       = 'payment/icozweb_payu/account_id_ar';
    const XML_PATH_PAYMENT_PAYU_ACCOUNT_ID_BR       = 'payment/icozweb_payu/account_id_br';
    const XML_PATH_PAYMENT_PAYU_ACCOUNT_ID_CO       = 'payment/icozweb_payu/account_id_co';
    const XML_PATH_PAYMENT_PAYU_ACCOUNT_ID_MX       = 'payment/icozweb_payu/account_id_mx';
    const XML_PATH_PAYMENT_PAYU_ACCOUNT_ID_PA       = 'payment/icozweb_payu/account_id_pa';
    const XML_PATH_PAYMENT_PAYU_ACCOUNT_ID_PE       = 'payment/icozweb_payu/account_id_pe';
    const XML_PATH_PAYMENT_PAYU_API_LOGIN           = 'payment/icozweb_payu/api_login';
    const XML_PATH_PAYMENT_PAYU_API_KEY             = 'payment/icozweb_payu/api_key';
    const XML_PATH_PAYMENT_PAYU_PUBLIC_KEY          = 'payment/icozweb_payu/public_key';
    const XML_PATH_PAYMENT_PAYU_CALLBACK_VALIDATION = 'payment/icozweb_payu/callback_validation';
    const XML_PATH_PAYMENT_PAYU_DEBUG               = 'payment/icozweb_payu/debug';
    const XML_PATH_PAYMENT_PAYU_SEND_INVOICE_EMAIL  = 'payment/icozweb_payu/send_invoice_email';
    const XML_PATH_PAYMENT_PAYU_CPF_ATTRIBUTE       = 'payment/icozweb_payu/customer_cpf_attribute';
    const XML_PATH_PAYMENT_PAYU_CNPJ_ATTRIBUTE      = 'payment/icozweb_payu/customer_cnpj_attribute';
    const XML_PATH_PAYMENT_PAYU_STREET_ATTRIBUTE    = 'payment/icozweb_payu/address_street_attribute';
    const XML_PATH_PAYMENT_PAYU_NUMBER_ATTRIBUTE    = 'payment/icozweb_payu/address_number_attribute';
    const XML_PATH_PAYMENT_PAYU_COMP_ATTRIBUTE      = 'payment/icozweb_payu/address_complement_attribute';
    const XML_PATH_PAYMENT_PAYU_COMPANY_ATTRIBUTE   = 'payment/icozweb_payu/customer_company_name_attribute';
    const XML_PATH_PAYMENT_PAYU_PARTNER_ID          = 'payment/icozweb_payu/partner_id';
    const XML_PATH_STORE_LOCALE                     = 'general/locale/code';
    const XML_PATH_PAYMENT_PAYU_JS_URL              = 'payment/icozweb_payu/js_url';

    const XML_PATH_PAYMENT_PAYU_CASH_AR_ACTIVE      = 'payment/icozweb_payu_cash_argentina/active';
    const XML_PATH_PAYMENT_PAYU_CASH_AR_METHODS     = 'payment/icozweb_payu_cash_argentina/methods';

    const PAYU_PROMOTIONS_HOST                      = 'https://api.payulatam.com';
    const SANDBOX_PAYU_PROMOTIONS_HOST              = 'https://sandbox.api.payulatam.com';

    public function isPayUEnabled() {
        return (
            Mage::getStoreConfigFlag(self::XML_PATH_PAYMENT_PAYU_CC_ENABLED) || 
            Mage::getStoreConfigFlag(self::XML_PATH_PAYMENT_PAYU_BOLETO_ENABLED) ||
            Mage::getStoreConfigFlag(self::XML_PATH_PAYMENT_PAYU_CASH_AR_ENABLED) ||
            Mage::getStoreConfigFlag(self::XML_PATH_PAYMENT_PAYU_CASH_CO_ENABLED) ||
            Mage::getStoreConfigFlag(self::XML_PATH_PAYMENT_PAYU_CASH_MX_ENABLED) ||
            Mage::getStoreConfigFlag(self::XML_PATH_PAYMENT_PAYU_CASH_PE_ENABLED)
        );
    }

    public function isMethodPayU($methodCode) {
        $validPayUMethods = array(
            'icozweb_payu_cc',
            'icozweb_payu_boleto',
            'icozweb_payu_cash_argentina',
            'icozweb_payu_cash_colombia',
            'icozweb_payu_cash_mexico',
            'icozweb_payu_cash_peru'
        );
        return in_array($methodCode, $validPayUMethods);
    }

    public function isSandbox() {
        return Mage::getStoreConfigFlag(self::XML_PATH_PAYMENT_PAYU_SANDBOX);
    }

    public function getJqueryLibrary() {
        if (Mage::getStoreConfigFlag(self::XML_PATH_PAYMENT_PAYU_NEED_JQUERY)) {
            $secure = Mage::getStoreConfigFlag('web/secure/use_in_frontend');
            return '<script type="text/javascript" src="' . Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_JS, $secure) . 'payu/jquery.min.js' . '"></script>';
        } else {
            return '';
        }
    }

    public function getMerchantId() {
        $merchantId = Mage::getStoreConfig(self::XML_PATH_PAYMENT_PAYU_MERCHANT_ID);
        if ($this->isSandbox()) {
            $merchantId = self::SANDBOX_MERCHANT_ID;
        }

        return $merchantId;
    }

    public function getAccountId($countryCode = '') {
        if ($countryCode === '') {
            $quote       = Mage::getSingleton('checkout/session')->getQuote();
            $address     = $quote ? $quote->getBillingAddress() : null;
            $countryCode = $address ? $address->getCountry() : $this->getStoreCountry(); // get default store country
        }

        $isSandbox = $this->isSandbox();
        $accountId = '';
        switch ($countryCode) {
            case 'AR':
                $accountId = $isSandbox ? self::SANDBOX_ACCOUNT_ID_AR : Mage::getStoreConfig(self::XML_PATH_PAYMENT_PAYU_ACCOUNT_ID_AR);
                break;
            case 'BR':
                $accountId = $isSandbox ? self::SANDBOX_ACCOUNT_ID_BR : Mage::getStoreConfig(self::XML_PATH_PAYMENT_PAYU_ACCOUNT_ID_BR);
                break;
            case 'CO':
                $accountId = $isSandbox ? self::SANDBOX_ACCOUNT_ID_CO : Mage::getStoreConfig(self::XML_PATH_PAYMENT_PAYU_ACCOUNT_ID_CO);
                break;
            case 'MX':
                $accountId = $isSandbox ? self::SANDBOX_ACCOUNT_ID_MX : Mage::getStoreConfig(self::XML_PATH_PAYMENT_PAYU_ACCOUNT_ID_MX);
                break;
            case 'PA':
                $accountId = $isSandbox ? self::SANDBOX_ACCOUNT_ID_PA : Mage::getStoreConfig(self::XML_PATH_PAYMENT_PAYU_ACCOUNT_ID_PA);
                break;
            case 'PE':
                $accountId = $isSandbox ? self::SANDBOX_ACCOUNT_ID_PE : Mage::getStoreConfig(self::XML_PATH_PAYMENT_PAYU_ACCOUNT_ID_PE);
                break;
            default:
                // If all else fails, default BR
                $accountId = $isSandbox ? self::SANDBOX_ACCOUNT_ID_BR : Mage::getStoreConfig(self::XML_PATH_PAYMENT_PAYU_ACCOUNT_ID_BR);
                break;
        }

        return $accountId;
    }

    public function getApiLogin() {
        $apiLogin = Mage::getStoreConfig(self::XML_PATH_PAYMENT_PAYU_API_LOGIN);
        if ($this->isSandbox()) {
            $apiLogin = self::SANDBOX_API_LOGIN;
        }

        return $apiLogin;
    }

    public function getApiKey() {
        $apiKey = Mage::getStoreConfig(self::XML_PATH_PAYMENT_PAYU_API_KEY);
        if ($this->isSandbox()) {
            $apiKey = self::SANDBOX_API_KEY;
        }

        return $apiKey;
    }

    public function getPublicKey() {
        $publicKey = Mage::getStoreConfig(self::XML_PATH_PAYMENT_PAYU_PUBLIC_KEY);
        if ($this->isSandbox()) {
            $publicKey = self::SANDBOX_PUBLIC_KEY;
        }

        return $publicKey;
    }

    public function setCallbackValidation($callbackValidation) {
        $mageConfig = Mage::getConfig();
        $mageConfig->saveConfig(self::XML_PATH_PAYMENT_PAYU_CALLBACK_VALIDATION, $callbackValidation);
        $mageConfig->reinit();
        Mage::app()->reinitStores();
    }

    public function getCallbackValidation() {
        $callbackValidation = Mage::getStoreConfig(self::XML_PATH_PAYMENT_PAYU_CALLBACK_VALIDATION);
        if ($callbackValidation == '') {
            $salt = hash('md5', rand());
            $callbackValidation = hash('sha256', $salt . Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB));
            $this->setCallbackValidation($callbackValidation);
        }

        return $callbackValidation;
    }

    public function isDebugActive() {
        return Mage::getStoreConfigFlag(self::XML_PATH_PAYMENT_PAYU_DEBUG);
    }

    public function sendInvoiceEmail() {
        return Mage::getStoreConfigFlag(self::XML_PATH_PAYMENT_PAYU_SEND_INVOICE_EMAIL);
    }

    public function getBoletoExpiration() {
        return Mage::getStoreConfig(self::XML_PATH_PAYMENT_BOLETO_EXPIRATION);
    }

    public function getAvailableCashMethods($code) {
        $classPath         = str_replace('icozweb_payu_', '', $code);
        $optionSource      = Mage::getModel('icozweb_payu/system_config_source_' . $classPath . '_methods');
        $allOptions        = $optionSource->toOptionArray();
        $selectedOptions   = explode(',', Mage::getStoreConfig('payment/' . $code . '/methods'));
        $optionsWithLabels = array();
        foreach ($allOptions as $option) {
            if (in_array($option['value'], $selectedOptions)) {
                $optionsWithLabels[$option['value']] = $option['label'];
            }
        }

        return $optionsWithLabels;
    }

    public function getStoreCountry() {
        $storeLocale = Mage::getStoreConfig(self::XML_PATH_STORE_LOCALE);
        $country     = substr($storeLocale, -2);
        return $country;
    }

    public function getPayUPromotionsHost() {
        if ($this->isSandbox()) {
            return self::SANDBOX_PAYU_PROMOTIONS_HOST;
        } else {
            return self::PAYU_PROMOTIONS_HOST;
        }
    }

    public function getNotificationURL() {
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . 'payu/notification/index';
    }

    public function getDisputeNotificationURL() {
        $callbackValidation = $this->getCallbackValidation();
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . 'payu/notification/dispute/validation/' . $callbackValidation;
    }

    public function getInstallmentsURL() {
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . 'payu/installments/ajax';
    }

    public function getInstallments($cardType = 'VISA') {
        $accountId     = $this->getAccountId();
        if (empty($accountId)) {
            $this->log('Ocorreu um problema ao calcular as parcelas: account_id não foi preenchido para o país da cotação.');
            return '';
        }

        $apiKey        = $this->getApiKey();
        $publicKey     = $this->getPublicKey();
        $date          = gmdate("D, d M Y H:i:s", time()) ." GMT";
        $method        = "GET";
        $url           = "/payments-api/rest/v4.3/pricing";
        $contentToSign = utf8_encode($method . "\n" . "\n" . "\n" . $date  . "\n" . $url);
        $signature     = base64_encode(hash_hmac('sha256', $contentToSign, $apiKey, true));
        $quote         = Mage::getModel('checkout/session')->getQuote();
        $currency      = $quote->getQuoteCurrencyCode();
        $amount        = $quote->getGrandTotal();
        $query         = '?accountId='     . $accountId .
                         '&currency='      . $currency  .
                         '&amount='        . $amount    .
                         '&paymentMethod=' . $cardType;
        $ch            = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->getPayUPromotionsHost() . $url . $query);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, 
            array(
                'Authorization: Hmac ' . $publicKey . ':' . $signature, 
                'Content-Type: application/xml',
                'Accept: application/xml',
                'Date: ' . $date
            )
        );
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response == '') {
            $this->log('Nenhum XML de parcelas foi recebido do PayU.');
            return '';
        }

        libxml_use_internal_errors(true);
        $sxe = simplexml_load_string($response);
        if (!$sxe) {
            $this->log('O XML de parcelas recebido do PayU é inválido: ' . print_r($response, true));
            return '';
        }

        $xml = new SimpleXMLElement($response);

        if (!isset($xml->paymentMethodFee->paymentMethodFeeDetail->pricingFees->fee) ||
            count($xml->paymentMethodFee->paymentMethodFeeDetail->pricingFees->fee) <= 0) {
            $this->log('O XML de parcelas recebido do PayU é inválido: ' . print_r($response, true));
            return '';
        }

        $coreHelper   = Mage::helper('core');
        $installments = '<option value="">' . $this->__('-- Please, select --') . '</option>';
        foreach ($xml->paymentMethodFee->paymentMethodFeeDetail->pricingFees->fee as $fee) {
            if (!isset($fee->pricing->totalValue)) {
                $this->log('Valor total no XML de parcelamento é inválido.');
                return '';
            }
            $totalValue        = floatval($fee->pricing->totalValue);
            $installmentNumber = $fee['installments'];
            $installmentValue  = $totalValue / floatval($installmentNumber);
            $interest          = $this->__(' without interest');
            if (isset($fee->pricing->payerDetail->total) && floatval($fee->pricing->payerDetail->total) > 0) {
                $interest      = $this->__(' with interest');
            }

            $installments .= '<option value="' . $installmentNumber . '|' . $installmentValue . '">' . 
                                $installmentNumber . $this->__('x of ') . $coreHelper->formatCurrency($installmentValue) . $interest . 
                             '</option>';

        }

        return $installments;
    }

    public function getPayULibraryScriptBlock() {
        $payUCardValidation = Mage::app()->getLayout()
            ->createBlock('icozweb_payu/form_payu_card_validation')
            ->toHtml();
        $payULibrary = Mage::app()->getLayout()->createBlock('core/text', 'payu_js');
        $secure = Mage::getStoreConfigFlag('web/secure/use_in_frontend');
        $payULibrary->setText(
            sprintf('
                %s
                <script type="text/javascript" src="%s"></script>
                <script type="text/javascript" src="%s"></script>
                %s',
                $this->getJqueryLibrary(),
                Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA, $secure) . 'payu/cached_library.js', //Mage::getStoreConfig(self::XML_PATH_PAYMENT_PAYU_JS_URL),
                Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_JS, $secure) . 'payu/payu.js',
                $payUCardValidation
            )
        );
        return $payULibrary;
    }

    public function isSignatureValid($params) {
        if (!isset($params['merchant_id']) || !isset($params['reference_sale']) || 
            !isset($params['value'])       || !isset($params['currency'])       || 
            !isset($params['state_pol'])   || !isset($params['sign'])) {
            return false;
        }
        $apiKey        = $this->getApiKey();
        $merchantId    = $params['merchant_id'];
        $referenceSale = $params['reference_sale'];
        $currency      = $params['currency'];
        $state         = $params['state_pol'];
        $signature     = $params['sign'];
        $value         = (string)$params['value'];
        $separator     = strpos($value, '.');
        if ($separator !== false) {
            $secondDecimal = substr($value, $separator + 2, 1);
            if ($secondDecimal == '0') {
                $value = number_format($params['value'], 1, '.', '');
            }
        }
        return ($signature == md5($apiKey . '~' . $merchantId . '~' . $referenceSale . '~' . $value . '~' . $currency . '~' . $state));
    }

    /**
     * Extract order data from Payment object
     * @param Varien_Object $payment
     *
     * @return Array
     */
    public function prepareOrderData($payment) {
        $order = $payment->getOrder();
        $customer = $order->getCustomer();
        $billingAddress = $order->getBillingAddress();
        $shippingAddress = $order->getShippingAddress();
        $orderData = array();
        $orderData['increment_id'] = $order->getIncrementId();
        $orderData['currency_code'] = $order->getOrderCurrencyCode();
        if ($order->getShippingAmount() > 0) {
            $orderData['shipping_amount'] = number_format($order->getShippingAmount(), 2, '.', '');
        }
        if ((-$order->getDiscountAmount()) > 0) {
            $orderData['discount_amount'] = number_format((-$order->getDiscountAmount()), 2, '.', '');
        }
        $orderData['items'] = array();
        foreach ($order->getAllVisibleItems() as $product) {
            $item = array(
                'name'  => $product->getName(),
                'qty'   => $product->getQtyOrdered(),
                'sku'   => $product->getSku(),
                'price' => number_format($product->getPrice(), 2, '.', '')
            );
            $orderData['items'][] = $item;
        }


        // Customer data
        $orderData['customer_id']           = $order->getCustomerId();
        $orderData['customer_email']        = strtolower($order->getCustomerEmail());
        $orderData['customer_name']         = $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname();
        $orderData['customer_cpf']          = $customer->getData($this->getCustomerCpfAttribute());
        $orderData['customer_company_name'] = $customer->getData($this->getCustomerCompanyNameAttribute());
        $orderData['customer_cnpj']         = preg_replace("/[^0-9]/", "", $customer->getData($this->getCustomerCnpjAttribute()));
        if ($order->getCustomerDob() != '') {
            $orderData['customer_dob']      = Mage::app()->getLocale()->date($order->getCustomerDob(), null, null, false)->toString('dd/MM/Y');
        }
        

        // Billing Address data
        $orderData['billing_street']       = $this->getStreetValue($billingAddress);
        $orderData['billing_number']       = $this->getNumberValue($billingAddress);
        $orderData['billing_complement']   = $this->getComplementValue($billingAddress);
        //$orderData['billing_neighborhood'] = $billingAddress->getStreet(4);
        $orderData['billing_city']         = $billingAddress->getCity();
        $orderData['billing_state']        = $billingAddress->getRegionCode();
        $orderData['billing_postcode']     = substr(preg_replace('/[^0-9]/', '', $billingAddress->getPostcode()), 0, 8);
        $orderData['billing_country']      = $billingAddress->getCountry();
        $billingDDD = '';
        $billingPhone = preg_replace("/[^0-9]/", "", $billingAddress->getTelephone());
        if (strlen($billingPhone) > 8) {
            $billingDDD   = substr($billingPhone, 0, 2);
            $billingPhone = substr($billingPhone, 2);
            $billingPhone = '(' . $billingDDD . ')' . $billingPhone;
        }
        $orderData['billing_phone']        = $billingPhone;
        

        // Shipping Address data
        if ($order->getIsNotVirtual()) {
            $orderData['shipping_street']       = $this->getStreetValue($shippingAddress);
            $orderData['shipping_number']       = $this->getNumberValue($shippingAddress);
            $orderData['shipping_complement']   = $this->getComplementValue($shippingAddress);
            //$orderData['shipping_neighborhood'] = $shippingAddress->getStreet(4);
            $orderData['shipping_city']         = $shippingAddress->getCity();
            $orderData['shipping_state']        = $shippingAddress->getRegionCode();
            $orderData['shipping_postcode']     = substr(preg_replace('/[^0-9]/', '', $shippingAddress->getPostcode()), 0, 8);
            $orderData['shipping_country']      = $shippingAddress->getCountry();
            $shippingDDD = '';
            $shippingPhone = preg_replace("/[^0-9]/", "", $shippingAddress->getTelephone());
            if (strlen($shippingPhone) > 8) {
                $shippingDDD   = substr($shippingPhone, 0, 2);
                $shippingPhone = substr($shippingPhone, 2);
                $shippingPhone = '(' . $shippingDDD . ')' . $shippingPhone;
            }
            $orderData['shipping_phone']        = $shippingPhone;
        } else {
            $orderData['shipping_street']       = $orderData['billing_street'];
            $orderData['shipping_number']       = $orderData['billing_number'];
            $orderData['shipping_neighborhood'] = $orderData['billing_neighborhood'];
            $orderData['shipping_city']         = $orderData['billing_city'];
            $orderData['shipping_state']        = $orderData['billing_state'];
            $orderData['shipping_postcode']     = $orderData['billing_postcode'];
            $orderData['shipping_complement']   = $orderData['billing_complement'];
            $orderData['shipping_country']      = $orderData['billing_country'];
            $orderData['shipping_phone']        = $orderData['billing_phone'];
        }

        return $orderData;
    }

    public function log($object) {
        if ($this->isDebugActive()) {
            if (is_string($object)) {
                Mage::log($object, null, 'payu.log', true);
            } else {
                Mage::log(var_export($object, true), null, 'payu.log', true);
            }
        }
    }

    public function getCustomerCpfAttribute() {
        return Mage::getStoreConfig(self::XML_PATH_PAYMENT_PAYU_CPF_ATTRIBUTE);
    }

    public function getCustomerCnpjAttribute() {
        return Mage::getStoreConfig(self::XML_PATH_PAYMENT_PAYU_CNPJ_ATTRIBUTE);
    }

    public function getCustomerCompanyNameAttribute() {
        return Mage::getStoreConfig(self::XML_PATH_PAYMENT_PAYU_COMPANY_ATTRIBUTE);
    }

    public function getStreetValue($address) {
        $streetAttribute = Mage::getStoreConfig(self::XML_PATH_PAYMENT_PAYU_STREET_ATTRIBUTE);
        $matches = array();
        if (preg_match_all('/street_(\d)/', $streetAttribute, $matches)) {
            return $address->getStreet($matches[1][0]);
        } else {
            return $address->getData($streetAttribute);
        }
    }

    public function getNumberValue($address) {
        $numberAttribute = Mage::getStoreConfig(self::XML_PATH_PAYMENT_PAYU_NUMBER_ATTRIBUTE);
        $matches = array();
        if (preg_match_all('/street_(\d)/', $numberAttribute, $matches)) {
            return $address->getStreet($matches[1][0]);
        } else {
            return $address->getData($numberAttribute);
        }
    }

    public function getComplementValue($address) {
        $complementAttribute = Mage::getStoreConfig(self::XML_PATH_PAYMENT_PAYU_COMP_ATTRIBUTE);
        $matches = array();
        if (preg_match_all('/street_(\d)/', $complementAttribute, $matches)) {
            return $address->getStreet($matches[1][0]);
        } else {
            return $address->getData($complementAttribute);
        }
    }

    public function getPartnerId() {
        return Mage::getStoreConfig(self::XML_PATH_PAYMENT_PAYU_PARTNER_ID);
    }

}
