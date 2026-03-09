<?php

namespace Nukeflame\Core;

class ReCore
{
    public function licence(string $owner, string $product = 'nukeflame-core'): string
    {
        $payload = strtoupper(trim($owner)) . '|' . strtoupper(trim($product)) . '|' . date('Ymd');

        return hash('sha256', $payload);
    }
}
