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
(function() {
    document.observe("dom:loaded", function() {
        validateCreditCardPayU = function(event) {
            ajaxURL = event.data.installmentsAjaxUrl;
            number = jQuery('#icozweb_payu_cc_cc_number').val();
            cvv = jQuery('#icozweb_payu_cc_cc_cvv').val();
            month = jQuery('#icozweb_payu_cc_expiration_month').val();
            year = jQuery('#icozweb_payu_cc_expiration_yr').val();
            typeElement = jQuery('#icozweb_payu_cc_credit_card_type');
            installmentsElement = jQuery('#icozweb_payu_cc_cc_installments');

            payU.setListBoxID('payu-card-image', '');
            payU.getPaymentMethods();

            if (number.length < 6 || cvv.length < 3 || month.length == 0 || year.length == 0) {
                typeElement.val('');
                //installmentsElement.attr('disabled', 'disabled');
                //installmentsElement.val('');
                //installmentsElement.trigger('chosen:updated');
                return;
            }

            payU.showPaymentMethods(true);
            payU.showLabel(true);

            // "VISA"||"MASTERCARD"||"AMEX"||"DINERS"||"ELO"||"HIPERCARD"
            type = payU.cardPaymentMethod(number);
            typeChanged = false;
            if (typeof type !== 'undefined' && type != null && type != 'desconhecido') {
                if (typeElement.val() != type) {
                    typeChanged = true;
                    typeElement.val(type);
                }
            } else {
                typeElement.val('');
                //installmentsElement.attr('disabled', 'disabled');
                //installmentsElement.val('');
                //installmentsElement.trigger('chosen:updated');
                return;
            }

            var isValid = payU.validateCard(number) && payU.validateExpiry(year, month);
            
            if (isValid) {
                if (typeChanged) {
                    jQuery.ajax({
                        url: ajaxURL,
                        data: {cardType: type},
                        dataType: 'text',
                    }).success(function(installmentsHTML, status, jqXHR) {
                        if (installmentsHTML != '') {
                            installmentsElement.html(installmentsHTML);
                            installmentsElement.removeAttr('disabled');
                            installmentsElement.trigger('chosen:updated');
                        }
                    });
                } else {
                    installmentsElement.removeAttr('disabled');
                    installmentsElement.trigger('chosen:updated');
                }
            } else {
                //installmentsElement.attr('disabled', 'disabled');
                //installmentsElement.val('');
                //installmentsElement.trigger('chosen:updated');
            }
        }

        PayUTransparente = function(extraParameters) {
            jQuery("#checkout-step-payment").on('change', '#icozweb_payu_cc_cc_number', extraParameters, validateCreditCardPayU);
            jQuery("#checkout-step-payment").on('change', '#icozweb_payu_cc_cc_cvv', extraParameters, validateCreditCardPayU);
            jQuery("#checkout-step-payment").on('change', '#icozweb_payu_cc_expiration_month', extraParameters, validateCreditCardPayU);
            jQuery("#checkout-step-payment").on('change', '#icozweb_payu_cc_expiration_yr', extraParameters, validateCreditCardPayU);
        }
    });
}());