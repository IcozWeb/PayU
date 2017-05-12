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

class IcozWeb_PayU_NotificationController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
        $dataHelper = Mage::helper('icozweb_payu');
        $params     = $this->getRequest()->getParams();
        if (!$dataHelper->isSignatureValid($params)) {
            $this->noRouteAction();
            return;
        }

        // Processar notificação
        try {
            $payment = Mage::getModel('icozweb_payu/payment_method');
            $notification = $payment->preProcessNotification($params);
            $payment->processNotification($notification);

            // Informar ao PayU que a chamada foi recebida
            $this->getResponse()->setHeader('HTTP/1.0', '200', true);
        } catch (Exception $e) {
            // A exceção já é logada em outros lugares, informar um erro
            $this->getResponse()->setHeader('HTTP/1.0', '500', true);
        }
    }

    public function dispute() {
        $dataHelper = Mage::helper('icozweb_payu');
        $params     = $this->getRequest()->getParams();
        if (!isset($params['validation']) || $params['validation'] != $dataHelper->getCallbackValidation()) {
            $this->noRouteAction();
            return;
        }

        // Processar notificação
        try {
            $payment = Mage::getModel('icozweb_payu/payment_method');
            $notification = $payment->processDisputeNotification($params);

            // Informar ao PayU que a chamada foi recebida
            $this->getResponse()->setHeader('HTTP/1.0', '200', true);
        } catch (Exception $e) {
            // A exceção já é logada em outros lugares, informar um erro
            $this->getResponse()->setHeader('HTTP/1.0', '500', true);
        }

    }

}
