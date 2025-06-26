<?php
namespace Horde\Form\V3;
use Horde_Form_Translation;

class CountryType extends BaseType
{
    public function isValid($var, Horde_Variables|array $vars, $value): bool
    {
        if ($var->isRequired()) {
            try {
                $GLOBALS['browser']->wasFileUploaded($var->getVarName());
            } catch (Horde_Browser_Exception $e) {
                $this->message = $e->getMessage();
                return false;
            }
        }

        return true;
    }

    public function getInfo($vars, $var)
    {
        $info = [];
        $name = $var->getVarName();
        try {
            $GLOBALS['browser']->wasFileUploaded($name);
            $info['name'] = Horde_Util::dispelMagicQuotes($_FILES[$name]['name']);
            $info['type'] = $_FILES[$name]['type'];
            $info['tmp_name'] = $_FILES[$name]['tmp_name'];
            $info['file'] = $_FILES[$name]['tmp_name'];
            $info['error'] = $_FILES[$name]['error'];
            $info['size'] = $_FILES[$name]['size'];
        } catch (Horde_Browser_Exception $e) {
        }
        return $info;
    }

    /**
     * Return info about field type.
     */
    public function about()
    {
        return [ 'name' => Horde_Form_Translation::t("File upload") ];
    }

}
