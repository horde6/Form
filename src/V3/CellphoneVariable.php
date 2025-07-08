<?php
namespace Horde\Form\V3;
use Horde_Variables;
use Horde_Form_Translation;

class CellphoneVariable extends PhoneVariable
{
    public function about(): array
    {
        return [
            'name' => Horde_Form_Translation::t("Mobile phone number"),
            'params' => [
                'size' => [
                    'label' => Horde_Form_Translation::t("Size"),
                    'type'  => 'int'
                ],
            ],
        ];
    }
}
