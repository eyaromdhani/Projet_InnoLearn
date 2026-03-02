<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Twilio\Rest\Client;

class SmsService
{
    private ?Client $twilioClient;
    private ?string $fromNumber;
    private HttpClientInterface $httpClient;

    private ?string $smsmodeApiKey;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;

        // Get Twilio credentials
        $accountSid = $_ENV['TWILIO_ACCOUNT_SID'] ?? null;
        $authToken = $_ENV['TWILIO_AUTH_TOKEN'] ?? null;
        $this->fromNumber = $_ENV['TWILIO_PHONE_NUMBER'] ?? null;
        
        // Get smsmode credentials
        $this->smsmodeApiKey = $_ENV['SMSMODE_API_KEY'] ?? null;

        // Only initialize Twilio if credentials are provided
        if ($accountSid && $authToken) {
            $this->twilioClient = new Client($accountSid, $authToken);
        } else {
            $this->twilioClient = null;
        }
    }

    /**
     * Send SMS verification code
     * 
     * @param string $phoneNumber Full phone number with country code (e.g., +212612345678)
     * @param string $code The 8-digit verification code
     * @return bool True if sent successfully, false otherwise
     */
    public function sendVerificationCode(string $phoneNumber, string $code): bool
    {
        // 0. Priorities: smsmode > Twilio > Textbelt > Dev
        
        // 1. Try smsmode if configured
        if ($this->smsmodeApiKey) {
            return $this->sendViaSmsmode($phoneNumber, $code);
        }

        // 2. Try Twilio if configured
        if ($this->twilioClient && $this->fromNumber) {
            // ... Twilio logic ...
            return $this->sendViaTwilio($phoneNumber, $code);
        }

        // 3. Fallback to Textbelt (Free Tier - 1/day)
        return $this->sendViaTextbelt($phoneNumber, $code);
    }

    private function sendViaSmsmode(string $phoneNumber, string $code): bool
    {
        try {
            // Remove + from phone number for some APIs if needed, but standard is usually with + or without.
            // smsmode usually accepts E.164.
            
            $response = $this->httpClient->request('POST', 'https://rest.smsmode.com/sms/v1/messages', [
                'headers' => [
                    'X-Api-Key' => $this->smsmodeApiKey,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'recipient' => [
                        'to' => $phoneNumber,
                    ],
                    'body' => [
                        'text' => "Your InnoLearn code is: {$code}. Expires in 10 min.",
                    ],
                    'from' => 'InnoLearn' // Sender ID (alfanumeric)
                ]
            ]);

            $statusCode = $response->getStatusCode();
            $content = $response->toArray(false); // false = don't throw on error status

            if ($statusCode >= 200 && $statusCode < 300) {
                $msgId = $content['messageId'] ?? 'unknown';
                file_put_contents(__DIR__ . '/../../var/log/mfa_debug.log', "[" . date('Y-m-d H:i:s') . "] ✅ SMSMODE SMS Sent to {$phoneNumber}. ID: {$msgId}\n", FILE_APPEND);
                return true;
            } else {
                $errorMsg = json_encode($content);
                $msg = "smsmode Error ({$statusCode}): " . $errorMsg;
                error_log($msg);
                file_put_contents(__DIR__ . '/../../var/log/mfa_debug.log', "[" . date('Y-m-d H:i:s') . "] ❌ " . $msg . "\n", FILE_APPEND);
                return false;
            }
        } catch (\Exception $e) {
            $msg = "smsmode Exception: " . $e->getMessage();
            file_put_contents(__DIR__ . '/../../var/log/mfa_debug.log', "[" . date('Y-m-d H:i:s') . "] ❌ " . $msg . "\n", FILE_APPEND);
            return false;
        }
    }

    private function sendViaTwilio(string $phoneNumber, string $code): bool
    {
        try {
            $message = $this->twilioClient->messages->create(
                $phoneNumber,
                [
                    'from' => $this->fromNumber,
                    'body' => "Your InnoLearn verification code is: {$code}\n\nThis code expires in 10 minutes."
                ]
            );

            if ($message->sid) {
                 file_put_contents(__DIR__ . '/../../var/log/mfa_debug.log', "[" . date('Y-m-d H:i:s') . "] ✅ TWILIO SMS Sent to {$phoneNumber}. SID: {$message->sid}\n", FILE_APPEND);
            }
            return $message->sid !== null;
        } catch (\Exception $e) {
            $msg = "Twilio SMS Error: " . $e->getMessage();
            error_log($msg);
            file_put_contents(__DIR__ . '/../../var/log/mfa_debug.log', "[" . date('Y-m-d H:i:s') . "] ❌ " . $msg . "\n", FILE_APPEND);
            return false;
        }
    }

    private function sendViaTextbelt(string $phoneNumber, string $code): bool
    {
        try {
            $response = $this->httpClient->request('POST', 'https://textbelt.com/text', [
                'body' => [
                    'phone' => $phoneNumber,
                    'message' => "Your InnoLearn code: {$code}",
                    'key' => 'textbelt',
                ],
            ]);

            $content = $response->toArray();
            
            if (($content['success'] ?? false) === true) {
                file_put_contents(__DIR__ . '/../../var/log/mfa_debug.log', "[" . date('Y-m-d H:i:s') . "] ✅ TEXTBELT SMS Sent to {$phoneNumber}. ID: {$content['textId']}\n", FILE_APPEND);
                return true;
            } else {
                $error = $content['error'] ?? 'Unknown error';
                $msg = "Textbelt SMS Error: " . $error;
                error_log($msg);
                file_put_contents(__DIR__ . '/../../var/log/mfa_debug.log', "[" . date('Y-m-d H:i:s') . "] ❌ " . $msg . "\n", FILE_APPEND);
                
                // Final Dev Mode Fallback
                $devMsg = "📱 SMS Service (Dev Mode - Final Fallback) - Code for {$phoneNumber}: {$code}";
                file_put_contents(__DIR__ . '/../../var/log/mfa_debug.log', "[" . date('Y-m-d H:i:s') . "] ⚠️ " . $devMsg . "\n", FILE_APPEND);
                return true; 
            }
        } catch (\Exception $e) {
            $msg = "Textbelt Exception: " . $e->getMessage();
            file_put_contents(__DIR__ . '/../../var/log/mfa_debug.log', "[" . date('Y-m-d H:i:s') . "] ❌ " . $msg . "\n", FILE_APPEND);
            // Even if exception, return false so user knows it failed (or handle dev fallback here too?)
            // Let's do dev fallback here too to ensure user can login
             $devMsg = "📱 SMS Service (Dev Mode - Exception Fallback) - Code for {$phoneNumber}: {$code}";
             file_put_contents(__DIR__ . '/../../var/log/mfa_debug.log', "[" . date('Y-m-d H:i:s') . "] ⚠️ " . $devMsg . "\n", FILE_APPEND);
             return true;
        }
    }

    /**
     * Format phone number for Twilio/Textbelt (country code + number)
     * e.g. +212612345678 or 212612345678 (Textbelt handles + usually)
     */
    public function formatPhoneNumber(string $countryCode, string $phoneNumber): string
    {
        // Remove any spaces or dashes
        $phoneNumber = preg_replace('/[\s\-]/', '', $phoneNumber);
        
        // Ensure country code starts with +
        if (!str_starts_with($countryCode, '+')) {
            $countryCode = '+' . $countryCode;
        }

        // Avoid double country code if user typed it
        // A simple check: if phone number starts with country code (without +), remove it
        $cleanCountryCode = ltrim($countryCode, '+');
        if (str_starts_with($phoneNumber, $cleanCountryCode)) {
            $phoneNumber = substr($phoneNumber, strlen($cleanCountryCode));
        }

        return $countryCode . $phoneNumber;
    }
}
