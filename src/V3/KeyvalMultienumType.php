<?php
namespace Horde\Form\V3;
use Horde_Form_Translation;

class KeyvalMultienumType extends MultienumType
{
   public function getInfo($vars, $var)
    {
        $value = $vars->get($var->getVarName());
        $info = [];
        foreach ($value as $key) {
            $info[$key] = $this->_values[$key];
        }
        return $info;
    }

    /**
     * Return info about field type.
     */
    public function about()
    {
        $about = parent::about();
        $about['name'] = Horde_Form_Translation::t("Multiple selection, preserving keys");
    }

}
