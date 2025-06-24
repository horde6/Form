<?php
namespace Horde\Form\V3;
use Horde_Form_Translation;

class PasswordconfirmType extends BaseType
{
    public function isValid($var, Horde_Variables|array $vars, $value): bool
    {
        if ($var->isRequired() && empty($value['original'])) {
            $message = Horde_Form_Translation::t("This field is required.");
            $this->message = $message;
            return false;
        }

        if ($value['original'] != $value['confirm']) {
            $message = Horde_Form_Translation::t("Passwords must match.");
            $this->message = $message;
            return false;
        }

        return true;
    }

    public function getInfo($vars, $var)
    {
        $value = $vars->get($var->getVarName());
        return $value['original'];
    }

    /**
     * Return info about field type.
     */
    public function about()
    {
        return [ 'name' => Horde_Form_Translation::t("Password with confirmation") ];
    }

}
