<?php
namespace Horde\Form\V3;
use Horde_Variables;
use Horde_Form_Translation;

class CaptchaVariable extends FigletVariable
{
    /**
     * Return info about field type.
     */
    public function about()
    {
        return [
            'name' => Horde_Form_Translation::t("Image CAPTCHA"),
            'params' => [
                'text' => [
                    'label' => Horde_Form_Translation::t("Text"),
                    'type'  => 'text'
                ],
                'font' => [
                    'label' => Horde_Form_Translation::t("Font"),
                    'type'  => 'text'
                ]
            ]
        ];
    }
}
