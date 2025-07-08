<?php
namespace Horde\Form\V3;
use Horde_Variables;
use Horde_Form_Translation;

class KeyvalMultienumVariable extends MultienumVariable
{
    //TODO: Rename back to getInfo() after the V3 transition
    protected function getInfoV3($vars)
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
    public function about(): array
    {
        $about = parent::about();
        $about['name'] = Horde_Form_Translation::t("Multiple selection, preserving keys");
    }

}
