<?php
namespace Horde\Form\V3;

use Horde_Variables;
use Horde_Form_Translation;

/**
 * ColorpickerVariable type for colour selection fields.
 */
class ColorpickerVariable extends BaseVariable
{
    public function isValid(Horde_Variables $vars, $value): bool
    {
        if ($this->isRequired() && empty($value)) {
            return $this->invalid('This field is required.');
        }

        if (empty($value) || preg_match('/^#([0-9a-z]){6}$/i', $value)) {
            return true;
        }

        return $this->invalid("This field must contain a color code in the RGB Hex format, for example '#1234af'.");
    }

    /**
     * Return info about field type.
     */
    public function about(): array
    {
        return [ 'name' => Horde_Form_Translation::t("Colour selection") ];
    }
}
