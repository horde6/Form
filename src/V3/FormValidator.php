<?php

declare(strict_types=1);

/**
 * Copyright 2001-2026 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category Horde
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package  Form
 */

namespace Horde\Form\V3;

/**
 * Interface for form-level validators.
 *
 * Form validators run after individual field validation and can
 * perform cross-field checks (e.g., date ranges, field dependencies).
 *
 * Implementations receive the full form data and the error array
 * by reference, allowing them to add errors keyed by field name.
 *
 * @category Horde
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package  Form
 */
interface FormValidator
{
    /**
     * Validate form data.
     *
     * Called after field-level validation. May add entries to $errors
     * keyed by variable name.
     *
     * @param array<string, mixed> $vars    Form data
     * @param array<string, string> &$errors  Error map (varName => message), passed by reference
     */
    public function validate(array $vars, array &$errors): void;
}
