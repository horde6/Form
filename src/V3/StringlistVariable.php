<?php
namespace Horde\Form\V3;
use Horde_Variables;
use Horde_Form_Translation;

class StringlistVariable extends TextVariable
{
    /**
     * Return info about field type.
     */
    public function about(): array
    {
        return [
            'name' => Horde_Form_Translation::t("String list"),
            'params' => [
                'regex'     => [
                    'label' => Horde_Form_Translation::t("Regex"),
                    'type'  => 'text'
                ],
                'size'      => [
                    'label' => Horde_Form_Translation::t("Size"),
                    'type'  => 'int'
                ],
                'maxlength' => [
                    'label' => Horde_Form_Translation::t("Maximum length"),
                    'type'  => 'int'
                ]
            ],
        ];
    }

}
