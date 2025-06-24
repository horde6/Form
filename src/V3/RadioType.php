<?php
namespace Horde\Form\V3;
use Horde_Form_Translation;

class RadioType extends EnumType
{
    /* Entirely implemented by Horde_Form_Type_enum; just a different
     * view. */

    /**
     * Return info about field type.
     */
    public function about()
    {
        return [
            'name' => Horde_Form_Translation::t("Radio selection"),
            'params' => [
                'values' => [
                    'label' => Horde_Form_Translation::t("Values"),
                    'type'  => 'stringarray'
                ]
            ]
        ];
    }

}
