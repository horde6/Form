<?php
namespace Horde\Form\V3;
use Horde_Variables;
use Horde_Form_Translation;

class OctalVariable extends BaseVariable
{
    public function isValid(Horde_Variables $vars, $value): bool
    {
        if ($this->isRequired() && empty($value) && ((string) (int) $value !== $value)) {
            return $this->invalid('This field is required.');
        }

        if (empty($value) || preg_match('/^[0-7]+$/', $value)) {
            return true;
        }

        return $this->invalid('This field may only contain octal values.');
    }

    /**
     * Return info about field type.
     */
    public function about(): array
    {
        return [ 'name' => Horde_Form_Translation::t("Octal") ];
    }

}
