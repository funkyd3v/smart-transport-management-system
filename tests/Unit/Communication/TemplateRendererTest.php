<?php

declare(strict_types=1);

namespace Tests\Unit\Communication;

use App\Modules\Communication\Support\TemplateRenderer;
use Tests\TestCase;

class TemplateRendererTest extends TestCase
{
    public function test_it_renders_template_placeholders(): void
    {
        $renderer = new TemplateRenderer();

        $result = $renderer->render('Hello {{customer_name}}, invoice {{invoice_number}} is ready.', [
            'customer_name' => 'Rahim',
            'invoice_number' => 'INV-1001',
        ]);

        $this->assertSame('Hello Rahim, invoice INV-1001 is ready.', $result);
    }
}
