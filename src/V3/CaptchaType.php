<?php
namespace Horde\Form\V3;
use Horde_Form_Translation;

class CaptchaType extends FigletType
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
