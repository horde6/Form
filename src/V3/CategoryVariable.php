<?php
namespace Horde\Form\V3;
use Horde_Variables;
use Horde_Form_Translation;

class CategoryVariable extends BaseVariable
{
    //TODO: Rename back to getInfo() after the V3 transition
    protected function getInfoV3($vars)
    {
        $info = $this->getValue($vars);
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
    public function about(): array
    {
        return [ 'name' => Horde_Form_Translation::t("Category") ];
    }

    public function isValid(Horde_Variables $vars, $value): bool
    {
        if (empty($value) && $this->isRequired()) {
            return $this->invalid('This field is required.');
        }

        return true;
    }

}
