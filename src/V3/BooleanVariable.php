<?php
namespace Horde\Form\V3;
use Horde_Variables;
use Horde_Form_Translation;

class BooleanVariable extends BaseVariable
{
    public function isValid(Horde_Variables|array $vars, $value): bool
    {
        return true;
    }

    public function getInfo($vars)
    {
        return Horde_String::lower($vars->get($this->getVarName())) == 'on';
    }

    /**
     * Return info about field type.
     */
    public function about()
    {
        return [ 'name' => Horde_Form_Translation::t("True or false") ];
    }

}
