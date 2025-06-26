<?php
namespace Horde\Form\V3;
use Horde_Form_Translation;

class CategoryType extends BaseType
{
    public function getInfo($vars, $var)
    {
        $info = $var->getValue($vars);
        if ($info == '*new*') {
            $info = [
                'new' => true,
                'value' => $vars->get('new_category')
            ];
        } else {
            $info = [
                'new' => false,
                'value' => $info
            ];
        }
        return $info;
    }

    /**
     * Return info about field type.
     */
    public function about()
    {
        return [ 'name' => Horde_Form_Translation::t("Category") ];
    }

    public function isValid($var, Horde_Variables|array $vars, $value): bool
    {
        if (empty($value) && $var->isRequired()) {
            return $this->invalid('This field is required.');
        }

        return true;
    }

}
