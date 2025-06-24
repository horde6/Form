<?php
namespace Horde\Form\V3;
use Horde_Form_Translation;

class BooleanType extends BaseType
{
    public function isValid($var, Horde_Variables|array $vars, $value): bool
    {
        return true;
    }

    public function getInfo($vars, $var)
    {
        return Horde_String::lower($vars->get($var->getVarName())) == 'on';
    }

    /**
     * Return info about field type.
     */
    public function about()
    {
        return [ 'name' => Horde_Form_Translation::t("True or false") ];
    }

}
