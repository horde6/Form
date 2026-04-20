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
 * Factory for creating Variable instances from type strings.
 *
 * Shared by BaseForm and FieldGroup so both can create variables
 * using the same type-string-to-class mapping.
 *
 * @category Horde
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package  Form
 */
trait VariableFactoryTrait
{
    /**
     * Create a Variable instance from a type string.
     *
     * Maps type strings like 'text', 'email', 'enum' to Variable classes.
     * Supports app-specific types via 'app:typename' format (e.g., 'whups:priority').
     *
     * @param string $humanName  Human-readable field label
     * @param string $varName  Internal variable name
     * @param string $type  Variable type string
     * @param bool $required  Whether field is required
     * @param bool $readonly  Whether field is read-only
     * @param string|null $description  Field description
     * @param array $params  Type-specific parameters
     * @return Variable  Created variable instance
     */
    private function createVariable(
        string $humanName,
        string $varName,
        string $type,
        bool $required,
        bool $readonly = false,
        ?string $description = null,
        array $params = []
    ): Variable {
        // Handle app:typename format (e.g., 'whups:priority')
        if (str_contains($type, ':')) {
            [$app, $type] = explode(':', $type, 2);
            $class = ucfirst($app) . '\\Form\\V3\\' . ucfirst($type) . 'Variable';
        } else {
            // Standard Horde types
            $class = 'Horde\\Form\\V3\\' . ucfirst($type) . 'Variable';
        }

        // Fallback to InvalidVariable if class doesn't exist
        if (!class_exists($class)) {
            error_log("Warning: Form type class '$class' not found, using InvalidVariable");
            $class = 'Horde\\Form\\V3\\InvalidVariable';
        }

        // Create variable instance
        $var = new $class(
            humanName: $humanName,
            varName: $varName,
            required: $required,
            readonly: $readonly,
            description: $description
        );

        // Initialize with type-specific parameters
        $var->init(...$params);

        return $var;
    }
}
