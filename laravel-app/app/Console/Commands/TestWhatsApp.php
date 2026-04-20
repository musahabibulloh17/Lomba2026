<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\FonnteService;

class TestWhatsApp extends Command
{
    protected $signature = 'test:whatsapp {phone?} {message?}';
    protected $description = 'Test WhatsApp notification';

    public function handle()
    {
        $phone = $this->argument('phone');
        $message = $this->argument('message');

        if (!$phone) {
            // Check user's phone
            $user = User::first();
            
            if (!$user) {
                $this->error('No user found!');
                return 1;
            }

            $this->info("User: {$user->name}");
            $this->info("Email: {$user->email}");
            $this->info("Phone: " . ($user->phone_number ?? 'NOT SET'));
            $this->info("WA Enabled: " . ($user->whatsapp_notifications ? 'YES' : 'NO'));
            
            if (!$user->phone_number) {
                $this->warn("\nPhone number not set!");
                
                $phone = $this->ask('Enter phone number to test (e.g., 081234567890)');
                if ($phone) {
                    $user->phone_number = $phone;
                    $user->whatsapp_notifications = true;
                    $user->save();
                    $this->info("Phone number saved: {$user->phone_number}");
                } else {
                    return 1;
                }
            }
            
            $phone = $user->phone_number;
        }

        if (!$message) {
            $message = "🔔 *Test WhatsApp - FlowSpec AI*\n\nHello! This is a test message from FlowSpec AI.\n\nIf you receive this, WhatsApp notifications are working! ✅\n\n" . now()->format('Y-m-d H:i:s');
        }

        $this->info("\n📤 Sending WhatsApp message...");
        $this->info("To: {$phone}");
        
        $fonnte = app(FonnteService::class);
        $result = $fonnte->sendMessage($phone, $message);

        if ($result['success']) {
            $this->info("✅ Message sent successfully!");
            $this->info(json_encode($result['data'] ?? [], JSON_PRETTY_PRINT));
        } else {
            $this->error("❌ Failed to send message!");
            $this->error("Error: " . $result['message']);
            if (isset($result['error'])) {
                $this->error(json_encode($result['error'], JSON_PRETTY_PRINT));
            }
        }

        return 0;
    }
}
