<?php
namespace Horde\Form\V3;
use Horde_Variables;
use Horde_Form_Translation;

class ObrowserVariable extends BaseVariable
{
    public function isValid(Horde_Variables|array $vars, $value): bool
    {
        return true;
    }

    /**
     * Return info about field type.
     */
    public function about(): array
    {
        return [ 'name' => Horde_Form_Translation::t("Relationship browser") ];
    }

}
