<?php

declare(strict_types=1);

/**
 * Copyright 2002-2026 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @author   Chuck Hagenbuch <chuck@horde.org>
 * @category Horde
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package  Form
 */

namespace Horde\Form\V3;

use Horde\Form\Form;

/**
 * Migration interface for Action methods that existed in lib/ (Horde_Form_Action).
 *
 * These methods provide backward compatibility with the lib/ implementation.
 * Code migrating from lib/ to V3 can rely on these methods having similar
 * signatures and behavior.
 *
 * @category Horde
 * @package  Form
 */
interface ActionMigrationInterface
{
    /**
     * Get action trigger events.
     *
     * @return array<string>|null  Event names (e.g., ['onload', 'onchange']) or null
     */
    public function getTrigger(): ?array;

    /**
     * Get JavaScript code for this action.
     *
     * @param Form $form       The form instance
     * @param mixed $renderer  The form renderer
     * @param string $varname  Variable name this action applies to
     *
     * @return string  JavaScript code
     */
    public function getActionScript(Form $form, $renderer, string $varname): string;

    /**
     * Print JavaScript for this action.
     *
     * Some actions may need to output JavaScript directly.
     */
    public function printJavaScript(): void;

    /**
     * Get target field name for this action.
     *
     * @return string|null  Target field name or null
     */
    public function getTarget(): ?string;

    /**
     * Set values based on action logic.
     *
     * @param mixed $vars       Form variables
     * @param mixed $sourceVal  Source value
     * @param int|null $index   Array index (if applicable)
     * @param bool $arrayVal    Whether value is an array
     */
    public function setValues($vars, $sourceVal, ?int $index = null, bool $arrayVal = false): void;
}
