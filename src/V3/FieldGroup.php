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
 * A logical group of form variables with optional name prefix.
 *
 * FieldGroup is the structural base for organizing variables within a
 * form. Variables in a prefixed group get bracket-notation names
 * (e.g., prefix 'billing' + varName 'street' = 'billing[street]'),
 * which PHP's POST parsing decodes into nested arrays automatically.
 *
 * FieldGroups participate in the form validation pipeline via the
 * FormValidator interface. Subclasses override validateGroup() for
 * cross-field validation scoped to the group's variables.
 *
 * Section extends FieldGroup to add visual metadata (title,
 * description, image, expanded state).
 *
 * @category Horde
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package  Form
 */
class FieldGroup implements FormValidator
{
    use VariableFactoryTrait;

    /** @var array<Variable> */
    private array $variables = [];

    /**
     * Whether this group is enabled (active) or disabled (read-only).
     *
     * Disabled groups are skipped during validation and rendered in
     * display mode. Their values are preserved as hidden inputs by
     * the renderer.
     */
    private bool $enabled = true;

    /**
     * @param string $name    Group identifier (used as key in form's group map)
     * @param string $prefix  Name prefix for variables ('' = no prefix)
     */
    public function __construct(
        private readonly string $name,
        private readonly string $prefix = '',
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    /**
     * Whether this group is enabled (active/editable).
     *
     * Disabled groups are skipped during form validation and rendered
     * in display-only mode by renderMixed().
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Enable or disable this group.
     *
     * @param bool $enabled  True = active/editable, false = read-only
     */
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * Add a variable to this group.
     *
     * If the group has a prefix, the variable's name is automatically
     * scoped: prefix 'billing' + varName 'street' = 'billing[street]'.
     *
     * @param string $humanName  Human-readable field label
     * @param string $varName  Internal variable name
     * @param string $type  Variable type (e.g., 'text', 'enum')
     * @param bool $required  Whether field is required
     * @param bool $readonly  Whether field is read-only
     * @param string|null $description  Field description/help text
     * @param array $params  Type-specific parameters
     * @return Variable  The created variable instance
     */
    public function addVariable(
        string $humanName,
        string $varName,
        string $type,
        bool $required,
        bool $readonly = false,
        ?string $description = null,
        array $params = []
    ): Variable {
        $scopedName = $this->prefix !== ''
            ? $this->prefix . '[' . $varName . ']'
            : $varName;

        $var = $this->createVariable(
            humanName: $humanName,
            varName: $scopedName,
            type: $type,
            required: $required,
            readonly: $readonly,
            description: $description,
            params: $params
        );

        $this->variables[] = $var;

        return $var;
    }

    /**
     * Get all variables in this group.
     *
     * @return array<Variable>
     */
    public function getVariables(): array
    {
        return $this->variables;
    }

    /**
     * Insert a pre-built variable into this group.
     *
     * Used by BaseForm when it needs to control variable creation
     * (e.g., insertVariableBefore). For normal use, prefer addVariable().
     *
     * @param Variable $var  Variable to insert
     * @param string|null $before  Insert before this variable name (null = append)
     */
    public function insertVariable(Variable $var, ?string $before = null): void
    {
        if ($before === null) {
            $this->variables[] = $var;
            return;
        }

        $position = null;
        foreach ($this->variables as $index => $existingVar) {
            if ($existingVar->getVarName() === $before) {
                $position = $index;
                break;
            }
        }

        if ($position === null) {
            $this->variables[] = $var;
        } else {
            $this->variables = array_merge(
                array_slice($this->variables, 0, $position),
                [$var],
                array_slice($this->variables, $position)
            );
        }
    }

    /**
     * Remove a variable from this group.
     *
     * @param Variable|string $var  Variable instance or variable name
     * @return bool  True if variable was found and removed
     */
    public function removeVariable(Variable|string $var): bool
    {
        $varName = $var instanceof Variable ? $var->getVarName() : $var;

        foreach ($this->variables as $index => $existingVar) {
            if ($existingVar->getVarName() === $varName
                || ($var instanceof Variable && $existingVar === $var)) {
                array_splice($this->variables, $index, 1);
                return true;
            }
        }

        return false;
    }

    /**
     * FormValidator implementation.
     *
     * Extracts this group's variables from the form data (using the
     * prefix if set) and delegates to validateGroup().
     *
     * @param array<string, mixed> $vars    Full form data
     * @param array<string, string> &$errors  Error map, passed by reference
     */
    public function validate(array $vars, array &$errors): void
    {
        $groupVars = $this->prefix !== ''
            ? ($vars[$this->prefix] ?? [])
            : $vars;

        $this->validateGroup($groupVars, $errors);
    }

    /**
     * Group-level validation override point.
     *
     * Subclasses override this to add cross-field validation scoped
     * to this group's variables. Called after field-level validation.
     *
     * For prefixed groups, $vars contains only this group's sub-array
     * (e.g., for prefix 'billing', $vars has 'street', 'city', etc.).
     * For unprefixed groups, $vars is the full form data.
     *
     * @param array<string, mixed> $vars    Group's variables
     * @param array<string, string> &$errors  Error map, passed by reference
     */
    protected function validateGroup(array $vars, array &$errors): void
    {
        // No-op default. Subclasses override.
    }
}
