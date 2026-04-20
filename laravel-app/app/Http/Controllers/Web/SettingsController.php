<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    /**
     * Display the settings page
     */
    public function index()
    {
        return view('settings.index');
    }

    /**
     * Update profile information
     */
    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {
            $user = Auth::user();
            $user->update([
                'name' => $validated['name'],
            ]);

            Log::info('User profile updated', [
                'user_id' => $user->id,
                'name' => $validated['name']
            ]);

            return redirect()->route('settings')
                ->with('success', 'Profile updated successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to update profile', [
                'error' => $e->getMessage()
            ]);

            return redirect()->route('settings')
                ->withErrors(['error' => 'Failed to update profile: ' . $e->getMessage()]);
        }
    }

    /**
     * Update notification settings (WhatsApp)
     */
    public function updateNotifications(Request $request)
    {
        $validated = $request->validate([
            'phone_number' => 'nullable|string|max:20',
            'whatsapp_notifications' => 'nullable|boolean',
        ]);

        try {
            $user = Auth::user();
            
            // Clean phone number
            $phoneNumber = $validated['phone_number'] ?? null;
            if ($phoneNumber) {
                // Remove non-numeric characters
                $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
                
                // Ensure it starts with 62 (Indonesia country code)
                if (!str_starts_with($phoneNumber, '62')) {
                    $phoneNumber = '62' . ltrim($phoneNumber, '0');
                }
            }

            $whatsappEnabled = $request->has('whatsapp_notifications') ? true : false;

            $user->update([
                'phone_number' => $phoneNumber,
                'whatsapp_notifications' => $whatsappEnabled,
            ]);

            Log::info('User notification settings updated', [
                'user_id' => $user->id,
                'phone_number' => $phoneNumber,
                'whatsapp_notifications' => $whatsappEnabled
            ]);

            $message = 'Notification settings updated successfully!';
            
            if ($whatsappEnabled && $phoneNumber) {
                $message .= ' WhatsApp notifications are now enabled for ' . $phoneNumber;
            } elseif ($whatsappEnabled && !$phoneNumber) {
                $message .= ' Please add your phone number to receive WhatsApp notifications.';
            } else {
                $message .= ' WhatsApp notifications are disabled.';
            }

            return redirect()->route('settings')
                ->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Failed to update notification settings', [
                'error' => $e->getMessage()
            ]);

            return redirect()->route('settings')
                ->withErrors(['error' => 'Failed to update settings: ' . $e->getMessage()]);
        }
    }

    /**
     * Disconnect Google account
     */
    public function disconnectGoogle()
    {
        try {
            $user = Auth::user();
            
            $user->update([
                'google_id' => null,
                'google_access_token' => null,
                'google_refresh_token' => null,
                'avatar' => null,
            ]);

            Log::info('User disconnected Google account', [
                'user_id' => $user->id
            ]);

            return redirect()->route('settings')
                ->with('success', 'Google account disconnected successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to disconnect Google account', [
                'error' => $e->getMessage()
            ]);

            return redirect()->route('settings')
                ->withErrors(['error' => 'Failed to disconnect: ' . $e->getMessage()]);
        }
    }
}
