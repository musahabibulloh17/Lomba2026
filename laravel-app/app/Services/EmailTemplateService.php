<?php

namespace App\Services;

class EmailTemplateService
{
    public static function meetingInvitation($meeting, $startTime, $endTime, $organizer, $meetLink = null)
    {
        $duration = $startTime->diffInMinutes($endTime);
        
        $html = "
        <html>
        <head>
            <style>
                body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5; margin: 0; padding: 0; }
                .container { max-width: 600px; margin: 30px auto; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; }
                .header h1 { margin: 0; font-size: 28px; font-weight: 600; }
                .header p { margin: 5px 0 0 0; opacity: 0.9; }
                .content { padding: 30px; }
                .meeting-card { background: #f9fafb; border-left: 4px solid #667eea; padding: 20px; border-radius: 8px; margin: 20px 0; }
                .meeting-title { font-size: 22px; font-weight: 600; color: #1f2937; margin: 0 0 15px 0; }
                .detail-row { margin: 12px 0; }
                .detail-label { font-weight: 600; color: #4b5563; }
                .detail-value { color: #1f2937; }
                .meet-button { display: inline-block; background: linear-gradient(135deg, #34D399 0%, #10B981 100%); color: white; padding: 14px 32px; text-decoration: none; border-radius: 8px; font-weight: 600; margin: 20px 0; text-align: center; box-shadow: 0 2px 4px rgba(16, 185, 129, 0.3); }
                .description { background: white; border: 1px solid #e5e7eb; padding: 15px; border-radius: 6px; margin: 15px 0; color: #4b5563; }
                .footer { background: #f9fafb; padding: 20px; text-align: center; color: #6b7280; font-size: 13px; border-top: 1px solid #e5e7eb; }
                .organizer { margin-top: 20px; padding-top: 20px; border-top: 1px solid #e5e7eb; color: #6b7280; font-size: 14px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>📅 Meeting Invitation</h1>
                    <p>You've been invited to a meeting</p>
                </div>
                <div class='content'>
                    <div class='meeting-card'>
                        <h2 class='meeting-title'>{$meeting->title}</h2>
                        
                        <div class='detail-row'>
                            <div class='detail-label'>📅 Date:</div>
                            <div class='detail-value'>{$startTime->format('l, F j, Y')}</div>
                        </div>
                        
                        <div class='detail-row'>
                            <div class='detail-label'>⏰ Time:</div>
                            <div class='detail-value'>{$startTime->format('g:i A')} - {$endTime->format('g:i A')} ({$duration} minutes)</div>
                        </div>";
        
        if ($meetLink) {
            $html .= "
                        
                        <div style='text-align: center; margin: 25px 0;'>
                            <a href='{$meetLink}' class='meet-button'>🎥 Join Google Meet</a>
                        </div>
                        
                        <div class='detail-row'>
                            <div class='detail-label'>🔗 Meeting Link:</div>
                            <div class='detail-value'><a href='{$meetLink}' style='color: #667eea;'>{$meetLink}</a></div>
                        </div>";
        }
        
        if ($meeting->description) {
            $html .= "
                        
                        <div class='description'>
                            <strong>📝 Description:</strong><br>
                            {$meeting->description}
                        </div>";
        }
        
        $html .= "
                        
                        <div class='organizer'>
                            <strong>Organized by:</strong> {$organizer->name}<br>
                            <strong>Email:</strong> {$organizer->email}
                        </div>
                    </div>
                    
                    <p style='color: #6b7280; font-size: 14px; margin-top: 20px;'>
                        ✅ This event has been automatically added to your Google Calendar.
                    </p>
                </div>
                <div class='footer'>
                    <p><strong>FlowSpec AI</strong> - Automated Workflow Management</p>
                    <p>Powered by Gemini AI & Google Workspace</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        return $html;
    }
    
    public static function meetingConfirmation($meeting, $startTime, $endTime, $organizer, $attendees = [], $meetLink = null)
    {
        $duration = $startTime->diffInMinutes($endTime);
        
        $html = "
        <html>
        <head>
            <style>
                body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5; margin: 0; padding: 0; }
                .container { max-width: 600px; margin: 30px auto; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
                .header { background: linear-gradient(135deg, #10B981 0%, #059669 100%); color: white; padding: 30px; text-align: center; }
                .header h1 { margin: 0; font-size: 28px; font-weight: 600; }
                .header p { margin: 5px 0 0 0; opacity: 0.9; }
                .content { padding: 30px; }
                .success-badge { background: #D1FAE5; color: #065F46; padding: 10px 20px; border-radius: 20px; display: inline-block; font-weight: 600; margin-bottom: 20px; }
                .meeting-card { background: #f9fafb; border-left: 4px solid #10B981; padding: 20px; border-radius: 8px; margin: 20px 0; }
                .meeting-title { font-size: 22px; font-weight: 600; color: #1f2937; margin: 0 0 15px 0; }
                .detail-row { margin: 12px 0; }
                .detail-label { font-weight: 600; color: #4b5563; }
                .detail-value { color: #1f2937; }
                .meet-button { display: inline-block; background: linear-gradient(135deg, #34D399 0%, #10B981 100%); color: white; padding: 14px 32px; text-decoration: none; border-radius: 8px; font-weight: 600; margin: 20px 0; text-align: center; box-shadow: 0 2px 4px rgba(16, 185, 129, 0.3); }
                .attendees-list { background: white; border: 1px solid #e5e7eb; padding: 15px; border-radius: 6px; margin: 15px 0; }
                .attendee-item { padding: 8px; border-bottom: 1px solid #f3f4f6; }
                .attendee-item:last-child { border-bottom: none; }
                .footer { background: #f9fafb; padding: 20px; text-align: center; color: #6b7280; font-size: 13px; border-top: 1px solid #e5e7eb; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>✅ Meeting Confirmed</h1>
                    <p>Your meeting has been successfully scheduled</p>
                </div>
                <div class='content'>
                    <div class='success-badge'>✓ Successfully Created</div>
                    
                    <div class='meeting-card'>
                        <h2 class='meeting-title'>{$meeting->title}</h2>
                        
                        <div class='detail-row'>
                            <div class='detail-label'>📅 Date:</div>
                            <div class='detail-value'>{$startTime->format('l, F j, Y')}</div>
                        </div>
                        
                        <div class='detail-row'>
                            <div class='detail-label'>⏰ Time:</div>
                            <div class='detail-value'>{$startTime->format('g:i A')} - {$endTime->format('g:i A')} ({$duration} minutes)</div>
                        </div>";
        
        if ($meetLink) {
            $html .= "
                        
                        <div style='text-align: center; margin: 25px 0;'>
                            <a href='{$meetLink}' class='meet-button'>🎥 Join Google Meet</a>
                        </div>
                        
                        <div class='detail-row'>
                            <div class='detail-label'>🔗 Meeting Link:</div>
                            <div class='detail-value'><a href='{$meetLink}' style='color: #10B981;'>{$meetLink}</a></div>
                        </div>";
        }
        
        if (!empty($attendees)) {
            $html .= "
                        
                        <div class='attendees-list'>
                            <strong>👥 Attendees (" . count($attendees) . "):</strong>";
            foreach ($attendees as $attendee) {
                $html .= "
                            <div class='attendee-item'>✓ {$attendee}</div>";
            }
            $html .= "
                        </div>
                        <p style='color: #059669; font-size: 14px; margin-top: 10px;'>
                            ✉️ Email invitations have been sent to all attendees.
                        </p>";
        }
        
        $html .= "
                    </div>
                    
                    <p style='color: #6b7280; font-size: 14px; margin-top: 20px;'>
                        ✅ This event has been added to your Google Calendar.
                    </p>
                </div>
                <div class='footer'>
                    <p><strong>FlowSpec AI</strong> - Automated Workflow Management</p>
                    <p>Powered by Gemini AI & Google Workspace</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        return $html;
    }
}
