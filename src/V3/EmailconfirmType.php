<?php
namespace Horde\Form\V3;
use Horde_Form_Translation;

class EmailconfirmType extends BaseType
{
    public function isValid($var, Horde_Variables|array $vars, $value): bool
    {
        if ($var->isRequired() && empty($value['original'])) {
            $message = Horde_Form_Translation::t("This field is required.");
            $this->message = $message;
            return false;
        }

        if ($value['original'] != $value['confirm']) {
            $message = Horde_Form_Translation::t("Email addresses must match.");
            $this->message = $message;
            return false;
        }

        $addr_ob = $GLOBALS['injector']->getInstance('Horde_Mail_Rfc822')->parseAddressList($value['original']);

        switch (count($addr_ob)) {
            case 0:
                $message = Horde_Form_Translation::t("You did not enter a valid email address.");
                $this->message = $message;
                return false;

            case 1:
                return true;

            default:
                $message = Horde_Form_Translation::t("Only one email address allowed.");
                $this->message = $message;
                return false;
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
