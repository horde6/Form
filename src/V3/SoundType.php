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
            $message = Horde_Form_Translation::t("This field is required.");
            $this->message = $message;
            return false;
        }

        if (empty($value) || in_array($value, $this->_sounds)) {
            return true;
        }

        $message = Horde_Form_Translation::t("Please choose a sound.");
        $this->message = $message;
        return false;
    }

    /**
     * Return info about field type.
     */
    public function about()
    {
        return [ 'name' => Horde_Form_Translation::t("Sound selection") ];
    }

}
