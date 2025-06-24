<?php
namespace Horde\Form\V3;
use Horde_Form_Translation;

class HtmlType extends BaseType
{
    public function isValid($var, Horde_Variables|array $vars, $value): bool
    {
        return true;
    }

    public function about(): array
    {
        return ['name' => Horde_Form_Translation::t("HTML")];
    }
}
