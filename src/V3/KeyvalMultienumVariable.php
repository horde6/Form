<?php
namespace Horde\Form\V3;
use Horde_Variables;
use Horde_Form_Translation;

class KeyvalMultienumVariable extends MultienumVariable
{
    public function getInfo($vars)
    {
        $value = $vars->get($this->getVarName());
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
