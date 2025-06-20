<?php
/**
 * Copyright 2001-2025 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @author   Robert E. Coyle <robertecoyle@hotmail.com>
 */
namespace Horde\Form\V3;
use Horde_Variables;
interface Type
{
    public function getMessage(): string;
    public function getProperty($property);
    public function setProperty($property, $value);
    public function init(...$params);
    public function onSubmit(...$params);
    public function isValid($var, Horde_Variables|array $vars, $value): bool;
    public function getTypeName(): string;
    public function getValues(...$params);
    public function getInfo($vars, $var): array|object;
    // TODO: Should about() be part of the interface?
}