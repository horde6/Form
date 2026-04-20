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
 * A FieldGroup with visual metadata for rendering.
 *
 * Section extends FieldGroup to add title, description, image, and
 * expanded/collapsed state. These are used by renderers to produce
 * collapsible section headers in the form layout.
 *
 * Sections replace the legacy metadata arrays in BaseForm's
 * `$this->sections` property with proper typed objects.
 *
 * @category Horde
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package  Form
 */
class Section extends FieldGroup
{
    /**
     * @param string $name         Section identifier
     * @param string $title        Display title (shown as section header)
     * @param string $description  Section description text
     * @param string $image        Section icon/image URL
     * @param bool $expanded       Whether section starts expanded
     * @param string $prefix       Name prefix for variables ('' = no prefix)
     */
    public function __construct(
        string $name,
        private string $title = '',
        private string $description = '',
        private string $image = '',
        private bool $expanded = true,
        string $prefix = '',
    ) {
        parent::__construct($name, $prefix);
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function setImage(string $image): void
    {
        $this->image = $image;
    }

    public function isExpanded(): bool
    {
        return $this->expanded;
    }

    public function setExpanded(bool $expanded): void
    {
        $this->expanded = $expanded;
    }
}
