<?php

namespace Mijora\VenipakCod\Model\Source;

class FeeType implements \Magento\Framework\Option\ArrayInterface {
   
    /**
     * Returns array to be used in multiselect on back-end
     *
     * @return array
     */
    public function toOptionArray() {
        $arr = [
            ['value' => 'percent', 'label' => 'Percent'],
            ['value' => 'fixed', 'label' => 'Fixed price'],
        ];    
        return $arr;
    }
}
