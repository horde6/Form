<?php
namespace Horde\Form\V3;
use Horde_Variables;
use Horde_Form_Translation;

class AddresslinkVariable extends AddressVariable
{
    public function isValid(Horde_Variables $vars, $value): bool
    {
        return true;
    }

    /**
     * Return info about field type.
     */
    public function about(): array
    {
        return [ 'name' => Horde_Form_Translation::t("Address Link") ];
    }

}
