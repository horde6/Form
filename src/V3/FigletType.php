<?php
namespace Horde\Form\V3;
use Horde_Form_Translation;

class FigletType extends BaseType
{
    public $_text;
    public $_font;

    /**
     * Initialize a Figlet form type
     *
     * function init($text, $font)
     */
    public function init(...$params)
    {
        $this->_text = $params[0];
        $this->_font = $params[1];
    }

    public function isValid($var, Horde_Variables|array $vars, $value): bool
    {
        if (empty($value) && $var->isRequired()) {
            return $this->invalid('This field is required.');
        }

        if (Horde_String::lower($value) != Horde_String::lower($this->_text)) {
            return $this->invalid('The text you entered did not match the text on the screen.');
        }

        return true;
    }

    public function getFont()
    {
        return $this->_font;
    }

    public function getText()
    {
        return $this->_text;
    }

    /**
     * Return info about field type.
     */
    public function about()
    {
        return [
            'name' => Horde_Form_Translation::t("Figlet CAPTCHA"),
            'params' => [
                'text' => [
                    'label' => Horde_Form_Translation::t("Text"),
                    'type'  => 'text'
                ],
                'font' => [
                    'label' => Horde_Form_Translation::t("Figlet font"),
                    'type'  => 'text'
                ]
            ]
        ];
    }

}
