<?php
namespace Horde\Form\V3;
use Horde_Variables;
use Horde_Form_Translation;

class IntlistVariable extends BaseVariable
{
    public function isValid(Horde_Variables|array $vars, $value): bool
    {
        if (empty($value) && $this->isRequired()) {
            return $this->invalid('This field is required.');
        }

        if (empty($value) || preg_match('/^[0-9 ,]+$/', $value)) {
            return true;
        }

        return $this->invalid('This field must be a comma or space separated list of integers');
    }

    /**
     * Return info about field type.
     */
    public function about(): array
    {
        return [ 'name' => Horde_Form_Translation::t("Integer list") ];
    }

}
