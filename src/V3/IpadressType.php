<?php
namespace Horde\Form\V3;
use Horde_Form_Translation;

class IpadressType extends TextType
{
    public function isValid($var, Horde_Variables|array $vars, $value): bool
    {
        if (strlen(trim($value)) > 0) {
            $ip = explode('.', $value);
            $valid = (count($ip) == 4);
            if ($valid) {
                foreach ($ip as $part) {
                    if (!is_numeric($part) ||
                        $part > 255 ||
                        $part < 0) {
                        $valid = false;
                        break;
                    }
                }
            }

            if (!$valid) {
                return $this->invalid('Please enter a valid IP address.');
            }
        } elseif ($var->isRequired()) {
            return $this->invalid('This field is required.');
        }

        return true;
    }

    /**
     * Return info about field type.
     */
    public function about():array
    {
        return [ 'name' => Horde_Form_Translation::t("IP address") ];
    }

}
