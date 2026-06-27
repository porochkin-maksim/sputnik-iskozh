<?php declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Test UPDATE query logic ===\n\n";

$preview = DB::select("
    SELECT c.id, c.original_service_id, c.service_id,
           (SELECT pc.id FROM claims pc
            JOIN invoices pi ON pc.invoice_id = pi.id
            JOIN periods pp ON pi.period_id = pp.id
            WHERE pi.account_id = i.account_id
            AND pp.end_at < p.start_at
            AND pp.is_closed = 1
            AND (pc.cost - pc.paid) != 0
            AND pc.service_id = c.original_service_id
            ORDER BY pp.end_at DESC LIMIT 1) AS match_id,
           p.name AS period, i.account_id
    FROM claims c
    JOIN invoices i ON c.invoice_id = i.id
    JOIN periods p ON i.period_id = p.id
    WHERE c.original_service_id IS NOT NULL
    AND c.original_service_id != c.service_id
    AND c.original_claim_id IS NULL
    ORDER BY p.start_at, i.account_id
    LIMIT 15
");
echo "First 15:\n";
foreach ($preview as $c) {
    echo "  #{$c->id} ({$c->period}, acct #{$c->account_id}): orig_svc={$c->original_service_id} → match={$c->match_id}\n";
}

echo "\n=== Null matches ===\n";
$nulls = DB::selectOne("
    SELECT COUNT(*) AS cnt FROM claims c
    JOIN invoices i ON c.invoice_id = i.id
    JOIN periods p ON i.period_id = p.id
    WHERE c.original_service_id IS NOT NULL AND c.original_service_id != c.service_id
    AND c.original_claim_id IS NULL
    AND (SELECT pc.id FROM claims pc JOIN invoices pi ON pc.invoice_id = pi.id
         JOIN periods pp ON pi.period_id = pp.id
         WHERE pi.account_id = i.account_id AND pp.end_at < p.start_at
         AND pp.is_closed = 1 AND (pc.cost - pc.paid) != 0
         AND pc.service_id = c.original_service_id
         ORDER BY pp.end_at DESC LIMIT 1) IS NULL
");
echo "  No match: {$nulls->cnt}/1388\n";

echo "\n=== Claim 5243 match ===\n";
$c5243 = DB::selectOne("
    SELECT (SELECT pc.id FROM claims pc JOIN invoices pi ON pc.invoice_id = pi.id
            JOIN periods pp ON pi.period_id = pp.id
            WHERE pi.account_id = i.account_id AND pp.end_at < p.start_at
            AND pp.is_closed = 1 AND (pc.cost - pc.paid) != 0
            AND pc.service_id = c.original_service_id
            ORDER BY pp.end_at DESC LIMIT 1) AS match_id
    FROM claims c JOIN invoices i ON c.invoice_id = i.id
    JOIN periods p ON i.period_id = p.id WHERE c.id = 5243
");
echo "  5243 → {$c5243->match_id}\n";
