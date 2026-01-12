<?php
namespace Horde\Form\V3;

use Horde_Variables;
use Horde_Form_Translation;

/**
 * SpacerVariable type for visual spacing in forms.
 */
class SpacerVariable extends BaseVariable
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
        return [
            'name' => Horde_Form_Translation::t("Spacer")
        ];
    }
}
