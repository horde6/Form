<?php

declare(strict_types=1);

/**
 * Copyright 2001-2026 Robert E. Coyle <robertecoyle@hotmail.com>
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
use Horde\Form\V3\Renderer\ControlRenderer;
use Horde\Form\V3\Renderer\LayoutStrategy;
use Horde\Form\V3\Renderer\ErrorRenderer;
use Horde\Form\V3\Renderer\AssetManager;

/**
 * Base implementation of the Renderer interface.
 *
 * Provides common functionality for all renderers. Subclasses implement
 * specific rendering strategies (HTML, JSON, etc.).
 *
 * Uses template method pattern - defines the rendering flow, subclasses
 * implement specific rendering logic.
 *
 * PSR-4 implementation.
 *
 * @see Horde_Form_Renderer PSR-0 legacy equivalent in lib/Horde/Form/Renderer.php
 *
 * @author    Robert E. Coyle <robertecoyle@hotmail.com>
 * @author    Ralf Lang <ralf.lang@ralf-lang.de>
 * @category  Horde
 * @copyright 2001-2007 Robert E. Coyle
 * @copyright 2026 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Form
 */
abstract class BaseRenderer implements Renderer
{
    /**
     * Control renderer for form controls.
     */
    protected ControlRenderer $controlRenderer;

    /**
     * Layout strategy for form structure.
     */
    protected LayoutStrategy $layoutStrategy;

    /**
     * Error renderer for validation errors.
     */
    protected ErrorRenderer $errorRenderer;

    /**
     * Asset manager for JS/CSS.
     */
    protected AssetManager $assetManager;

    /**
     * Show form header (title, description)?
     */
    protected bool $showHeader = true;

    /**
     * Required field marker.
     */
    protected string $requiredMarker = '*';

    /**
     * Help text marker (prepended to description text).
     */
    protected string $helpMarker = '';

    /**
     * Encode form title with htmlspecialchars()?
     */
    protected bool $encodeTitle = true;

    /**
     * Use striped rows (alternate colors)?
     */
    protected bool $stripedRows = false;

    /**
     * Current row index (for striped rows).
     */
    protected int $currentRow = 0;

    /**
     * Form name being rendered.
     */
    protected string $formName = '';

    /**
     * Construct a new renderer.
     *
     * @param ControlRenderer|null $controlRenderer  Control renderer
     * @param LayoutStrategy|null $layoutStrategy  Layout strategy
     * @param ErrorRenderer|null $errorRenderer  Error renderer
     * @param AssetManager|null $assetManager  Asset manager
     * @param array<string, mixed> $config  Configuration options
      *
      * @api
     */
    public function __construct(
        ?ControlRenderer $controlRenderer = null,
        ?LayoutStrategy $layoutStrategy = null,
        ?ErrorRenderer $errorRenderer = null,
        ?AssetManager $assetManager = null,
        array $config = []
    ) {
        // Set defaults if not provided (will be overridden by subclasses)
        $this->controlRenderer = $controlRenderer ?? $this->createDefaultControlRenderer();
        $this->layoutStrategy = $layoutStrategy ?? $this->createDefaultLayoutStrategy();
        $this->errorRenderer = $errorRenderer ?? $this->createDefaultErrorRenderer();
        $this->assetManager = $assetManager ?? $this->createDefaultAssetManager();

        // Apply configuration
        if (isset($config['showHeader'])) {
            $this->showHeader = (bool) $config['showHeader'];
        }
        if (isset($config['requiredMarker'])) {
            $this->requiredMarker = (string) $config['requiredMarker'];
        }
        if (isset($config['helpMarker'])) {
            $this->helpMarker = (string) $config['helpMarker'];
        }
        if (isset($config['encodeTitle'])) {
            $this->encodeTitle = (bool) $config['encodeTitle'];
        }
        if (isset($config['stripedRows'])) {
            $this->stripedRows = (bool) $config['stripedRows'];
        }
    }

    /**
     * Create default control renderer (overrideable).
     *
     * @return ControlRenderer
     */
    abstract protected function createDefaultControlRenderer(): ControlRenderer;

    /**
     * Create default layout strategy (overrideable).
     *
     * @return LayoutStrategy
     */
    abstract protected function createDefaultLayoutStrategy(): LayoutStrategy;

    /**
     * Create default error renderer (overrideable).
     *
     * @return ErrorRenderer
     */
    abstract protected function createDefaultErrorRenderer(): ErrorRenderer;

    /**
     * Create default asset manager (overrideable).
     *
     * @return AssetManager
     */
    abstract protected function createDefaultAssetManager(): AssetManager;

    /**
     * Render the complete form.
     *
     * Template method - defines the rendering flow.
      *
      * @api
     */
    public function render(Form $form, string $action = '', string $method = 'post'): string
    {
        $this->formName = $form->getName();
        $this->currentRow = 0;

        $output = [];

        // Opening form tag
        $output[] = $this->renderOpen($form, $action, $method);

        // Form header (title, description)
        if ($this->showHeader) {
            $output[] = $this->renderHeader($form);
        }

        // Validation errors (if any)
        $output[] = $this->renderErrors($form);

        // Hidden fields
        $output[] = $this->renderHidden($form);

        // Render variables by section
        $variables = $form->getVariables(flat: false);

        if (empty($variables)) {
            // No sections, render as flat list
            $fields = $this->renderVariables($form->getVariables(flat: true), $form);
            $output[] = $this->layoutStrategy->wrapForm(
                header: '',
                fields: $fields,
                buttons: $this->renderButtons($form),
                meta: ['formName' => $form->getName()]
            );
        } else {
            // Has sections
            $sections = [];
            foreach ($variables as $sectionName => $sectionVars) {
                $sections[] = $this->renderSection($sectionName, $sectionVars, $form);
            }

            $output[] = $this->layoutStrategy->wrapForm(
                header: '',
                fields: implode("\n", $sections),
                buttons: $this->renderButtons($form),
                meta: ['formName' => $form->getName()]
            );
        }

        // Closing form tag
        $output[] = $this->renderClose();

        // Action scripts (CSP-friendly addEventListener bindings)
        $output[] = $this->collectActionScripts($form);

        // Assets (JS/CSS)
        $output[] = $this->assetManager->render();

        return implode("\n", array_filter($output));
    }

    /**
     * Render multiple variables.
     *
     * @param array<Variable> $variables  Variables to render
     * @param Form $form  Parent form
     * @return string  Rendered variables
      *
      * @internal
     */
    protected function renderVariables(array $variables, Form $form): string
    {
        $output = [];
        foreach ($variables as $var) {
            $output[] = $this->renderVariable($var, $form);
        }
        return implode("\n", $output);
    }

    /**
     * Render a single variable.
      *
      * @api
     */
    public function renderVariable(Variable $variable, Form $form): string
    {
        // Skip hidden variables (rendered separately)
        if ($variable->isHidden()) {
            return '';
        }

        // Render components
        $label = $this->controlRenderer->renderLabel($variable, $form);
        $control = $this->controlRenderer->renderControl($variable, $form, $variable->readonly);
        $help = $this->controlRenderer->renderHelp($variable);

        // Get error for this field (if any)
        $error = '';
        $fieldError = $form->getError($variable->getVarName());
        if ($fieldError) {
            $error = $this->errorRenderer->renderFieldError($variable->getVarName(), $fieldError);
        }

        // Row metadata
        $meta = [
            'varName' => $variable->getVarName(),
            'required' => $variable->required,
            'readonly' => $variable->readonly,
            'disabled' => $variable->isDisabled(),
            'rowClass' => $this->getRowClass(),
        ];

        $this->currentRow++;

        return $this->layoutStrategy->wrapField($label, $control, $help, $error, $meta);
    }

    /**
     * Render a form section.
      *
      * @api
     */
    public function renderSection(string|int $sectionName, array $variables, Form $form): string
    {
        // If the form exposes group objects, use Section metadata directly
        $title = '';
        $description = '';
        $group = method_exists($form, 'getGroup') ? $form->getGroup((string) $sectionName) : null;

        if ($group instanceof \Horde\Form\V3\Section) {
            $title = $group->getTitle();
            $description = $group->getDescription();
        } else {
            $title = is_string($sectionName) && $sectionName !== '__base' ? $sectionName : '';
            $description = $title ? $form->getSectionDesc($sectionName) : '';
        }

        $content = $this->renderVariables($variables, $form);

        $meta = [
            'sectionName' => $sectionName,
            'isBase' => $sectionName === '__base',
        ];

        return $this->layoutStrategy->wrapSection($title, $description, $content, $meta);
    }

    /**
     * Render validation errors.
      *
      * @api
     */
    public function renderErrors(Form $form): string
    {
        $errors = $form->getErrors();
        if (empty($errors)) {
            return '';
        }

        return $this->errorRenderer->renderFormErrors($errors);
    }

    /**
     * Render hidden fields.
      *
      * @api
     */
    public function renderHidden(Form $form): string
    {
        $output = [];

        // Form name (for submission detection)
        $output[] = sprintf(
            '<input type="hidden" name="formname" value="%s">',
            htmlspecialchars($form->getName())
        );

        // CSRF token
        if ($form instanceof BaseForm) {
            $token = $form->generateToken();
            if ($token !== null) {
                $output[] = sprintf(
                    '<input type="hidden" name="%s" value="%s">',
                    htmlspecialchars($form->getTokenFieldName()),
                    htmlspecialchars($token)
                );
            }
        }

        // Hidden variables
        $vars = $form->getVars();
        $hiddenVars = $form->getVariables(flat: true, withHidden: true);
        foreach ($hiddenVars as $var) {
            if ($var->isHidden()) {
                $value = $var->resolveValue($vars);
                $output[] = sprintf(
                    '<input type="hidden" name="%s" value="%s">',
                    htmlspecialchars($var->getVarName()),
                    htmlspecialchars((string) $value)
                );
            }
        }

        return implode("\n", $output);
    }

    /**
     * Render the form in display-only (inactive) mode.
     *
     * Produces a read-only representation: title, sections, and values
     * as plain text. No form tag, no buttons, no hidden fields, no
     * errors, no CSRF token.
      *
      * @api
     */
    public function renderInactive(Form $form): string
    {
        $this->formName = $form->getName();
        $this->currentRow = 0;

        $output = [];

        if ($this->showHeader) {
            $output[] = $this->renderHeader($form);
        }

        $variables = $form->getVariables(flat: false);

        if (empty($variables)) {
            $fields = $this->renderDisplayVariables($form->getVariables(flat: true), $form);
            $output[] = $this->layoutStrategy->wrapForm(
                header: '',
                fields: $fields,
                buttons: '',
                meta: ['formName' => $form->getName()],
            );
        } else {
            $sections = [];
            foreach ($variables as $sectionName => $sectionVars) {
                $sections[] = $this->renderDisplaySection($sectionName, $sectionVars, $form);
            }

            $output[] = $this->layoutStrategy->wrapForm(
                header: '',
                fields: implode("\n", $sections),
                buttons: '',
                meta: ['formName' => $form->getName()],
            );
        }

        return implode("\n", array_filter($output));
    }

    /**
     * Render a form with mixed active/inactive sections.
     *
     * Enabled groups are rendered as editable controls (like render()).
     * Disabled groups are rendered as display-only values with hidden
     * inputs to preserve their data across form submission.
     *
     * Used for wizard-style forms where completed steps are shown
     * read-only while the current step is editable — all within a
     * single <form> tag.
     *
     * Falls back to render() if no groups have mixed enabled state
     * (all enabled) or renderInactive() (all disabled).
      *
      * @api
     */
    public function renderMixed(Form $form, string $action = '', string $method = 'post'): string
    {
        $this->formName = $form->getName();
        $this->currentRow = 0;

        $output = [];

        // Opening form tag
        $output[] = $this->renderOpen($form, $action, $method);

        // Form header
        if ($this->showHeader) {
            $output[] = $this->renderHeader($form);
        }

        // Validation errors
        $output[] = $this->renderErrors($form);

        // Hidden fields (formname, CSRF, explicitly hidden variables)
        $output[] = $this->renderHidden($form);

        // Get all groups (structured by name)
        $allVariables = $form->getVariables(flat: false);

        if (empty($allVariables)) {
            // No groups at all — render as flat editable form
            $fields = $this->renderVariables($form->getVariables(flat: true), $form);
            $output[] = $this->layoutStrategy->wrapForm(
                header: '',
                fields: $fields,
                buttons: $this->renderButtons($form),
                meta: ['formName' => $form->getName()],
            );
        } else {
            $sections = [];
            $vars = $form->getVars();

            foreach ($allVariables as $sectionName => $sectionVars) {
                $group = method_exists($form, 'getGroup')
                    ? $form->getGroup((string) $sectionName)
                    : null;

                if ($group !== null && !$group->isEnabled()) {
                    // Disabled group: display mode + hidden inputs for preservation
                    $sections[] = $this->renderDisplaySection($sectionName, $sectionVars, $form);
                    $sections[] = $this->renderPreserveVariables($sectionVars, $vars);
                } else {
                    // Enabled group (or no group object): editable controls
                    $sections[] = $this->renderSection($sectionName, $sectionVars, $form);
                }
            }

            $output[] = $this->layoutStrategy->wrapForm(
                header: '',
                fields: implode("\n", $sections),
                buttons: $this->renderButtons($form),
                meta: ['formName' => $form->getName()],
            );
        }

        // Closing form tag
        $output[] = $this->renderClose();

        // Action scripts
        $output[] = $this->collectActionScripts($form);

        // Assets
        $output[] = $this->assetManager->render();

        return implode("\n", array_filter($output));
    }

    /**
     * Emit hidden inputs to preserve variable values across form submission.
     *
     * Used by renderMixed() for disabled groups whose values must survive
     * the POST but are shown in display mode (not as form controls).
     *
     * @param array<Variable> $variables  Variables to preserve
     * @param array<string, mixed> $vars  Current form data
     * @return string  HTML hidden input elements
      *
      * @internal
     */
    protected function renderPreserveVariables(array $variables, array $vars): string
    {
        $output = [];
        foreach ($variables as $var) {
            $varName = $var->getVarName();
            $value = $var->resolveValue($vars);
            $this->renderPreserveValue($output, $varName, $value);
        }
        return implode("\n", $output);
    }

    /**
     * Recursively emit hidden input(s) for a value.
     *
     * @param array<string> &$output  Output buffer
     * @param string $name  Field name
     * @param mixed $value  Field value (scalar or array)
      *
      * @internal
     */
    private function renderPreserveValue(array &$output, string $name, mixed $value): void
    {
        if (is_array($value)) {
            foreach ($value as $k => $v) {
                $this->renderPreserveValue($output, $name . '[' . $k . ']', $v);
            }
        } else {
            $output[] = sprintf(
                '<input type="hidden" name="%s" value="%s">',
                htmlspecialchars($name),
                htmlspecialchars((string) ($value ?? '')),
            );
        }
    }

    /**
     * Render multiple variables in display mode.
     *
     * @param array<Variable> $variables
      *
      * @internal
     */
    protected function renderDisplayVariables(array $variables, Form $form): string
    {
        $output = [];
        foreach ($variables as $var) {
            $rendered = $this->renderDisplayVariable($var, $form);
            if ($rendered !== '') {
                $output[] = $rendered;
            }
        }

        return implode("\n", $output);
    }

    /**
     * Render a single variable in display mode.
      *
      * @internal
     */
    protected function renderDisplayVariable(Variable $variable, Form $form): string
    {
        if ($variable->isHidden()) {
            return '';
        }

        $label = $this->controlRenderer->renderDisplayLabel($variable, $form);
        $display = $this->controlRenderer->renderDisplay($variable, $form);

        // Skip empty display values (e.g. figlet/captcha in inactive mode).
        if ($display === '') {
            return '';
        }

        $meta = [
            'varName' => $variable->getVarName(),
            'required' => false,
            'readonly' => true,
            'disabled' => false,
            'rowClass' => $this->getRowClass(),
        ];

        $this->currentRow++;

        return $this->layoutStrategy->wrapField($label, $display, '', '', $meta);
    }

    /**
     * Render a section in display mode.
      *
      * @internal
     */
    protected function renderDisplaySection(string|int $sectionName, array $variables, Form $form): string
    {
        $title = '';
        $description = '';
        $group = method_exists($form, 'getGroup') ? $form->getGroup((string) $sectionName) : null;

        if ($group instanceof \Horde\Form\V3\Section) {
            $title = $group->getTitle();
            $description = $group->getDescription();
        } else {
            $title = is_string($sectionName) && $sectionName !== '__base' ? $sectionName : '';
            $description = $title ? $form->getSectionDesc($sectionName) : '';
        }

        $content = $this->renderDisplayVariables($variables, $form);

        $meta = [
            'sectionName' => $sectionName,
            'isBase' => $sectionName === '__base',
        ];

        return $this->layoutStrategy->wrapSection($title, $description, $content, $meta);
    }

    /**
     * Get CSS class for current row (for striped rows).
     *
     * @return string  CSS class
      *
      * @internal
     */
    protected function getRowClass(): string
    {
        if (!$this->stripedRows) {
            return '';
        }

        return $this->currentRow % 2 === 0 ? 'even' : 'odd';
    }

    /**
     * Build an HTML tag with attributes.
     *
     * @param string $tag  Tag name
     * @param array<string, mixed> $attrs  Attributes (null values are skipped)
     * @param string|null $content  Tag content (null = self-closing)
     * @return string  HTML tag
      *
      * @internal
     */
    protected function buildTag(string $tag, array $attrs = [], ?string $content = null): string
    {
        $attrStr = '';
        foreach ($attrs as $key => $value) {
            if ($value === null || $value === false) {
                continue;
            }
            if ($value === true) {
                $attrStr .= ' ' . htmlspecialchars($key);
            } else {
                $attrStr .= sprintf(' %s="%s"', htmlspecialchars($key), htmlspecialchars((string) $value));
            }
        }

        if ($content === null) {
            // Self-closing tag
            return "<{$tag}{$attrStr}>";
        }

        return "<{$tag}{$attrStr}>{$content}</{$tag}>";
    }

    /**
     * Set whether to show form header.
      *
      * @api
     */
    public function setShowHeader(bool $show): void
    {
        $this->showHeader = $show;
    }

    /**
     * Set required field marker.
      *
      * @api
     */
    public function setRequiredMarker(string $marker): void
    {
        $this->requiredMarker = $marker;
    }

    /**
     * Set help text marker.
      *
      * @api
     */
    public function setHelpMarker(string $marker): void
    {
        $this->helpMarker = $marker;
    }

    /**
     * Set whether to encode form title.
      *
      * @api
     */
    public function setEncodeTitle(bool $encode): void
    {
        $this->encodeTitle = $encode;
    }

    /**
     * Get control renderer.
      *
      * @api
     */
    public function getControlRenderer(): ControlRenderer
    {
        return $this->controlRenderer;
    }

    /**
     * Get layout strategy.
      *
      * @api
     */
    public function getLayoutStrategy(): LayoutStrategy
    {
        return $this->layoutStrategy;
    }

    /**
     * Get error renderer.
      *
      * @api
     */
    public function getErrorRenderer(): ErrorRenderer
    {
        return $this->errorRenderer;
    }

    /**
     * Get asset manager.
      *
      * @api
     */
    public function getAssetManager(): AssetManager
    {
        return $this->assetManager;
    }

    /**
     * Collect action scripts from all variables and emit as a single script block.
     *
     * Uses CSP-friendly addEventListener bindings instead of inline handlers.
     * Helper functions (from getHelperScript()) are emitted first, then
     * event bindings.
      *
      * @internal
     */
    protected function collectActionScripts(Form $form): string
    {
        $helpers = [];
        $bindings = [];

        foreach ($form->getVariables(flat: true, withHidden: false) as $var) {
            if (!$var->hasAction()) {
                continue;
            }
            $action = $var->getAction();
            if ($action === null) {
                continue;
            }
            $varName = $var->getVarName();

            // Collect helper functions (V3 interface)
            if ($action instanceof ActionV3Interface) {
                $js = $action->getHelperScript();
                if ($js !== '') {
                    $helpers[] = $js;
                }
            }

            // Collect trigger bindings
            $triggers = $action->getTrigger();
            if (empty($triggers)) {
                continue;
            }

            $script = $action->getActionScript($form, $this, $varName);
            if ($script === '') {
                continue;
            }

            $formName = $form->getName();
            foreach ($triggers as $trigger) {
                if ($trigger === 'onload') {
                    $bindings[] = sprintf(
                        "document.addEventListener('DOMContentLoaded', function() { %s });",
                        $script,
                    );
                } else {
                    $event = ltrim($trigger, 'on');
                    $bindings[] = sprintf(
                        "document.forms['%s'].elements['%s'].addEventListener('%s', function() { %s });",
                        $formName,
                        $varName,
                        $event,
                        $script,
                    );
                }
            }
        }

        if (empty($helpers) && empty($bindings)) {
            return '';
        }

        $parts = [];
        $parts[] = '<script>';
        if ($helpers) {
            $parts[] = implode("\n", $helpers);
        }
        if ($bindings) {
            $parts[] = implode("\n", $bindings);
        }
        $parts[] = '</script>';

        return implode("\n", $parts);
    }
}
