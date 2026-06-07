<?php

declare(strict_types=1);

namespace Tests\Unit\Communication;

use App\Modules\Communication\Enums\CommunicationChannel;
use App\Modules\Communication\Support\RecipientValidator;
use Tests\TestCase;

class RecipientValidatorTest extends TestCase
{
    public function test_it_validates_sms_recipients(): void
    {
        $validator = new RecipientValidator();

        $this->assertTrue($validator->isValid(CommunicationChannel::Sms, '+8801712345678'));
        $this->assertFalse($validator->isValid(CommunicationChannel::Sms, '123'));
    }

    public function test_it_validates_email_recipients(): void
    {
        $validator = new RecipientValidator();

        $this->assertTrue($validator->isValid(CommunicationChannel::Email, 'user@example.com'));
        $this->assertFalse($validator->isValid(CommunicationChannel::Email, 'not-an-email'));
    }
}
