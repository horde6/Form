<?php
namespace Horde\Form\V3;
use Horde_Variables;
use Horde_Form_Translation;
use Horde_Themes;

class SoundVariable extends BaseVariable
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

    public function isValid(Horde_Variables $vars, $value): bool
    {
        if ($this->isRequired() && empty($value)) {
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
    public function about(): array
    {
        return [ 'name' => Horde_Form_Translation::t("Sound selection") ];
    }

}
