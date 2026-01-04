<?php
namespace Horde\Form\V3;
use Horde_Variables;
use Horde_Form_Translation;

class PasswordVariable extends BaseVariable
{
    public function isValid(Horde_Variables $vars, $value): bool
    {
        if ($this->isRequired() && strlen(trim($value)) == 0) {
            return $this->invalid('This field is required.');
        }

        return true;
    }

    /**
     * Return info about field type.
     */
    public function about(): array
    {
        return [ 'name' => Horde_Form_Translation::t("Password") ];
    }

}
