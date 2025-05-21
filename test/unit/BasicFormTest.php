<?php

/**
 * Test the Horde_Auth:: class.
 *
 * Copyright 2010-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category   Horde
 * @package    Auth
 * @subpackage UnitTests
 * @author     Gunnar Wrobel <wrobel@pardus.de>
 * @license    http://www.horde.org/licenses/lgpl21 LGPL-2.1
 */

namespace Horde\Form\Test\Unit;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use Horde_Form;
use Horde_Variables;

#[CoversNothing]
class BasicFormTest extends TestCase
{
    public function testEmptyFormCreation()
    {
        // Traditionally, Horde_Form accepts any variable as the input.
        $vars = null;
        $form = new Horde_Form($vars);
        $this->assertInstanceOf(Horde_Form::class, $form);
    }
    public function testFormCreationWithTitle()
    {
        $vars = null;
        $form = new Horde_Form($vars, 'test');
        $this->assertInstanceOf(Horde_Form::class, $form);
        $form = new Horde_Form($vars, 'testTitle', 'testForm');
        $this->assertInstanceOf(Horde_Form::class, $form);
        $this->assertEquals('testTitle', $form->getTitle());
        $this->assertEquals('testForm', $form->getName());
    }

    public function testGetSetTitle()
    {
        $vars = null;
        $form = new Horde_Form($vars, 'testTitle');
        $this->assertEquals('testTitle', $form->getTitle());
        $form->setTitle('newTitle');
        $this->assertEquals('newTitle', $form->getTitle());
    }
    public function testGetName()
    {
        $vars = null;
        $form = new Horde_Form($vars, name: 'testForm');
        $this->assertEquals('testForm', $form->getName());
    }

    public function testAddVariable()
    {
        $vars = null;
        $form = new Horde_Form($vars, name: 'testForm');
        $form->addVariable(humanName: 'Test Field Variable', varName: 'testField', type: 'text', required: false, readonly: false, description: 'testDescription', params: []);
        $varsRetrieved = $form->getVariables();
        $this->assertCount(1, $varsRetrieved);
    }

    public function testGetVariablesVarIsIdenticToInputVarIfObject()
    {
        $vars = new \ArrayObject();
        $form = new Horde_Form($vars, name: 'testForm');
        // getVars is not getVariables!
        $this->assertSame($vars, $form->getVars());
    }
    public function testGetVariablesVarIsIdenticToInputVarIfArray()
    {
        $vars = ['test' => 'value'];
        $form = new Horde_Form($vars, name: 'testForm');
        // getVars is not getVariables!
        $this->assertSame($vars, $form->getVars());
    }

    public function testGetInfoWithSimpleTextFields()
    {
        $vars = new Horde_Variables(['testField1' => 'value1', 'testField2' => 'value2']);
        $info = [];
        $form = new Horde_Form($vars, name: 'testFormWithTwoTextFields');
        $form->addVariable(humanName: 'Test Field Variable 1', varName: 'testField1', type: 'text', required: false, readonly: false, description: 'testDescription1', params: []);
        $form->addVariable(humanName: 'Test Field Variable 2', varName: 'testField2', type: 'text', required: false, readonly: false, description: 'testDescription2', params: []);
        $result = $form->getInfo(null, $info);
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertArrayHasKey('testField1', $result);
        $this->assertArrayHasKey('testField2', $result);
        $this->assertEquals('value1', $result['testField1']);
        $this->assertEquals('value2', $result['testField2']);   
    }
}
