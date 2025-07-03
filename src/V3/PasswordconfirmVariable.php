<?php
namespace Horde\Form\V3;
use Horde_Variables;
use Horde_Form_Translation;

class PasswordconfirmVariable extends BaseVariable
{
    public function isValid(Horde_Variables|array $vars, $value): bool
    {
        if ($this->isRequired() && empty($value['original'])) {
            return $this->invalid('This field is required.');
        }

        if ($value['original'] != $value['confirm']) {
            return $this->invalid('Passwords must match.');
        }

        return true;
    }

    public function getInfo($vars)
    {
        $value = $vars->get($this->getVarName());
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
