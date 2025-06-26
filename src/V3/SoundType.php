<?php
namespace Horde\Form\V3;
use Horde_Form_Translation;

class SoundType extends BaseType
{
    public $_sounds = [];

    public function init(...$params)
    {
        $this->_sounds = array_keys(Horde_Themes::soundList());
    }

    public function getSounds()
    {
        return $this->_sounds;
    }

    public function isValid($var, Horde_Variables|array $vars, $value): bool
    {
        if ($var->isRequired() && empty($value)) {
            return $this->invalid('This field is required.');
        }

        if (empty($value) || in_array($value, $this->_sounds)) {
            return true;
        }

        return $this->invalid('Please choose a sound.');
    }

    /**
     * Return info about field type.
     */
    public function about()
    {
        return [ 'name' => Horde_Form_Translation::t("Sound selection") ];
    }

}
