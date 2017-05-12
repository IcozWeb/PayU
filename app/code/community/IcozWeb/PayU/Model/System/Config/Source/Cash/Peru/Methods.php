<?php

class IcozWeb_PayU_Model_System_Config_Source_Cash_Peru_Methods {

    const BCP = 'BCP';

    public static function toOptionArray() {
        $dataHelper = Mage::helper('icozweb_payu');
        return array(
            array('value' => self::BCP, 'label' => $dataHelper->__('BCP')),
        );
    }

}
