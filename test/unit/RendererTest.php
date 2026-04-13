<?php

/**
 * Tests for the Horde_Form_Renderer class.
 *
 * Copyright 2026 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL-2.1).
 *
 * @category   Horde
 * @package    Form
 * @subpackage UnitTests
 * @author     Ralf Lang <ralf.lang@ralf-lang.de>
 * @license    http://www.horde.org/licenses/lgpl21 LGPL-2.1
 */

namespace Horde\Form\Test\Unit;

use Horde_Form;
use Horde_Form_Renderer;
use Horde_Variables;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Tests for the Horde_Form_Renderer section rendering.
 *
 * @category   Horde
 * @package    Form
 * @subpackage UnitTests
 */
#[CoversClass(Horde_Form_Renderer::class)]
class RendererTest extends TestCase
{
    /**
     * Create a Renderer instance without triggering the constructor,
     * which depends on Horde_Core_Ui_VarRenderer.
     */
    private function createRenderer(): Horde_Form_Renderer
    {
        $rc = new ReflectionClass(Horde_Form_Renderer::class);
        return $rc->newInstanceWithoutConstructor();
    }

    // ========================================================================
    // _renderSectionBegin visibility tests
    // ========================================================================

    /**
     * When no open section is set (null), _renderSectionBegin should
     * default to '__base' and display the __base section as visible.
     */
    public function testBaseSectionVisibleWhenOpenSectionIsNull(): void
    {
        $vars = new Horde_Variables();
        $form = new Horde_Form($vars, '', 'test_form');
        // No setOpenSection() call — getOpenSection() returns null
        $form->addVariable('Field', 'field', 'text', false);

        $renderer = $this->createRenderer();

        ob_start();
        $renderer->_renderSectionBegin($form, '__base');
        $output = ob_get_clean();

        $this->assertStringContainsString(
            'display:block',
            $output,
            '__base section should be visible when open section is null'
        );
    }

    /**
     * When setOpenSection(0) is called on a form that only has a __base
     * section (no explicit sections), the __base section must still be
     * displayed.
     *
     * This is the scenario triggered by Turba's View/Contact.php which
     * calls setOpenSection(Util::getFormData('section', 0)).
     *
     * In PHP 7.x, the loose comparison 0 == '__base' was true, so this
     * worked by accident. In PHP 8.0+, 0 == '__base' is false, hiding
     * all form fields.
     */
    public function testBaseSectionVisibleWhenOpenSectionIsZero(): void
    {
        $vars = new Horde_Variables();
        $form = new Horde_Form($vars, '', 'test_form');
        $form->addVariable('Field', 'field', 'text', false);
        // Simulate what Turba View/Contact.php does
        $form->setOpenSection(0);

        $renderer = $this->createRenderer();

        ob_start();
        $renderer->_renderSectionBegin($form, '__base');
        $output = ob_get_clean();

        $this->assertStringContainsString(
            'display:block',
            $output,
            '__base section should be visible when open section is 0 (integer) and no explicit sections exist'
        );
    }

    /**
     * When setOpenSection('0') is called (string '0') on a form with
     * only a __base section, the __base section must still be displayed.
     */
    public function testBaseSectionVisibleWhenOpenSectionIsZeroString(): void
    {
        $vars = new Horde_Variables();
        $form = new Horde_Form($vars, '', 'test_form');
        $form->addVariable('Field', 'field', 'text', false);
        $form->setOpenSection('0');

        $renderer = $this->createRenderer();

        ob_start();
        $renderer->_renderSectionBegin($form, '__base');
        $output = ob_get_clean();

        $this->assertStringContainsString(
            'display:block',
            $output,
            '__base section should be visible when open section is "0" (string) and no explicit sections exist'
        );
    }

    /**
     * When explicit sections exist and the open section matches one of
     * them, that section should be visible and others hidden.
     */
    public function testExplicitSectionVisibleWhenMatchesOpenSection(): void
    {
        $vars = new Horde_Variables();
        $form = new Horde_Form($vars, '', 'test_form');
        $form->setSection('general', 'General');
        $form->addVariable('Name', 'name', 'text', false);
        $form->setSection('details', 'Details');
        $form->addVariable('Notes', 'notes', 'text', false);
        $form->setOpenSection('general');

        $renderer = $this->createRenderer();

        ob_start();
        $renderer->_renderSectionBegin($form, 'general');
        $outputGeneral = ob_get_clean();

        ob_start();
        $renderer->_renderSectionBegin($form, 'details');
        $outputDetails = ob_get_clean();

        $this->assertStringContainsString(
            'display:block',
            $outputGeneral,
            'Open section should be visible'
        );
        $this->assertStringContainsString(
            'display:none',
            $outputDetails,
            'Non-open section should be hidden'
        );
    }

    /**
     * When explicit numeric sections exist (0, 1, 2...) and
     * setOpenSection(0) is called, section 0 should be visible.
     */
    public function testNumericSectionVisibleWhenMatchesOpenSection(): void
    {
        $vars = new Horde_Variables();
        $form = new Horde_Form($vars, '', 'test_form');
        $form->setSection(0, 'First Tab');
        $form->addVariable('Name', 'name', 'text', false);
        $form->setSection(1, 'Second Tab');
        $form->addVariable('Notes', 'notes', 'text', false);
        $form->setOpenSection(0);

        $renderer = $this->createRenderer();

        ob_start();
        $renderer->_renderSectionBegin($form, 0);
        $outputFirst = ob_get_clean();

        ob_start();
        $renderer->_renderSectionBegin($form, 1);
        $outputSecond = ob_get_clean();

        $this->assertStringContainsString(
            'display:block',
            $outputFirst,
            'Numeric section 0 should be visible when open section is 0'
        );
        $this->assertStringContainsString(
            'display:none',
            $outputSecond,
            'Numeric section 1 should be hidden when open section is 0'
        );
    }
}
