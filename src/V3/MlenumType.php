<?php
namespace Horde\Form\V3;
use Horde_Form_Translation;

class MlenumType extends BaseType
{
    public $_values;
    public $_prompts;

    /**
     * Initialize an mlenum field
     *
     * function init($values, $prompts = null)
     */
    public function init(...$params)
    {
        $this->_values = &$params[0];
        $prompts = $params[1] ?? null;

        if ($prompts === true) {
            $this->_prompts = [Horde_Form_Translation::t("-- select --"), Horde_Form_Translation::t("-- select --")];
        } elseif (!is_array($prompts)) {
            $this->_prompts = [$prompts, $prompts];
        } else {
            $this->_prompts = $prompts;
        }
    }

    /**
     *     function onSubmit($var, $vars)
     */
    public function onSubmit(...$params)
    {
        $var = $params[0];
        $vars = $params[1];

        $varname = $var->getVarName();
        $value = $vars->get($varname);

        if ($value['1'] != $value['old']) {
            $var->form->setSubmitted(false);
        }
    }

    public function isValid($var, Horde_Variables|array $vars, $value): bool
    {
        if ($var->isRequired() && (empty($value['1']) || empty($value['2']))) {
            $message = Horde_Form_Translation::t("This field is required.");
            $this->message = $message;
            return false;
        }

        if (!count($this->_values) || isset($this->_values[$value['1']]) ||
            (!empty($this->_prompts) && empty($value['1']))) {
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

    public function getPrompts()
    {
        return $this->_prompts;
    }

    public function getInfo($vars, $var, $info)
    {
        $info = $vars->get($var->getVarName());
        return $info['2'];
    }

    /**
     * Return info about field type.
     */
    public function about()
    {
        return [
            'name' => Horde_Form_Translation::t("Multi-level drop down lists"),
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
