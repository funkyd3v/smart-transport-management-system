<?php

declare(strict_types=1);

namespace App\Modules\Communication\Support;

class TemplateRenderer
{
    public function render(string $template, array $data): string
    {
        $replacements = [];

        foreach ($data as $key => $value) {
            $replacements['{{'.$key.'}}'] = (string) $value;
        }

        return strtr($template, $replacements);
    }
}
