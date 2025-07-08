<?php
namespace Horde\Form\V3;
use Horde_Variables;
use Horde_Form_Translation;

class FileVariable extends BaseVariable
{
    public function isValid(Horde_Variables|array $vars, $value): bool
    {
        if ($this->isRequired()) {
            try {
                $GLOBALS['browser']->wasFileUploaded($this->getVarName());
            } catch (Horde_Browser_Exception $e) {
                $this->message = $e->getMessage();
                return false;
            }
        }

        return true;
    }

    //TODO: Rename back to getInfo() after the V3 transition
    protected function getInfoV3($vars)
    {
        $name = $this->getVarName();
        $info = [];
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
    public function about(): array
    {
        return ['name' => Horde_Form_Translation::t("File upload")];
    }


}
