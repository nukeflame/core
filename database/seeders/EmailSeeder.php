<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class EmailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $faker = Faker::create();

        for ($i = 0; $i < 100; $i++) {
            $messageId = Str::uuid()->toString();
            $threadId = rand(0, 1) ? Str::uuid()->toString() : null;
            $inReplyTo = $threadId ? Str::uuid()->toString() : null;

            $smsFull = $faker->paragraphs(3, true);

            DB::table('emails')->insert([
                'message_id' => $messageId,
                'thread_id' => $threadId,
                'in_reply_to' => $inReplyTo,
                'subject' => $faker->sentence(),
                'body' => $smsFull,
                'from' => json_encode([
                    'name' => $faker->name(),
                    'email' =>  $faker->email()
                ]),
                'snippet' => Str::limit($smsFull, 250),
                'to' => json_encode([
                    ['name' => 'Recipient One', 'email' => 'recipient1@example.com'],
                    ['name' => 'Recipient Two', 'email' => 'recipient2@example.com']
                ]),
                'cc' => json_encode([
                    ['name' => 'CC Person', 'email' => 'cc@example.com']
                ]),
                'bcc' => null,
                'direction' => ['inbound', 'outbound'][rand(0, 1)],
                'status' => ['pending', 'sent', 'failed', 'received'][rand(0, 3)],
                'headers' => json_encode(['X-Mailer' => 'Laravel Mailer']),
                'attachments' => json_encode([
                    ['filename' => 'file.pdf', 'path' => '/attachments/file.pdf', 'size' => random_int(10000, 500000)],
                    ['filename' => 'file.jpg', 'path' => '/attachments/file.jpg', 'size' => random_int(10000, 500000)]
                ]),
                'folder' => ['inbox', 'sent', 'archive'][rand(0, 2)],
                'email_date' => Carbon::now()->subDays(rand(0, 30)),
                'starred' => rand(0, 1),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
