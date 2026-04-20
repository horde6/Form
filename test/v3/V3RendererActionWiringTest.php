<?php

declare(strict_types=1);

/**
 * Tests for BaseRenderer::collectActionScripts() — verifies that the
 * renderer emits CSP-friendly addEventListener bindings for V3 actions.
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

namespace Horde\Form\Test\V3;

use Horde\Form\V3\BaseForm;
use Horde\Form\V3\BaseRenderer;
use Horde\Form\V3\HtmlRenderer;
use Horde\Form\V3\SetcursorposAction;
use Horde\Form\V3\SubmitAction;
use Horde\Form\V3\UpdatefieldAction;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Tests that BaseRenderer::collectActionScripts() emits proper
 * addEventListener bindings in the rendered output.
 */
#[CoversClass(BaseRenderer::class)]
class V3RendererActionWiringTest extends TestCase
{
    public function testRenderEmitsScriptBlockForSubmitAction(): void
    {
        $form = new BaseForm([], 'Action Test', 'actiontest');
        $form->useToken(false);

        $var = $form->addVariable('Category', 'category', 'enum', false, false, null, [
            ['tech' => 'Technology', 'science' => 'Science'],
            true,
        ]);
        $var->setAction(new SubmitAction());

        $renderer = new HtmlRenderer();
        $output = $renderer->render($form, '/submit', 'post');

        $this->assertStringContainsString('<script>', $output);
        $this->assertStringContainsString('</script>', $output);
    }

    public function testRenderEmitsAddEventListenerForOnchange(): void
    {
        $form = new BaseForm([], 'Action Test', 'actiontest');
        $form->useToken(false);

        $var = $form->addVariable('Category', 'category', 'enum', false, false, null, [
            ['tech' => 'Technology', 'science' => 'Science'],
            true,
        ]);
        $var->setAction(new SubmitAction());

        $renderer = new HtmlRenderer();
        $output = $renderer->render($form, '/submit', 'post');

        $this->assertStringContainsString("addEventListener('change'", $output);
    }

    public function testRenderEmitsSubmitScript(): void
    {
        $form = new BaseForm([], 'Action Test', 'actiontest');
        $form->useToken(false);

        $var = $form->addVariable('Category', 'category', 'enum', false, false, null, [
            ['tech' => 'Technology', 'science' => 'Science'],
            true,
        ]);
        $var->setAction(new SubmitAction());

        $renderer = new HtmlRenderer();
        $output = $renderer->render($form, '/submit', 'post');

        $this->assertStringContainsString('.submit()', $output);
    }

    public function testRenderEmitsFormNameInScript(): void
    {
        $form = new BaseForm([], 'Action Test', 'actiontest');
        $form->useToken(false);

        $var = $form->addVariable('Category', 'category', 'enum', false, false, null, [
            ['tech' => 'Technology', 'science' => 'Science'],
            true,
        ]);
        $var->setAction(new SubmitAction());

        $renderer = new HtmlRenderer();
        $output = $renderer->render($form, '/submit', 'post');

        $this->assertStringContainsString("document.forms['actiontest']", $output);
    }

    public function testRenderEmitsVariableNameInScript(): void
    {
        $form = new BaseForm([], 'Action Test', 'actiontest');
        $form->useToken(false);

        $var = $form->addVariable('Category', 'category', 'enum', false, false, null, [
            ['tech' => 'Technology', 'science' => 'Science'],
            true,
        ]);
        $var->setAction(new SubmitAction());

        $renderer = new HtmlRenderer();
        $output = $renderer->render($form, '/submit', 'post');

        $this->assertStringContainsString("elements['category']", $output);
    }

    public function testRenderNoScriptBlockWithoutActions(): void
    {
        $form = new BaseForm([], 'No Action Form', 'noaction');
        $form->useToken(false);

        $form->addVariable('Name', 'name', 'text', false);

        $renderer = new HtmlRenderer();
        $output = $renderer->render($form, '/submit', 'post');

        $this->assertStringNotContainsString('<script>', $output);
    }

    public function testRenderEmitsDOMContentLoadedForOnloadTrigger(): void
    {
        $form = new BaseForm([], 'Cursor Test', 'cursortest');
        $form->useToken(false);

        $var = $form->addVariable('Input', 'myinput', 'text', false);
        $var->setAction(new SetcursorposAction([0, 5]));

        $renderer = new HtmlRenderer();
        $output = $renderer->render($form, '/submit', 'post');

        $this->assertStringContainsString('DOMContentLoaded', $output);
    }

    public function testRenderEmitsHelperFunctionsBeforeBindings(): void
    {
        $form = new BaseForm([], 'Helper Test', 'helpertest');
        $form->useToken(false);

        $var = $form->addVariable('Input', 'myinput', 'text', false);
        $var->setAction(new SetcursorposAction([0, 5]));

        $renderer = new HtmlRenderer();
        $output = $renderer->render($form, '/submit', 'post');

        $helperPos = strpos($output, 'function setCursorPosition_');
        $bindingPos = strpos($output, 'DOMContentLoaded');

        $this->assertNotFalse($helperPos);
        $this->assertNotFalse($bindingPos);
        $this->assertLessThan($bindingPos, $helperPos, 'Helper function must be defined before event binding');
    }

    public function testRenderScriptAfterFormClose(): void
    {
        $form = new BaseForm([], 'Script Position', 'scriptpos');
        $form->useToken(false);

        $var = $form->addVariable('Category', 'category', 'enum', false, false, null, [
            ['a' => 'Alpha', 'b' => 'Beta'],
            true,
        ]);
        $var->setAction(new SubmitAction());

        $renderer = new HtmlRenderer();
        $output = $renderer->render($form, '/submit', 'post');

        $formClosePos = strpos($output, '</form>');
        $scriptPos = strpos($output, '<script>');

        $this->assertNotFalse($formClosePos);
        $this->assertNotFalse($scriptPos);
        $this->assertLessThan($scriptPos, $formClosePos, '<script> must appear after </form>');
    }

    public function testRenderWithUpdatefieldEmitsHelperAndBindings(): void
    {
        $form = new BaseForm([], 'Update Field Test', 'updatetest');
        $form->useToken(false);

        $var = $form->addVariable('First Name', 'firstname', 'text', false);
        $var->setAction(new UpdatefieldAction([
            'target' => 'fullname',
            'format' => '%s %s',
            'fields' => ['firstname', 'lastname'],
        ]));
        $form->addVariable('Last Name', 'lastname', 'text', false);
        $form->addVariable('Full Name', 'fullname', 'text', false);

        $renderer = new HtmlRenderer();
        $output = $renderer->render($form, '/submit', 'post');

        // Should have helper function
        $this->assertStringContainsString('function updateField_', $output);
        // Should have change event binding
        $this->assertStringContainsString("addEventListener('change'", $output);
        // Should have keyup event binding
        $this->assertStringContainsString("addEventListener('keyup'", $output);
        // Should have DOMContentLoaded for onload trigger
        $this->assertStringContainsString('DOMContentLoaded', $output);
    }
}
