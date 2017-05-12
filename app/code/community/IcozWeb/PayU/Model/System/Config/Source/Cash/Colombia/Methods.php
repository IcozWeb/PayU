<?php

class IcozWeb_PayU_Model_System_Config_Source_Cash_Colombia_Methods {

    const BALOTO = 'BALOTO';
    const EFECTY = 'EFECTY';


    public static function toOptionArray() {
        $dataHelper = Mage::helper('icozweb_payu');
        return array(
            array('value' => self::BALOTO, 'label' => $dataHelper->__('Baloto')),
            array('value' => self::EFECTY, 'label' => $dataHelper->__('Efecty')),
        );
    }

}
