<?php
namespace Horde\Form\V3;
use Horde_Form_Translation;

/**
 * Horde_Form_Type for selecting a single value out of a list
 * For selecting multiple values, use Horde_Form_Type_multienum
 */
class EnumType extends BaseType
{
    public $_values;
    public $_prompt;
    /**
     * Initialize (kind of constructor)
     *
     * function init($values, $prompt = null)
     *
     * @param array $values            A hash map where the key is the internal 'value' to process and the value is the caption presented to the user
     * @param string|bool  $prompt  A null value text to prompt user selecting a value. Use a default if boolean true, else use the supplied string. No prompt on false.
     */
    public function init(...$params)
    {
        $this->setValues($params[0] ?? []);
        $prompt = $params[1] ?? false;

        if ($prompt === true) {
            $this->_prompt = Horde_Form_Translation::t("-- select --");
        } else {
            $this->_prompt = $prompt;
        }
    }

     public function isValid($var, Horde_Variables|array $vars, $value): bool
    {
        if ($var->isRequired() && $value == '' && !isset($this->_values[$value])) {
            $message = Horde_Form_Translation::t("This field is required.");
            $this->message = $message;
            return false;
        }

        if (count($this->_values) == 0 || isset($this->_values[$value]) ||
            ($this->_prompt && empty($value))) {
            return true;
        }

        $message = Horde_Form_Translation::t("Invalid data submitted.");
        $this->message = $message;
        return false;
    }

    public function getValues(...$params)
    {
        return $this->_values;
    }

    public function setValues($values)
    {
        $this->_values = $values;
    }

    public function getPrompt()
    {
        return $this->_prompt;
    }

    /**
     * Return info about field type.
     */
    public function about()
    {
        return [
            'name' => Horde_Form_Translation::t("Drop down list"),
            'params' => [
                'values' => [
                    'label' => Horde_Form_Translation::t("Values to select from"),
                    'type'  => 'stringarray'
                ],
                'prompt' => [
                    'label' => Horde_Form_Translation::t("Prompt text"),
                    'type'  => 'text'
                ]
            ]
        ];
    }

}
