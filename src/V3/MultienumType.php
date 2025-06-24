<?php
namespace Horde\Form\V3;
use Horde_Form_Translation;

class MultienumType extends EnumType
{
    public $size = 5;

    /**
     * Initialize (kind of constructor)
     *
     * function init($values, $size = null)
     *
     * @param array $values  A hash map where the key is the internal 'value' to process and the value is the caption presented to the user
     * @param int $size  The number of rows the multienum should display before scrolling
     */
    public function init(...$params)
    {
        $values = $params[0] ?? [];
        $size = $params[1] ?? null;

        if (!is_null($size)) {
            $this->size = (int) $size;
        }

        parent::init($values);
    }

    public function isValid($var, Horde_Variables|array $vars, $value): bool
    {
        if (is_array($value)) {
            foreach ($value as $val) {
                if (!$this->isValid($var, $vars, $val)) {
                    return false;
                }
            }
            return true;
        }

        if (empty($value) && ((string) (int) $value !== $value)) {
            if ($var->isRequired()) {
                $message = Horde_Form_Translation::t("This field is required.");
                $this->message = $message;
                return false;
            } else {
                return true;
            }
        }

        if (count($this->_values) == 0 || isset($this->_values[$value])) {
            return true;
        }

        $message = Horde_Form_Translation::t("Invalid data submitted.");
        $this->message = $message;
        return false;
    }

    /**
     * Return info about field type.
     */
    public function about()
    {
        return [
            'name' => Horde_Form_Translation::t("Multiple selection"),
            'params' => [
                'values' => [
                    'label' => Horde_Form_Translation::t("Values"),
                    'type'  => 'stringarray'
                ],
                'size'   => [
                    'label' => Horde_Form_Translation::t("Size"),
                    'type'  => 'int'
                ]
            ],
        ];
    }

}
