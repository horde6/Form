<?php
namespace Horde\Form\V3;

use Horde_Variables;
use Horde_Form_Translation;

/**
 * StringlistVariable type for string list input fields.
 *
 * @property string $regex The regex pattern for validation
 * @property int $size The size of the input field
 * @property int|null $maxlength The maximum number of characters
 */
class StringlistVariable extends TextVariable
{
    /**
     * Return info about field type.
     */
    public function about(): array
    {
        $about = parent::about();
        $about['name'] = Horde_Form_Translation::t("String list");
        return $about;
    }
}
