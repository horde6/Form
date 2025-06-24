<?php
namespace Horde\Form\V3;
use Horde_Form_Translation;

class TimeType extends BaseType
{
    public function isValid($var, Horde_Variables|array $vars, $value): bool
    {
        if ($var->isRequired() && empty($value) && ((string) (float) $value !== $value)) {
            $message = Horde_Form_Translation::t("This field is required.");
            $this->message = $message;
            return false;
        }

        if (empty($value) || preg_match('/^[0-2]?[0-9]:[0-5][0-9]$/', $value)) {
            return true;
        }

        $message = Horde_Form_Translation::t("This field may only contain numbers and the colon.");
        $this->message = $message;
        return false;
    }

    /**
     * Return info about field type.
     */
    public function about()
    {
        return [ 'name' => Horde_Form_Translation::t("Time") ];
    }

}
