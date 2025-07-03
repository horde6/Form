<?php
namespace Horde\Form\V3;
use Horde_Variables;
use Horde_Form_Translation;

class Ip6adressVariable extends TextVariable
{
    public function isValid(Horde_Variables|array $vars, $value): bool
    {
        if (strlen(trim($value)) > 0) {
            $valid = @inet_pton($value);

            if ($valid === false) {
                return $this->invalid('Please enter a valid IP address.');
            }
        } elseif ($this->isRequired()) {
            return $this->invalid('This field is required.');
        }

        return true;
     }

    /**
     * Return info about field type.
     */
    public function about():array
    {
        return [ 'name' => Horde_Form_Translation::t("IPv6 address") ];
    }

}
