<?php
namespace Horde\Form\V3;
use Horde_Form_Translation;

class BooleanType extends BaseType
{
    public function isValid($var, Horde_Variables|array $vars, $value): bool
    {
        if ($var->isRequired() && empty($value)) {
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
    public function about()
    {
        return [ 'name' => Horde_Form_Translation::t("Colour selection") ];
    }

}
