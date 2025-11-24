<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class SystemActionEnums extends Enum
{
    const CLAIM_INTIMATION_PROCESS = 'claim_intimation_process';
    const CLAIM_VERIFICATION_PROCESS = 'claim_verification_process';
    const CLAIM_REGISTRATION = 'claim_registration';

    const VERIFY_CLAIM_INTIMATION_PROCESS = 'verify_claim_intimation_process';
    const VERIFY_COVER_PROCESS = 'verify_cover';
}
