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

class IcozWeb_PayU_Model_Payment_Method extends Mage_Payment_Model_Method_Abstract {

    const PAYU_ORDER_STATUS_NEW             = 'NEW';          // O pedido acaba de ser criada no sistema.
    const PAYU_ORDER_STATUS_IN_PROGRESS     = 'IN_PROGRESS';  // O pedido está sendo processado.
    const PAYU_ORDER_STATUS_AUTHORIZED      = 'AUTHORIZED';   // A última transação do pedido foi uma autorização aprovada.
    const PAYU_ORDER_STATUS_CAPTURED        = 'CAPTURED';     // A última transação do pedido foi uma captura aprovada.
    const PAYU_ORDER_STATUS_CANCELLED       = 'CANCELLED';    // A última transação do pedido foi um cancelamento aprovado.
    const PAYU_ORDER_STATUS_DECLINED        = 'DECLINED';     // A última transação do pedido foi rejeitada.
    const PAYU_ORDER_STATUS_REFUNDED        = 'REFUNDED';     // A última transação do pedido foi um reembolso aprovado.

    // Enviado pelo SDK
    const PAYU_TRANSACTION_STATE_APPROVED   = 'APPROVED';     // Transação aprovada.
    const PAYU_TRANSACTION_STATE_DECLINED   = 'DECLINED';     // Transação rejeitada.
    const PAYU_TRANSACTION_STATE_ERROR      = 'ERROR';        // Erro processando a transação.
    const PAYU_TRANSACTION_STATE_EXPIRED    = 'EXPIRED';      // Transação expirada.
    const PAYU_TRANSACTION_STATE_PENDING    = 'PENDING';      // Transação pendente ou em validação.
    const PAYU_TRANSACTION_STATE_SUBMITTED  = 'SUBMITTED';    // Transação enviada para a entidade financeira e por algum motivo não terminou seu processamento.

    // Enviado pelo sistema de notificação
    const PAYU_STATE_POL_APPROVED           = 4;
    const PAYU_STATE_POL_EXPIRED            = 5;
    const PAYU_STATE_POL_REJECTED           = 6;

    // Enviado pelo sistema de notificação de disputas
    const PAYU_DISPUTE_STATE_NOTIFIED       = 'NOTIFIED';
    const PAYU_DISPUTE_STATE_REVIEW         = 'ON_REVIEW';
    const PAYU_DISPUTE_STATE_LOST           = 'LOST';
    const PAYU_DISPUTE_STATE_WON            = 'WON';
    const PAYU_DISPUTE_STATE_REFUNDED       = 'REFUNDED';
    const PAYU_DISPUTE_STATE_EXPIRED        = 'EXPIRED';



    protected $_isGateway                   = true;
    protected $_isInitializeNeeded          = true;
    protected $_canAuthorize                = true;
    protected $_canCapture                  = false;
    protected $_canRefund                   = true;
    protected $_canRefundInvoicePartial     = false;
    protected $_canVoid                     = true;
    protected $_canUseInternal              = true;
    protected $_canUseCheckout              = true;
    protected $_canUseForMultishipping      = true;
    protected $_canSaveCc                   = false;



    /*
                    (== PayUTransactionResponseCode::)
    state_pol       response_message_pol                                    response_code_pol   Description
    4 (Aprovada)    APPROVED                                                1                   Transação aprovada
    
    5 (Expirada)    EXPIRED_TRANSACTION                                     20                  Transação expirada
    
    6 (Rejeitada)   PAYMENT_NETWORK_REJECTED                                4                   Transação rejeitada por entidade financeira
                    ENTITY_DECLINED                                         5                   Transação rejeitada pelo banco
                    INSUFFICIENT_FUNDS                                      6                   Fundos insuficientes
                    INVALID_CARD                                            7                   Cartão inválido
                    CONTACT_THE_ENTITY                                      8                   Entrar em contacto com a entidade financeira
                    BANK_ACCOUNT_ACTIVATION_ERROR                           8                   Débito automático não permitido
                    BANK_ACCOUNT_NOT_AUTHORIZED_FOR_AUTOMATIC_DEBIT         8                   Débito automático não permitido
                    INVALID_AGENCY_BANK_ACCOUNT                             8                   Débito automático não permitido
                    INVALID_BANK_ACCOUNT                                    8                   Débito automático não permitido
                    INVALID_BANK                                            8                   Débito automático não permitido
                    EXPIRED_CARD                                            9                   Cartão vencido
                    RESTRICTED_CARD                                         10                  Cartão restrito
                    INVALID_EXPIRATION_DATE_OR_SECURITY_CODE                12                  Data de expiração ou código de segurança inválidos
                    REPEAT_TRANSACTION                                      13                  Nova tentativa de pagamento
                    INVALID_TRANSACTION                                     14                  Transação inválida
                    EXCEEDED_AMOUNT                                         17                  O valor excede o máximo permitido pela entidade
                    ABANDONED_TRANSACTION                                   19                  Transação abandonada pelo pagador
                    CREDIT_CARD_NOT_AUTHORIZED_FOR_INTERNET_TRANSACTIONS    22                  Cartão não autorizado para comprar pela internet
                    ANTIFRAUD_REJECTED                                      23                  Transação rejeitada por suspeita de fraude
                    DIGITAL_CERTIFICATE_NOT_FOUND                           9995                Certificado digital não encontrado
                    BANK_UNREACHABLE                                        9996                Erro tratando de entrar em contato com o banco
                    PAYMENT_NETWORK_NO_CONNECTION                           9996                Não foi possível estabelecer comunicação com a entidade financeira
                    PAYMENT_NETWORK_NO_RESPONSE                             9996                Não se recebeu resposta da entidade financeira
                    ENTITY_MESSAGING_ERROR                                  9997                Erro entrando em contato com a entidade financeira
                    NOT_ACCEPTED_TRANSACTION                                9998                Transação não permitida
                    INTERNAL_PAYMENT_PROVIDER_ERROR                         9999                Erro
                    INACTIVE_PAYMENT_PROVIDER                               9999                Erro
                    Erro                                                    9999                Erro
                    ERROR_CONVERTING_TRANSACTION_AMOUNTS                    9999                Erro
                    BANK_ACCOUNT_ACTIVATION_ERROR                           9999                Erro
                    FIX_NOT_REQUIRED                                        9999                Erro
                    AUTOMATICALLY_FIXED_AND_SUCCESS_REVERSAL                9999                Erro
                    AUTOMATICALLY_FIXED_AND_UNSUCCESS_REVERSAL              9999                Erro
                    AUTOMATIC_FIXED_NOT_SUPPORTED                           9999                Erro
                    NOT_FIXED_FOR_ERROR_STATE                               9999                Erro
                    ERROR_FIXING_AND_REVERSING                              9999                Erro
                    ERROR_FIXING_INCOMPLETE_DATA                            9999                Erro
                    PAYMENT_NETWORK_BAD_RESPONSE                            9999                Erro
    */


    /**
     * Check whether payment method can be used
     *
     * @param Mage_Sales_Model_Quote|null $quote
     *
     * @return bool
     */
    public function isAvailable($quote = null) {
        $isAvailable = parent::isAvailable($quote);
        if (empty($quote) || !Mage::getStoreConfigFlag("payment/" . $this->getCode() . "/group_restriction")) {
            return $isAvailable;
        }

        $currentGroupID = $quote->getCustomerGroupId();
        $allowedGroups  = explode(',', $this->_getStoreConfig('customer_groups'));
        if ($isAvailable && in_array($currentGroupID, $allowedGroups)) {
            return true;
        }

        return false;
    }

    /**
     * Do a manual check with the PayU query API in order to update order status.
     *
     * @param string $orderId
     *
     */
    public function checkOrderPayUStatus($orderId) {
        $dataHelper        = Mage::helper('icozweb_payu');
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

        $order       = Mage::getModel('sales/order')->load($orderId);
        $payment     = $order->getPayment();
        $methodCode  = $payment->getMethodInstance()->getCode();
        if (!$dataHelper->isMethodPayU($methodCode)) {
            return false;
        }

        $payUOrderId = $payment->getAdditionalInformation('payu_order_id');
        $parameters  = array(PayUParameters::ORDER_ID => $payUOrderId);
        $payUOrder   = PayUReports::getOrderDetail($parameters);

        if (!$payUOrder) {
            Mage::throwException($dataHelper->__('No data returned from PayU.'));
        }

        $orderUpdateTriggerStates = array(
            self::PAYU_TRANSACTION_STATE_APPROVED  => array(
                Mage_Sales_Model_Order::STATE_PENDING_PAYMENT,
            ),
            self::PAYU_TRANSACTION_STATE_DECLINED  => array(
                Mage_Sales_Model_Order::STATE_PENDING_PAYMENT
            ),
            self::PAYU_TRANSACTION_STATE_ERROR     => array(
                Mage_Sales_Model_Order::STATE_PENDING_PAYMENT
            ),
            self::PAYU_TRANSACTION_STATE_EXPIRED   => array(
                Mage_Sales_Model_Order::STATE_PENDING_PAYMENT
            ),
            self::PAYU_TRANSACTION_STATE_PENDING   => array(
                Mage_Sales_Model_Order::STATE_PROCESSING
            ),
            self::PAYU_TRANSACTION_STATE_SUBMITTED => array(
                Mage_Sales_Model_Order::STATE_PROCESSING
            ),
        );
        $paymentTransactionId = $payment->getAdditionalInformation('payu_transaction_id');
        $transactions         = $payUOrder->transactions;
        $transactionFound     = false;
        $orderUpdated         = false;
        $orderState           = $order->getState();
        foreach ($transactions as $transaction) {
            if ($transaction->id == $paymentTransactionId) {
                $transactionFound = true;
                $transactionState = $transaction->transactionResponse->state;
                if (in_array($orderState, $orderUpdateTriggerStates[$transactionState])) {
                    $orderUpdated                   = true;
                    $notification                   = new stdClass();
                    $notification->state            = $transactionState;
                    $notification->orderIncrementID = $order->getIncrementId();
                    $notification->responseCode     = $transaction->transactionResponse->responseCode;
                    $notification->responseMessage  = isset($transaction->transactionResponse->responseMessage) ? $transaction->transactionResponse->responseMessage : '';
                    $this->processNotification($notification);
                }
            }
        }

        if (!$transactionFound) {
            Mage::throwException($dataHelper->__('No transaction was found for this order on PayU with the provided transaction ID: ' . $paymentTransactionId));
        }

        return $orderUpdated;
    }


    /**
     * Preprocess a new payment event from PayU
     * @param array $params
     *
     * @return stdClass $notification
     */
    public function preProcessNotification($params) {
        $dataHelper = Mage::helper('icozweb_payu');
        if (!isset($params['reference_sale']) || empty($params['reference_sale'])) {
            $dataHelper->log('Erro ao pré-processar a notificação. Não foi possível encontrar a referência do pedido: ' . print_r($params, true));
            Mage::throwException($dataHelper->__('Error pre-processing the notification. Unable to find order reference.'));
        }
        if (!isset($params['state_pol']) || empty($params['state_pol'])) {
            $dataHelper->log('Erro ao pré-processar a notificação. Não há estado da transação: ' . print_r($params, true));
            Mage::throwException($dataHelper->__('Error pre-processing the notification. Transaction has no state.'));
        }

        // Converting state representation
        $state = self::PAYU_TRANSACTION_STATE_ERROR;
        switch ($params['state_pol']) {
            case self::PAYU_STATE_POL_APPROVED:
                $state = self::PAYU_TRANSACTION_STATE_APPROVED;
                break;
            case self::PAYU_STATE_POL_EXPIRED:
                $state = self::PAYU_TRANSACTION_STATE_EXPIRED;
                break;
            case self::PAYU_STATE_POL_REJECTED:
                $state = self::PAYU_TRANSACTION_STATE_DECLINED;
                break;
            default:
                break;
        }

        $notification                      = new stdClass();
        $notification->state               = $state;
        //$notification->payUOrderID         = $params['reference_pol']; //???
        //$notification->payUTransactionID   = $params['transaction_id']; //???
        $notification->orderIncrementID    = $params['reference_sale'];
        $notification->responseCode        = isset($params['response_code_pol']) ? $params['response_code_pol'] : ''; // PayUTransactionResponseCode::xxx
        $notification->responseMessage     = '';

        return $notification;
    }

    public function processDisputeNotification($params) {
        $dataHelper = Mage::helper('icozweb_payu');
        if (!isset($params['reference']) || empty($params['reference'])) {
            $dataHelper->log('Erro ao processar a notificação de disputa. Não foi possível encontrar a referência do pedido: ' . print_r($params, true));
            Mage::throwException($dataHelper->__('Error processing the dispute notification. Unable to find order reference.'));
        }
        if (!isset($params['state']) || empty($params['state'])) {
            $dataHelper->log('Erro ao processar a notificação de disputa. Não há estado da transação: ' . print_r($params, true));
            Mage::throwException($dataHelper->__('Error processing the dispute notification. Transaction has no state.'));
        }

        $disputeState = $params['state'];
        $order        = Mage::getModel('sales/order')->loadByIncrementId($params['reference']);
        if (!$order->getId()) {
            $dataHelper->log('Notificação de disputa inválida. Não foi possível encontrar a referência do pedido: ' . print_r($params, true));
            Mage::throwException($dataHelper->__('Invalid dispute notification. Unable to find order reference: %s', $params['reference']));
        }


        try {
            $payment          = $order->getPayment();
            $customerNotified = false;
            $stateChanged     = false;
            $reason           = isset($params['reason']) ? ' (' . $params['reason'] . ')' : '';
            $comment          = isset($params['comment']) ? ' ' . $params['comment'] : '';
            $message          = '';
            switch($disputeState) {
                case self::PAYU_DISPUTE_STATE_NOTIFIED:
                    $message          = $dataHelper->__('Dispute started on PayU.') . $reason . $comment;
                    $customerNotified = true;
                    break;
                case self::PAYU_DISPUTE_STATE_REVIEW:
                    $message          = $dataHelper->__('Dispute on review.') . $reason . $comment;
                    break;
                case self::PAYU_DISPUTE_STATE_EXPIRED:
                    $message          = $dataHelper->__('Dispute expired.') . $reason . $comment;
                    $customerNotified = true;
                    break;
                case self::PAYU_DISPUTE_STATE_WON:
                    $message          = $dataHelper->__('Dispute resolved. PayU granted a favorable decision to the store owner. Refund will not be made.');
                    $customerNotified = true;
                    break;
                case self::PAYU_DISPUTE_STATE_LOST:
                    $message          = $dataHelper->__('Refunded completed. PayU granted a favorable decision to the customer.');
                    if ($order->canUnhold()) {
                        $order->unhold();
                    }
                    try {
                        if ($order->canCancel()) {
                            $order->cancel();
                            $order->save();
                        } else if ($order->canCreditmemo()) {
                            $payment->registerRefundNotification($params['value']); // Essa função faz toda a mágica do credit memo
                        } else {
                            Mage::throwException('O pedido estava num estado inesperado (' . $order->getState() . ').');
                        }
                    } catch (Exception $e) {
                        $stateChanged = true;
                        $message     .= $dataHelper->__(' However, the order was in an unexpected state. The system has forced a change to the correct state.'); 
                        $dataHelper->log('Forçando estado interno para Fechado, pois ocorreu um problema ao reembolsar o pedido #' . $order->getIncrementId() . ': ' . $e->getMessage());
                    }
                    break;
                case self::PAYU_DISPUTE_STATE_REFUNDED:
                    $message          = $dataHelper->__('Refunded completed. The store owner conceded to the customer request.');
                    if ($order->canUnhold()) {
                        $order->unhold();
                    }
                    try {
                        if ($order->canCancel()) {
                            $order->cancel();
                            $order->save();
                        } else if ($order->canCreditmemo()) {
                            $payment->registerRefundNotification($params['value']); // Essa função faz toda a mágica do credit memo
                        } else {
                            Mage::throwException($dataHelper->__('The order was in an unexpected state (%s)', $order->getState()));
                        }
                    } catch (Exception $e) {
                        $stateChanged = true;
                        $message     .= $dataHelper->__(' However, the order was in an unexpected state. The system has forced a change to the correct state.');
                        $dataHelper->log('Forçando estado interno para Fechado, pois ocorreu um problema ao reembolsar o pedido #' . $order->getIncrementId() . ': ' . $e->getMessage());
                    }
                    break;
                default:
                    $message          = $dataHelper->__('Invalid dispute notification. Unknown state received from PayU: %s', $disputeState);
                    break;
            }

            if ($stateChanged) {
                $defaultStatus = Mage::getModel('sales/order_status')->loadDefaultByState(Mage_Sales_Model_Order::STATE_CLOSED);
                $order->setData('state', Mage_Sales_Model_Order::STATE_CLOSED);
                $history = $order->addStatusHistoryComment($message, $defaultStatus);
                $history
                    ->setIsVisibleOnFront(false)
                    ->setIsCustomerNotified(false);
            } else {
                $history = $order->addStatusHistoryComment($message);
                $history
                    ->setIsVisibleOnFront($customerNotified)
                    ->setIsCustomerNotified($customerNotified);
            }
            
            $payment->save();
            $order->save();

        } catch (Exception $e) {
            $dataHelper->log('Ocorreu um problema ao processar a notificação de disputa do PayU: ' . $e->getMessage());
            Mage::throwException($dataHelper->__('There was a problem processing the dispute notification from PayU: unable to save the order correctly.'));
        }
    }

    /**
     * Process a new notification from PayU
     * @param stdClass $notification
     *
     */
    public function processNotification($notification) { 
        $dataHelper = Mage::helper('icozweb_payu');  
        $order      = Mage::getModel('sales/order')->loadByIncrementId($notification->orderIncrementID);
        if (!$order->getId()) {
            Mage::helper('icozweb_payu')->log('Notificação inválida. Não foi possível encontrar a referência do pedido: ' . print_r($notification, true));
            Mage::throwException($dataHelper->__('Invalid notification. Unable to find order reference.'));
        }
        
        $payment            = $order->getPayment();
        $this->_code        = $payment->getMethod();
        $isCreditCard       = ($this->getCode() == 'icozweb_payu_cc');
        $state              = Mage_Sales_Model_Order::STATE_PENDING_PAYMENT;
        $stateChanged       = true;
        $customerNotified   = true;
        $message            = '';
        $notificationState  = $notification->state;
        switch ($notificationState) {
            case self::PAYU_TRANSACTION_STATE_PENDING:
            case self::PAYU_TRANSACTION_STATE_SUBMITTED:
                //$stateChanged     = false;
                $customerNotified = false;
                $message          = $dataHelper->__('Waiting: the transaction has been started and PayU is waiting for payment confirmation.');
                break;
            case self::PAYU_TRANSACTION_STATE_APPROVED:
                $state            = Mage_Sales_Model_Order::STATE_PROCESSING;
                $message          = $dataHelper->__('Authorized: payment has been performed by the customer and confirmed by PayU with the financial institution responsible.');
                if (!$order->hasInvoices()) {
                    $invoice = $order->prepareInvoice();
                    $invoice->register()->pay();
                    $invoiceComment = $dataHelper->__('Payment received successfully. PayU Order ID: %s', $payment->getAdditionalInformation('payu_order_id'));
                    $invoice->addComment($invoiceComment);
                    $invoice->sendEmail($dataHelper->sendInvoiceEmail(), $invoiceComment);
                    Mage::getModel('core/resource_transaction')
                        ->addObject($invoice)
                        ->addObject($invoice->getOrder())
                        ->save();
                    $order->addStatusHistoryComment($dataHelper->__('Invoice #%s created successfully.', $invoice->getIncrementId()));
                }
                break;
            case self::PAYU_TRANSACTION_STATE_DECLINED:
                $state            = Mage_Sales_Model_Order::STATE_CANCELED;
                $message          = $dataHelper->__('Canceled: payment has not been performed. (%s) %s', $notification->responseCode, $notification->responseMessage);
                if ($order->canUnhold()) {
                    $order->unhold();
                }
                if ($order->canCancel()) {
                    $order->cancel();
                }
                break;
            case self::PAYU_TRANSACTION_STATE_EXPIRED:
                $state            = Mage_Sales_Model_Order::STATE_CANCELED;
                $message          = $dataHelper->__('Expired: PayU transaction has expired. (%s) %s', $notification->responseCode, $notification->responseMessage);
                if ($order->canUnhold()) {
                    $order->unhold();
                }
                if ($order->canCancel()) {
                    $order->cancel();
                }
                break;
            case self::PAYU_TRANSACTION_STATE_ERROR:
                $state            = Mage_Sales_Model_Order::STATE_CANCELED;
                $message          = $dataHelper->__('Error: the PayU transaction has encountered an error. (%s) %s', $notification->responseCode, $notification->responseMessage);
                if ($order->canUnhold()) {
                    $order->unhold();
                }
                if ($order->canCancel()) {
                    $order->cancel();
                }
                break;
            default:
                $stateChanged     = false;
                $customerNotified = false;
                $message          = $dataHelper->__('Unknown event received from PayU: %s', $notificationState);
                break;
        }

        try {
            if ($stateChanged) {
                $order->setState($state, true, $message, $customerNotified);
            } else {
                $history = $order->addStatusHistoryComment($message);
                $history
                    ->setIsVisibleOnFront($customerNotified)
                    ->setIsCustomerNotified($customerNotified);
            }
            
            $payment->save();
            $order->save();
        } catch (Exception $e) {
            $dataHelper->log('Ocorreu um problema ao processar a notificação do PayU: ' . $e->getMessage());
            Mage::throwException($dataHelper->__('There was a problem processing the notification from PayU: unable to save the order correctly.'));
        }
    }

    /**
     * Method that will be executed instead of authorize or capture
     * if flag isInitializeNeeded set to true
     *
     * @param string $paymentAction
     * @param object $stateObject
     *
     * @return IcozWeb_PayU_Model_Payment_Method
     */
    public function initialize($paymentAction, $stateObject) {
        $stateObject->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT);
        $stateObject->setStatus($this->getConfigData('order_status'));
        $stateObject->setIsNotified(false);
        return $this;
    }

    /**
     * Uses PayU API to create a new payment
     * @param Varien_Object $payment
     * @param string $paymentMethod
     * @param float $amount
     *
     * @return array $parameters
     */
    public function prepareParameters($payment, $paymentMethod, $amount) {
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
            PayUParameters::PAYMENT_METHOD              => $paymentMethod,
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

        if (isset($orderData['customer_dob']) && !empty($orderData['customer_dob'])) {
            $format = "d/m/Y";
            if (Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE) == 'en_US') {
                $format = "m/d/Y";
            }
            $formattedDate = DateTime::createFromFormat($format, $orderData['customer_dob']);
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

        return $parameters;
    }


    /**
     * Uses PayU API to create a new payment
     * @param array $parameters
     *
     * @return stdClass $response
     */
    public function processPayUPayment($parameters) {
        //
        // Create and send payment
        //
        $response = PayUPayments::doAuthorizationAndCapture($parameters);
        if (!$response || !isset($response->transactionResponse->state)) {
            Mage::throwException(Mage::helper('icozweb_payu')->__('No response received from PayU.'));
        }

        //
        // Process payment result
        //
        //$state = $response->transactionResponse->state;
        //if ($state != self::PAYU_TRANSACTION_STATE_APPROVED) {
            $state = self::PAYU_TRANSACTION_STATE_PENDING;  // Apenas para indicar o início da transação, a notificação chega posteriormente
        //}
        $notification                      = new stdClass();
        $notification->state               = $state;
        //$notification->payUOrderID         = $response->transactionResponse->orderId;
        //$notification->payUTransactionID   = $response->transactionResponse->transactionId;
        $notification->orderIncrementID    = $parameters[PayUParameters::REFERENCE_CODE];
        $notification->responseCode        = $response->transactionResponse->responseCode; // PayUTransactionResponseCode::xxx
        $notification->responseMessage     = isset($response->transactionResponse->responseMessage) ? $response->transactionResponse->responseMessage : '';
        $this->processNotification($notification);

        $newInformation = array();

        // General parameters
        if (isset($response->transactionResponse->orderId) && !empty($response->transactionResponse->orderId)) {
            $newInformation['payu_order_id'] = (string)$response->transactionResponse->orderId;
        }
        if (isset($response->transactionResponse->transactionId) && !empty($response->transactionResponse->transactionId)) {
            $newInformation['payu_transaction_id'] = (string)$response->transactionResponse->transactionId;
        }
        if (isset($response->transactionResponse->extraParameters->EXPIRATION_DATE) && !empty($response->transactionResponse->extraParameters->EXPIRATION_DATE)) {
            $newInformation['expiration'] = (string)$response->transactionResponse->extraParameters->EXPIRATION_DATE;
        }

        // Boleto
        if (isset($response->transactionResponse->extraParameters->URL_BOLETO_BANCARIO) && !empty($response->transactionResponse->extraParameters->URL_BOLETO_BANCARIO)) {
            $newInformation['boleto_url'] = (string)$response->transactionResponse->extraParameters->URL_BOLETO_BANCARIO;
        }

        // Cash 
        if (isset($response->transactionResponse->extraParameters->URL_PAYMENT_RECEIPT_HTML) && !empty($response->transactionResponse->extraParameters->URL_PAYMENT_RECEIPT_HTML)) {
            $newInformation['cash_payment_receipt_html'] = (string)$response->transactionResponse->extraParameters->URL_PAYMENT_RECEIPT_HTML;
        }
        if (isset($response->transactionResponse->extraParameters->REFERENCE) && !empty($response->transactionResponse->extraParameters->REFERENCE)) {
            $newInformation['cash_payment_reference'] = (string)$response->transactionResponse->extraParameters->REFERENCE;
        }

        return $newInformation;
    }


    /**
     * Refund the amount via PayU
     *
     * @param Varien_Object $payment
     * @param decimal $amount
     * @return IcozWeb_PayU_Model_Payment_Method
     * @throws Mage_Core_Exception
     */
    public function refund(Varien_Object $payment, $amount) {
        $hasPendingRequest = ($payment->hasAdditionalInformation('has_pending_refund_request') && $payment->getAdditionalInformation('has_pending_refund_request'));
        $state             = self::PAYU_TRANSACTION_STATE_PENDING;
        $message           = '';
        $dataHelper        = Mage::helper('icozweb_payu');

        if ($hasPendingRequest) {
            $parameters = array(
                PayUParameters::ORDER_ID => $payment->getAdditionalInformation('payu_order_id')
            );

            $order = PayUReports::getOrderDetail($parameters); 
            if ($order) {
                $order->status;
                $approvedCapture = false;
                $approvedRefund  = false;
                $declinedRefund  = false;
                $declinedMessage = '';
                $transactions    = $order->transactions;
                foreach ($transactions as $transaction) {
                    if ($transaction->type == TransactionType::AUTHORIZATION_AND_CAPTURE || $transaction->type == TransactionType::CAPTURE) {
                        if ($transaction->transactionResponse->state == self::PAYU_TRANSACTION_STATE_APPROVED) {
                            $approvedCapture = true;
                        }
                    } else if ($transaction->type == TransactionType::REFUND) {
                        if ($transaction->transactionResponse->state == self::PAYU_TRANSACTION_STATE_APPROVED) {
                            $approvedRefund = true;
                        } else if ($transaction->transactionResponse->state == self::PAYU_TRANSACTION_STATE_DECLINED) {
                            $declinedRefund  = true;
                            $declinedMessage = $transaction->transactionResponse->responseCode;
                        }
                    }
                }

                if ($order->status == self::PAYU_ORDER_STATUS_CAPTURED) {
                    if ($declinedRefund) {
                        $state   = self::PAYU_TRANSACTION_STATE_DECLINED;
                        $message = $declinedMessage;
                    } else if ($approvedCapture) {
                        // Wait more, nothing to change.
                    } else {
                        // Theoretically unreachable case. Captured order without any approved captured transactions. Keep waiting anyway.
                    }
                } else if ($order->status == self::PAYU_ORDER_STATUS_REFUNDED) {
                    if ($approvedRefund) {
                        $state = self::PAYU_TRANSACTION_STATE_APPROVED;
                        // Nothing else to do.
                    } else {
                        // Theoretically unreachable case. Refunded order without any approved refund transactons.
                        // Set state anyway in order to process credit memo and reflect what happened on PayU. 
                        $state = self::PAYU_TRANSACTION_STATE_APPROVED;
                        $dataHelper->log('Pedido no PayU está reembolsado, mas não possui nenhuma transação de reembolso aprovada: ' . print_r($order->transactions, true));
                    }
                } else {
                    // Error, unable to refund order in this state.
                    $dataHelper->log('Tentativa de reembolso de um pedido num estado inválido no PayU: ' . $order->status);
                    $payment->unsAdditionalInformation('has_pending_refund_request');
                    $payment->save();
                    Mage::throwException($dataHelper->__('The PayU order is in a state on which refund is not possible: %s', $order->status));
                }
            }
        } else {
            $parameters = array(
                PayUParameters::ORDER_ID       => $payment->getAdditionalInformation('payu_order_id'),
                PayUParameters::TRANSACTION_ID => $payment->getAdditionalInformation('payu_transaction_id'),
                PayUParameters::REASON         => $dataHelper->__('Refund requested via administrative dashboard.'),
            );

            $response = PayUPayments::doRefund($parameters);
            if (!$response || !isset($response->transactionResponse->state)) {
                Mage::throwException($dataHelper->__('No valid response received from PayU when requesting the refund.'));
            }

            $state   = $response->transactionResponse->state;
            $message = (isset($response->transactionResponse->responseMessage)) ? $response->transactionResponse->responseMessage : '';
        }
            
        switch ($state) {
            case self::PAYU_TRANSACTION_STATE_APPROVED:
                // Nothing else to do.
                break;
            case self::PAYU_TRANSACTION_STATE_DECLINED:
                Mage::throwException($dataHelper->__('The refund request has been declined by PayU for the following reason: ') . $message);
                break;
            case self::PAYU_TRANSACTION_STATE_ERROR:
                Mage::throwException($dataHelper->__('Please, try again. There was a problem requesting the refund from PayU: ') . $message);
                break;
            case self::PAYU_TRANSACTION_STATE_EXPIRED:
                Mage::throwException($dataHelper->__('The refund request with PayU has expired. Please, try again to send a new request.'));
                break;
            case self::PAYU_TRANSACTION_STATE_PENDING:
            case self::PAYU_TRANSACTION_STATE_SUBMITTED:
                // Esperar
                $payment->setAdditionalInformation('has_pending_refund_request', true);
                $payment->save();
                Mage::throwException($dataHelper->__('The refund request has been created on PayU, but has not yet been approved. Try again later for a new verification.'));
                break;
            default:
                Mage::throwException($dataHelper->__('The refund request on PayU has returned with an unknown state: (%s) %s', $state, $message));
                break;
        }

        if ($hasPendingRequest) {
            $payment->unsAdditionalInformation('has_pending_refund_request');
            $payment->save();
        }

        return $this;
    }

}
