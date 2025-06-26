<?php
namespace Horde\Form\V3;
use Horde_Form_Translation;

class SetType extends BaseType
{
    public $_values;
    public $_checkAll = false;

    /**
     * Initialize a Set form type
     *
     * function init($values, $checkAll = false)
     */
    public function init(...$params)
    {
        if (is_array($params) && array_key_exists('values', $params)) {
            $this->_values = $params['values'];
        } else {
            $this->_values = $params[0];
        }
        if (is_array($params) && array_key_exists('checkAll', $params)) {
            $this->_checkAll = (bool) $params['checkAll'];
        } else {
            $this->_checkAll = $params[1] ?? false;
        }
    }

    public function isValid($var, Horde_Variables|array $vars, $value): bool
    {
        if ((!is_null($this->_values) && count($this->_values) == 0) || is_null($value) || count($value) == 0) {
            return true;
        }

        foreach ($value as $item) {
            if (!isset($this->_values[$item])) {
                return $this->invalid('Invalid data submitted.');
            }
        }

        return true;

    }

    public function getValues(...$params)
    {
        return $this->_values;
    }

    /**
     * Return info about field type.
     */
    public function about()
    {
        return [
            'name' => Horde_Form_Translation::t("Set"),
            'params' => [
                'values' => [
                    'label' => Horde_Form_Translation::t("Values"),
                    'type'  => 'stringarray'
                ]
            ]
        ];
    }

}
