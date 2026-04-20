<?php

namespace App\Services;

use Google\Client as GoogleClient;
use Google\Service\Gmail;
use Google\Service\Gmail\Message;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class GmailService
{
    protected $client;
    protected $service;

    public function __construct()
    {
        $this->client = new GoogleClient();
        $this->client->setApplicationName(config('app.name'));
        $this->client->setScopes([
            Gmail::GMAIL_SEND,
            Gmail::GMAIL_COMPOSE,
        ]);
        $this->client->setAuthConfig([
            'client_id' => config('services.google.client_id'),
            'client_secret' => config('services.google.client_secret'),
            'redirect_uris' => [config('services.google.redirect')],
        ]);
        $this->client->setAccessType('offline');
        $this->client->setPrompt('consent');
    }

    public function setUserToken(User $user)
    {
        if (!$user->google_access_token) {
            Log::warning('User does not have Google token', ['user_id' => $user->id]);
            return false;
        }

        // Decode token if it's stored as JSON string
        $accessToken = $user->google_access_token;
        if (is_string($accessToken)) {
            $accessToken = json_decode($accessToken, true);
        }

        $this->client->setAccessToken($accessToken);

        // Refresh token if expired
        if ($this->client->isAccessTokenExpired()) {
            if ($user->google_refresh_token) {
                $this->client->fetchAccessTokenWithRefreshToken($user->google_refresh_token);
                $newToken = $this->client->getAccessToken();
                
                $user->update([
                    'google_access_token' => json_encode($newToken)
                ]);
            } else {
                Log::warning('User Google token expired and no refresh token', ['user_id' => $user->id]);
                return false;
            }
        }

        $this->service = new Gmail($this->client);
        return true;
    }

    public function sendEmail($to, $subject, $body, $from = null)
    {
        if (!$this->service) {
            throw new \Exception('Gmail service not initialized. Please set user token first.');
        }

        try {
            $fromEmail = $from ?? config('mail.from.address');
            $fromName = config('mail.from.name');

            $message = $this->createMessage(
                $fromEmail,
                $to,
                $subject,
                $body
            );

            $sentMessage = $this->service->users_messages->send('me', $message);
            
            Log::info('Email sent via Gmail', [
                'message_id' => $sentMessage->id,
                'to' => $to,
                'subject' => $subject
            ]);

            return $sentMessage;
        } catch (\Exception $e) {
            Log::error('Failed to send email via Gmail', [
                'error' => $e->getMessage(),
                'to' => $to,
                'subject' => $subject
            ]);
            throw $e;
        }
    }

    protected function createMessage($from, $to, $subject, $body)
    {
        // Encode subject line properly for UTF-8 characters (emoji, etc.)
        $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
        
        $message = "From: $from\r\n";
        $message .= "To: $to\r\n";
        $message .= "Subject: $encodedSubject\r\n";
        $message .= "MIME-Version: 1.0\r\n";
        $message .= "Content-Type: text/html; charset=utf-8\r\n";
        $message .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $message .= base64_encode($body);

        $rawMessage = base64_encode($message);
        $rawMessage = str_replace(['+', '/', '='], ['-', '_', ''], $rawMessage);

        $gmailMessage = new Message();
        $gmailMessage->setRaw($rawMessage);

        return $gmailMessage;
    }

    public function sendTaskNotification($task, $recipients)
    {
        $subject = "New Task Assigned: {$task->title}";
        
        $body = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 10px; }
                    .content { background: #f9fafb; padding: 20px; border-radius: 10px; margin-top: 20px; }
                    .task-details { background: white; padding: 15px; border-radius: 8px; margin: 10px 0; }
                    .label { font-weight: bold; color: #4F46E5; }
                    .footer { text-align: center; color: #6b7280; font-size: 12px; margin-top: 20px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2>FlowSpec AI - Task Assignment</h2>
                    </div>
                    <div class='content'>
                        <h3>You have been assigned a new task</h3>
                        <div class='task-details'>
                            <p><span class='label'>Task:</span> {$task->title}</p>
                            <p><span class='label'>Description:</span> {$task->description}</p>
                            <p><span class='label'>Due Date:</span> " . ($task->due_date ? $task->due_date->format('F d, Y') : 'No due date') . "</p>
                            <p><span class='label'>Priority:</span> " . ucfirst($task->priority ?? 'normal') . "</p>
                            <p><span class='label'>Status:</span> " . ucfirst($task->status) . "</p>
                        </div>
                        <p>Please log in to FlowSpec AI to view and manage this task.</p>
                    </div>
                    <div class='footer'>
                        <p>This is an automated message from FlowSpec AI</p>
                        <p>© " . date('Y') . " FlowSpec AI. All rights reserved.</p>
                    </div>
                </div>
            </body>
            </html>
        ";

        foreach ($recipients as $recipient) {
            $this->sendEmail($recipient, $subject, $body);
        }
    }

    public function sendMeetingInvitation($meeting, $recipients)
    {
        $subject = "Meeting Invitation: {$meeting->title}";
        
        $body = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: linear-gradient(135deg, #10B981 0%, #059669 100%); color: white; padding: 20px; border-radius: 10px; }
                    .content { background: #f9fafb; padding: 20px; border-radius: 10px; margin-top: 20px; }
                    .meeting-details { background: white; padding: 15px; border-radius: 8px; margin: 10px 0; }
                    .label { font-weight: bold; color: #10B981; }
                    .footer { text-align: center; color: #6b7280; font-size: 12px; margin-top: 20px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2>FlowSpec AI - Meeting Invitation</h2>
                    </div>
                    <div class='content'>
                        <h3>You are invited to a meeting</h3>
                        <div class='meeting-details'>
                            <p><span class='label'>Meeting:</span> {$meeting->title}</p>
                            <p><span class='label'>Description:</span> {$meeting->description}</p>
                            <p><span class='label'>Start Time:</span> " . $meeting->start_time->format('F d, Y h:i A') . "</p>
                            <p><span class='label'>End Time:</span> " . $meeting->end_time->format('F d, Y h:i A') . "</p>
                            " . ($meeting->location ? "<p><span class='label'>Location:</span> {$meeting->location}</p>" : "") . "
                        </div>
                        <p>This meeting has been added to your Google Calendar.</p>
                    </div>
                    <div class='footer'>
                        <p>This is an automated message from FlowSpec AI</p>
                        <p>© " . date('Y') . " FlowSpec AI. All rights reserved.</p>
                    </div>
                </div>
            </body>
            </html>
        ";

        foreach ($recipients as $recipient) {
            $this->sendEmail($recipient, $subject, $body);
        }
    }
}
