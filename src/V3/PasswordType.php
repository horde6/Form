<?php
namespace Horde\Form\V3;
use Horde_Form_Translation;

class PasswordType extends BaseType
{
    public function isValid($var, Horde_Variables|array $vars, $value): bool
    {
        $valid = true;

        if ($var->isRequired()) {
            $valid = strlen(trim($value)) > 0;

            if (!$valid) {
                $message = Horde_Form_Translation::t("This field is required.");
                $this->message = $message;
            }
        }

        return $valid;
    }

    /**
     * Return info about field type.
     */
    public function about()
    {
        return [ 'name' => Horde_Form_Translation::t("Password") ];
    }

}
