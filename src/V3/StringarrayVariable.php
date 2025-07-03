<?php
namespace Horde\Form\V3;
use Horde_Variables;
use Horde_Form_Translation;

class StringarrayVariable extends StringlistVariable
{
    public function getInfo($vars)
    {
        return array_map('trim', explode(',', $vars->get($this->getVarName())));
    }

    /**
     * Return info about field type.
     */
    public function about():array
    {
        return [
            'name' => Horde_Form_Translation::t("String list returning an array"),
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
