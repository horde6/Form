<?php

declare(strict_types=1);

/**
 * Demo: PSR-15 Controller with Form V3
 *
 * Reference implementation showing how to use Horde Form V3 inside
 * a PSR-15 RequestHandler.  Not directly runnable (requires a PSR-7
 * implementation and a router) but serves as a working pattern.
 *
 * Wire this handler to a route like POST /contact in your router.
 *
 * Copyright 2026 Horde LLC (http://www.horde.org/)
 *
 * @category Horde
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package  Form
 */

namespace Horde\Form\Example;

use Horde\Form\V3\BaseForm;
use Horde\Form\V3\HtmlRenderer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DemoController implements RequestHandlerInterface
{
    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly StreamFactoryInterface $streamFactory,
    ) {}

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // BaseForm accepts PSR-7 ServerRequest directly.
        // POST/PUT/PATCH → getParsedBody(), GET → getQueryParams().
        $form = $this->buildForm($request);

        if ($request->getMethod() === 'POST' && $form->isSubmitted()) {
            if ($form->validate()) {
                return $this->handleValid($form);
            }
            // Fall through to re-render with errors
        }

        return $this->renderForm($form);
    }

    private function buildForm(ServerRequestInterface $request): BaseForm
    {
        // Real apps should inject a Token service for CSRF protection:
        //   $form = new BaseForm($request, 'Contact Us', token: $token);
        $form = new BaseForm($request, 'Contact Us');
        $form->useToken(false);

        // --- Personal Info ---
        $form->setSection('personal', 'Personal Information', expanded: true);
        $form->addVariable('Full Name', 'name', 'text', true, false, 'First and last name');
        $form->addVariable('Email', 'email', 'email', true);
        $form->addVariable('Phone', 'phone', 'phone', false);

        // --- Address ---
        $form->setSection('address', 'Mailing Address');
        $form->addVariable('Street', 'street', 'text', false);
        $form->addVariable('City', 'city', 'text', false);
        $form->addVariable('Country', 'country', 'enum', false, false, null, [
            [
                'us' => 'United States',
                'de' => 'Germany',
                'fr' => 'France',
                'jp' => 'Japan',
            ],
            'Select a country...',
        ]);

        // --- Preferences ---
        $form->setSection('prefs', 'Preferences', expanded: false);
        $form->addVariable('Subscribe to Newsletter', 'newsletter', 'boolean', false);
        $form->addVariable('Preferred Contact Method', 'contact_method', 'radio', false, false, null, [
            ['email' => 'Email', 'phone' => 'Phone', 'mail' => 'Mail'],
        ]);
        $form->addVariable('Interests', 'interests', 'multienum', false, false, null, [
            [
                'development' => 'Software Development',
                'sysadmin' => 'System Administration',
                'groupware' => 'Groupware',
                'migration' => 'Migration Services',
            ],
        ]);
        $form->addVariable('Comments', 'comments', 'longtext', false, false, null, [8, 80]);

        $form->addHidden('Form Source', 'source', 'text', false);

        $form->setButtons(['Send Message'], false);

        return $form;
    }

    private function handleValid(BaseForm $form): ResponseInterface
    {
        $info = $form->getInfo();

        $body = $this->streamFactory->createStream(
            json_encode(['status' => 'ok', 'data' => $info], JSON_PRETTY_PRINT)
        );

        return $this->responseFactory
            ->createResponse(200)
            ->withHeader('Content-Type', 'application/json')
            ->withBody($body);
    }

    private function renderForm(BaseForm $form): ResponseInterface
    {
        $renderer = new HtmlRenderer();
        $html = $renderer->render($form, '/contact', 'post');

        $page = <<<HTML
            <!DOCTYPE html>
            <html lang="en">
            <head><meta charset="utf-8"><title>{$form->getTitle()}</title></head>
            <body>
            {$html}
            </body>
            </html>
            HTML;

        $body = $this->streamFactory->createStream($page);

        return $this->responseFactory
            ->createResponse(200)
            ->withHeader('Content-Type', 'text/html; charset=utf-8')
            ->withBody($body);
    }
}
