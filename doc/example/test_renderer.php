<?php

declare(strict_types=1);

/**
 * Quick integration test for renderer system.
 */

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../test/stubs/HordeStubs.php';

use Horde\Form\V3\BaseForm;
use Horde\Form\V3\HtmlRenderer;

echo "Testing V3 Renderer...\n\n";

// Test 1: Create form with fields
echo "Test 1: Create form\n";
$form = new BaseForm(
    vars: ['name' => 'John Doe', 'email' => 'john@example.com'],
    title: 'Contact Form'
);

$form->addVariable(
    humanName: 'Full Name',
    varName: 'name',
    type: 'text',
    required: true,
    description: 'Enter your full name'
);

$form->addVariable(
    humanName: 'Email Address',
    varName: 'email',
    type: 'email',
    required: true,
    description: 'Enter your email'
);

$form->addVariable(
    humanName: 'Country',
    varName: 'country',
    type: 'enum',
    required: false,
    params: ['values' => ['us' => 'United States', 'ca' => 'Canada', 'uk' => 'United Kingdom'], 'prompt' => 'Select country...']
);

echo "✓ Form created with " . count($form->getVariables()) . " fields\n\n";

// Test 2: Render form
echo "Test 2: Render form\n";
$renderer = new HtmlRenderer();

try {
    $html = $renderer->render($form, '/submit', 'post');
    echo "✓ Form rendered (" . strlen($html) . " bytes)\n\n";

    // Output HTML
    echo "Generated HTML:\n";
    echo str_repeat('=', 60) . "\n";
    echo $html . "\n";
    echo str_repeat('=', 60) . "\n\n";

} catch (Exception $e) {
    echo "✗ Render failed: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

// Test 3: Render with errors
echo "Test 3: Render with validation errors\n";
$form->setError('email', 'Invalid email format');
$htmlWithErrors = $renderer->render($form, '/submit', 'post');
echo "✓ Form with errors rendered (" . strlen($htmlWithErrors) . " bytes)\n";

if (strpos($htmlWithErrors, 'Invalid email format') !== false) {
    echo "✓ Error message found in output\n";
} else {
    echo "✗ Error message NOT found in output\n";
}

echo "\n========================================\n";
echo "Renderer tests complete!\n";
echo "========================================\n";
