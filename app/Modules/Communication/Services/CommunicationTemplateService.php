<?php

declare(strict_types=1);

namespace App\Modules\Communication\Services;

use App\Modules\Communication\Enums\CommunicationChannel;
use App\Modules\Communication\Repositories\CommunicationRepositoryInterface;
use App\Modules\Communication\Support\TemplateRenderer;

class CommunicationTemplateService
{
    public function __construct(
        private readonly CommunicationRepositoryInterface $repository,
        private readonly TemplateRenderer $renderer,
    ) {}

    public function resolveBody(?string $templateKey, CommunicationChannel $channel, string $fallbackBody, array $data): string
    {
        if ($templateKey === null || $templateKey === '') {
            return $fallbackBody;
        }

        $template = $this->repository->findTemplate($templateKey, $channel->value);

        if ($template === null) {
            return $fallbackBody;
        }

        return $this->renderer->render((string) $template->body_template, $data);
    }

    public function resolveSubject(?string $templateKey, CommunicationChannel $channel, string $fallbackSubject, array $data): string
    {
        if ($templateKey === null || $templateKey === '') {
            return $fallbackSubject;
        }

        $template = $this->repository->findTemplate($templateKey, $channel->value);

        if ($template === null || $template->subject_template === null) {
            return $fallbackSubject;
        }

        return $this->renderer->render((string) $template->subject_template, $data);
    }
}
