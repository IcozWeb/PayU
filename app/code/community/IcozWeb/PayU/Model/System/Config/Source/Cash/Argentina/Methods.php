<?php

class IcozWeb_PayU_Model_System_Config_Source_Cash_Argentina_Methods {

    const COBRO_EXPRESS = 'COBRO_EXPRESS';
    const PAGOFACIL     = 'PAGOFACIL';
    const RAPIPAGO      = 'RAPIPAGO';
    const BAPRO         = 'BAPRO';
    const RIPSA         = 'RIPSA';

    public static function toOptionArray() {
        $dataHelper = Mage::helper('icozweb_payu');
        return array(
            array('value' => self::COBRO_EXPRESS, 'label' => $dataHelper->__('Cobro Express')),
            array('value' => self::PAGOFACIL,     'label' => $dataHelper->__('Pago Fácil')),
        	array('value' => self::RAPIPAGO,      'label' => $dataHelper->__('Rapipago')),
        	array('value' => self::BAPRO,         'label' => $dataHelper->__('BAPRO')),
            array('value' => self::RIPSA,         'label' => $dataHelper->__('RIPSA')),
        );
    }

}
