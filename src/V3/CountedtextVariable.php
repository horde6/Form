<?php
namespace Horde\Form\V3;
use Horde_Variables;
use Horde_String;
use Horde_Form_Translation;

class CountedtextVariable extends LongtextVariable
{
    public $_chars;

    /**
     * Init a longtext field
     *
     * function init($rows = null, $cols = null, $chars = 1000)
     */
    public function init(...$params)
    {
        $rows = $params[0] ?? null;
        $cols = $params[1] ?? null;
        $chars = $params[2] ?? 1000;
        parent::init($rows, $cols);
        $this->_chars = $chars;
    }

    public function isValid(Horde_Variables $vars, $value): bool
    {
        $length = Horde_String::length(trim($value));

        if ($this->isRequired() && $length <= 0) {
            return $this->invalid('This field is required.');
        }

        if ($length > $this->_chars) {
            $this->message = sprintf(Horde_Form_Translation::ngettext("There are too many characters in this field. You have entered %d character; ", "There are too many characters in this field. You have entered %d characters; ", $length), $length)
                . sprintf(Horde_Form_Translation::t("you must enter less than %d."), $this->_chars);
            return false;
        }

        return true;
    }

    /**
     * Return info about field type.
     */
    public function about(): array
    {
        return [
            'name' => Horde_Form_Translation::t("Counted text"),
            'params' => [
                'rows'  => [
                    'label' => Horde_Form_Translation::t("Number of rows"),
                    'type'  => 'int'
                ],
                'cols'  => [
                    'label' => Horde_Form_Translation::t("Number of columns"),
                    'type'  => 'int'
                ],
                'chars' => [
                    'label' => Horde_Form_Translation::t("Number of characters"),
                    'type'  => 'int'
                ]
            ]
        ];
    }

}
