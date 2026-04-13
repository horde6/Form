<?php

/**
 * Copyright 2001-2026 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category Horde
 * @license  http://www.horde.org/licenses/lgpl21 LGPL
 * @package  Form
 */

namespace Horde\Form\V3;

use Horde_Variables;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Migration interface for Form methods that existed in lib/ (Horde_Form).
 *
 * These methods provide backward compatibility with the lib/ implementation.
 * Code migrating from lib/ to V3 can rely on these methods having similar
 * signatures and behavior.
 *
 * @category Horde
 * @package  Form
 */
interface FormMigrationInterface
{
    /**
     * Returns the form variables as an array.
     *
     * @return array  Form variables
     */
    public function getVars(): array;

    /**
     * Sets form variables.
     *
     * @param Horde_Variables|ServerRequestInterface|array $vars  Variables to set
     */
    public function setVars(Horde_Variables|ServerRequestInterface|array $vars): void;

    /**
     * Gets a single variable value.
     *
     * @param string $name     Variable name
     * @param mixed $default   Default value if not set
     *
     * @return mixed  Variable value
     */
    public function getVar(string $name, mixed $default = null): mixed;

    /**
     * Sets a single variable value.
     *
     * @param string $name   Variable name
     * @param mixed $value   Variable value
     */
    public function setVar(string $name, mixed $value): void;

    /**
     * Returns the form title.
     *
     * @return string  Form title
     */
    public function getTitle(): string;

    /**
     * Sets the form title.
     *
     * @param string $title  Form title
     */
    public function setTitle(string $title): void;

    /**
     * Returns the extra form content.
     *
     * @return string  Extra content
     */
    public function getExtra(): string;

    /**
     * Sets extra form content.
     *
     * @param string $extra  Extra content
     */
    public function setExtra(string $extra): void;

    /**
     * Returns the form name.
     *
     * @return string  Form name
     */
    public function getName(): string;

    /**
     * Gets or sets token usage.
     *
     * @param bool|null $token  Set token usage, or null to get current state
     *
     * @return bool  Token usage state
     */
    public function useToken(?bool $token = null): bool;

    /**
     * Sets the currently open form section.
     *
     * @param string $section      Section name
     * @param string $desc         Section description
     * @param string $image        Section image path
     * @param bool $expanded       Whether section is expanded
     */
    public function setSection(
        string $section,
        string $desc = '',
        string $image = '',
        bool $expanded = true
    ): void;

    /**
     * Gets section description.
     *
     * @param string|int $section  Section identifier
     *
     * @return string  Section description
     */
    public function getSectionDesc(string|int $section): string;

    /**
     * Gets section image path.
     *
     * @param string|int $section  Section identifier
     *
     * @return string  Section image path
     */
    public function getSectionImage(string|int $section): string;

    /**
     * Sets the open section.
     *
     * @param string|int $section  Section identifier
     */
    public function setOpenSection(string|int $section): void;

    /**
     * Gets the currently open section.
     *
     * @return string|int|null  Open section identifier
     */
    public function getOpenSection(): string|int|null;

    /**
     * Gets section expanded state.
     *
     * @param string|int $section  Section identifier
     * @param bool $boolean        Return as boolean vs string
     *
     * @return bool|string  Expanded state
     */
    public function getSectionExpandedState(string|int $section, bool $boolean = false): bool|string;

    /**
     * Adds a variable to the form.
     *
     * @param string $humanName         Human-readable name
     * @param string $varName           Internal variable name
     * @param string|Variable $type     Variable type or instance
     * @param bool $required            Whether required
     * @param bool $readonly            Whether readonly
     * @param string|null $description  Long description
     * @param mixed $params             Type-specific parameters
     *
     * @return Variable  The created variable
     */
    public function addVariable(
        string $humanName,
        string $varName,
        string|Variable $type,
        bool $required,
        bool $readonly = false,
        ?string $description = null,
        mixed $params = []
    ): Variable;

    /**
     * Inserts a variable before another variable.
     *
     * @param string $before            Variable to insert before
     * @param string $humanName         Human-readable name
     * @param string $varName           Internal variable name
     * @param string|Variable $type     Variable type or instance
     * @param bool $required            Whether required
     * @param bool $readonly            Whether readonly
     * @param string|null $description  Long description
     * @param mixed $params             Type-specific parameters
     *
     * @return Variable  The created variable
     */
    public function insertVariableBefore(
        string $before,
        string $humanName,
        string $varName,
        string|Variable $type,
        bool $required,
        bool $readonly = false,
        ?string $description = null,
        mixed $params = []
    ): Variable;

    /**
     * Removes a variable from the form.
     *
     * @param Variable|string $var  Variable instance or name
     *
     * @return bool  True if removed, false if not found
     */
    public function removeVariable(Variable|string $var): bool;

    /**
     * Adds a hidden variable.
     *
     * @param string $humanName         Human-readable name
     * @param string $varName           Internal variable name
     * @param string|Variable $type     Variable type or instance
     * @param bool $required            Whether required
     * @param bool $readonly            Whether readonly
     * @param string|null $description  Long description
     * @param mixed $params             Type-specific parameters
     *
     * @return Variable  The created variable
     */
    public function addHidden(
        string $humanName,
        string $varName,
        string|Variable $type,
        bool $required,
        bool $readonly = false,
        ?string $description = null,
        mixed $params = []
    ): Variable;

    /**
     * Gets form variables.
     *
     * @param bool $flat          Return flat array vs nested by section
     * @param bool $withHidden    Include hidden variables
     *
     * @return array  Form variables
     */
    public function getVariables(bool $flat = true, bool $withHidden = false): array;

    /**
     * Sets form buttons.
     *
     * @param array|string|bool $submit  Submit button configuration
     * @param string|bool $reset         Reset button configuration
     */
    public function setButtons(array|string|bool $submit, string|bool $reset = false): void;

    /**
     * Appends buttons to existing buttons.
     *
     * @param array|string $submit  Submit button configuration
     */
    public function appendButtons(array|string $submit): void;

    /**
     * Validates the form.
     *
     * @param Horde_Variables|array|null $vars  Variables to validate
     *
     * @return bool  True if valid
     */
    public function validate($vars = null): bool;

    /**
     * Returns whether form is valid.
     *
     * @return bool  True if valid
     */
    public function isValid(): bool;

    /**
     * Gets all validation errors.
     *
     * @return array  Errors indexed by variable name
     */
    public function getErrors(): array;

    /**
     * Gets error for specific variable.
     *
     * @param string $varName  Variable name
     *
     * @return string|null  Error message or null
     */
    public function getError(string $varName): ?string;

    /**
     * Sets an error for a variable.
     *
     * @param string $varName   Variable name
     * @param string $message   Error message
     */
    public function setError(string $varName, string $message): void;

    /**
     * Clears errors.
     *
     * @param string|null $varName  Variable name, or null for all
     */
    public function clearError(?string $varName = null): void;

    /**
     * Extracts form data.
     *
     * @param Horde_Variables|array|null $vars  Variables to extract from
     *
     * @return array  Extracted form data
     */
    public function getInfo($vars = null): array;

    /**
     * Returns whether form was submitted.
     *
     * @return bool  True if submitted
     */
    public function isSubmitted(): bool;

    /**
     * Gets form encoding type.
     *
     * @return string|null  Encoding type or null
     */
    public function getEnctype(): ?string;
}
