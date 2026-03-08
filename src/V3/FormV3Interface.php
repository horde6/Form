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

/**
 * V3-native interface for Form methods introduced in the V3 implementation.
 *
 * These methods are new to V3 and provide modernized functionality
 * not present in lib/ (Horde_Form).
 *
 * Note: Most Form functionality is migration-based. V3 improvements are
 * primarily internal (PSR-7 support, type hints, private helper methods).
 * This interface is included for completeness but currently empty as all
 * public methods existed in lib/.
 *
 * @category Horde
 * @package  Form
 */
interface FormV3Interface
{
    // Currently no V3-specific public methods.
    // Internal improvements include:
    // - normalizeVars() - private method for PSR-7/array/Horde_Variables normalization
    // - getInfoFromVariables() - private extraction method
    // - createVariable() - private factory method
    // - Enhanced type hints on all methods (PHP 8+ features)
}
