<?php
namespace Horde\Form\V3;
use Horde_Form_Translation;

class EmailconfirmType extends BaseType
{
    public function isValid($var, Horde_Variables|array $vars, $value): bool
    {
        if ($var->isRequired() && empty($value['original'])) {
            return $this->invalid('This field is required.');
        }

        if ($value['original'] != $value['confirm']) {
            return $this->invalid('Email addresses must match.');
        }

        $addr_ob = $GLOBALS['injector']->getInstance('Horde_Mail_Rfc822')->parseAddressList($value['original']);

        switch (count($addr_ob)) {
            case 0:
                return $this->invalid('You did not enter a valid email address.');

            case 1:
                return true;

            default:
                return $this->invalid('Only one email address allowed.');
        }
    }

    /**
     * Return info about field type.
     */
    public function about()
    {
        return [ 'name' => Horde_Form_Translation::t("Email with confirmation") ];
    }

}
