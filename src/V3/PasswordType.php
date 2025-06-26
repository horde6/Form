<?php
namespace Horde\Form\V3;
use Horde_Form_Translation;

class PasswordType extends BaseType
{
    public function isValid($var, Horde_Variables|array $vars, $value): bool
    {
        if ($var->isRequired() && strlen(trim($value)) == 0) {
            return $this->invalid('This field is required.');
        }

        return true;
    }

    /**
     * Return info about field type.
     */
    public function about()
    {
        return [ 'name' => Horde_Form_Translation::t("Password") ];
    }

}
