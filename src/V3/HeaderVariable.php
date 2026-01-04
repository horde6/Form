<?php
namespace Horde\Form\V3;
use Horde_Variables;
use Horde_Form_Translation;

class HeaderVariable extends BaseVariable
{
    public function isValid(Horde_Variables $vars, $value): bool
    {
        return true;
    }

    public function about(): array
    {
        return [ 'name' => Horde_Form_Translation::t("Header") ];
    }

}
