<?php
namespace Horde\Form\V3;

use Horde_Variables;
use Horde_Form_Translation;

/**
 * IntVariable type for integer input fields.
 */
class IntVariable extends BaseVariable
{
    /**
     * Validates integer field value.
     *
     * Checks that the value contains only digits (0-9). Required fields
     * must have a non-empty value. Empty optional fields pass validation.
     *
     * @param Horde_Variables $vars  Form variables
     * @param mixed $value           Field value to validate
     *
     * @return bool  True if valid, false with error message set if invalid
     */
    public function isValid(Horde_Variables $vars, $value): bool
    {
        if ($this->isRequired() && empty($value) && ((string) (int) $value !== $value)) {
            return $this->invalid('This field is required.');
        }

        if (empty($value) || preg_match('/^[0-9]+$/', $value)) {
            return true;
        }

        return $this->invalid('This field may only contain integers.');
    }

    /**
     * Return info about field type.
     */
    public function about(): array
    {
        return [ 'name' => Horde_Form_Translation::t("Integer") ];
    }
}
