<?php
declare(strict_types=1);

/**
 * Copyright 2001-2007 Robert E. Coyle <robertecoyle@hotmail.com>
 * Copyright 2026 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @author   Robert E. Coyle <robertecoyle@hotmail.com>
 * @author   Ralf Lang <ralf.lang@ralf-lang.de>
 * @category Horde
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package  Form
 */

namespace Horde\Form\V3;

use Horde\Form\Form;

/**
 * Renderer interface for rendering forms in various formats.
 *
 * Separates presentation (how forms look) from logic (what forms do).
 *
 * Supported rendering strategies:
 * - HTML+JS (interactive forms)
 * - JSON (API responses)
 * - HTML-Print (printer-friendly)
 * - HTML-Inactive (display-only)
 *
 * V3 design principles:
 * - Clean separation: Form = logic, Renderer = presentation
 * - Interface-based: Multiple renderers possible
 * - Strategy pattern: Pluggable components (layout, errors, assets)
 * - No global state: All config via constructor
 *
 * @author    Robert E. Coyle <robertecoyle@hotmail.com>
 * @author    Ralf Lang <ralf.lang@ralf-lang.de>
 * @category  Horde
 * @copyright 2001-2007 Robert E. Coyle
 * @copyright 2026 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Form
 */
interface Renderer
{
    /**
     * Render the complete form.
     *
     * This is the primary method - renders the entire form including
     * opening tag, header, fields, errors, buttons, and closing tag.
     *
     * @param Form $form  The form to render
     * @param string $action  Form action URL
     * @param string $method  HTTP method (get/post)
     * @return string  Complete rendered output
      *
      * @api
     */
    public function render(Form $form, string $action = '', string $method = 'post'): string;

    /**
     * Render form opening tag.
     *
     * @param Form $form  The form to render
     * @param string $action  Form action URL
     * @param string $method  HTTP method
     * @return string  Opening form tag
      *
      * @api
     */
    public function renderOpen(Form $form, string $action, string $method): string;

    /**
     * Render form closing tag.
     *
     * @return string  Closing form tag
      *
      * @api
     */
    public function renderClose(): string;

    /**
     * Render form header (title, description, extra content).
     *
     * @param Form $form  The form to render
     * @return string  Form header HTML
      *
      * @api
     */
    public function renderHeader(Form $form): string;

    /**
     * Render a single variable as a form field.
     *
     * @param Variable $variable  The variable to render
     * @param Form $form  The parent form
     * @return string  Rendered field
      *
      * @api
     */
    public function renderVariable(Variable $variable, Form $form): string;

    /**
     * Render a form section (group of variables).
     *
     * @param string|int $sectionName  Section identifier
     * @param array<Variable> $variables  Variables in this section
     * @param Form $form  The parent form
     * @return string  Rendered section
      *
      * @api
     */
    public function renderSection(string|int $sectionName, array $variables, Form $form): string;

    /**
     * Render form buttons (submit, reset).
     *
     * @param Form $form  The form to render
     * @return string  Form buttons HTML
      *
      * @api
     */
    public function renderButtons(Form $form): string;

    /**
     * Render validation errors.
     *
     * @param Form $form  The form to render
     * @return string  Errors HTML
      *
      * @api
     */
    public function renderErrors(Form $form): string;

    /**
     * Render hidden fields.
     *
     * @param Form $form  The form to render
     * @return string  Hidden fields HTML
      *
      * @api
     */
    public function renderHidden(Form $form): string;
}
