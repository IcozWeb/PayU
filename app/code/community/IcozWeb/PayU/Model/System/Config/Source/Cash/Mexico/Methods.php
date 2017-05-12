<?php

class IcozWeb_PayU_Model_System_Config_Source_Cash_Mexico_Methods {

    const SANTANDER    = 'SANTANDER';
    const SCOTIABANK   = 'SCOTIABANK';
    const BANCOMER     = 'BANCOMER';
    const OXXO         = 'OXXO';
    const SEVEN_ELEVEN = 'SEVEN_ELEVEN';

    public static function toOptionArray() {
        $dataHelper = Mage::helper('icozweb_payu');
        return array(
            array('value' => self::SANTANDER,    'label' => $dataHelper->__('Santander')),
            array('value' => self::SCOTIABANK,   'label' => $dataHelper->__('Scotiabank')),
        	array('value' => self::BANCOMER,     'label' => $dataHelper->__('Bancomer')),
        	array('value' => self::OXXO,         'label' => $dataHelper->__('Oxxo')),
            array('value' => self::SEVEN_ELEVEN, 'label' => $dataHelper->__('7-Eleven')),
        );
    }

}
