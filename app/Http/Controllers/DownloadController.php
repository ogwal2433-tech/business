<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Cache;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use App\Models\DownloadLog;
use Illuminate\Support\Facades\Log;

class DownloadController extends Controller
{
    public function downloadApp(Request $request)
    {
        $platform = $request->query('platform', 'android');
        $userAgent = $request->header('User-Agent');

        $this->logDownload($platform, $userAgent, $request->ip());

        $files = [
            'android' => [
                'path' => 'apps/smartbiz-android-v2.1.0.apk',
                'name' => 'SmartBiz-Android-v2.1.0.apk',
                'size' => '45.2 MB'
            ],
            'ios' => [
                'path' => 'apps/smartbiz-ios-v2.1.0.ipa',
                'name' => 'SmartBiz-iOS-v2.1.0.ipa',
                'size' => '52.8 MB'
            ],
            'windows' => [
                'path' => 'apps/SmartBiz-Windows-Setup.exe',
                'name' => 'SmartBiz-Windows-Setup.exe',
                'size' => '80.1 MB',
                'github_url' => 'https://github.com/ogwal2433-tech/business/releases/download/v1.0.0/SmartBiz-Setup-1.0.0.exe'
            ],
            'mac' => [
                'path' => 'apps/smartbiz-mac-v2.1.0.dmg',
                'name' => 'SmartBiz-Mac-Installer.dmg',
                'size' => '58.1 MB'
            ]
        ];

        if (!isset($files[$platform])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid platform specified'
            ], 400);
        }

        $file = $files[$platform];

        // Redirect to GitHub Releases for Windows (avoids SmartScreen warning)
        if ($platform === 'windows' && isset($file['github_url'])) {
            return redirect()->away($file['github_url']);
        }

        if (!Storage::disk('public')->exists($file['path'])) {
            Log::error('Download file not found: ' . $file['path']);

            if ($platform === 'android') {
                return redirect()->away('https://play.google.com/store/apps/details?id=com.smartbiz.app');
            } elseif ($platform === 'ios') {
                return redirect()->away('https://apps.apple.com/app/smartbiz/id123456789');
            }

            return response()->json([
                'success' => false,
                'message' => 'Download file temporarily unavailable. Please try again later.'
            ], 404);
        }

        $this->incrementDownloadCount($platform);

        return Storage::disk('public')->download($file['path'], $file['name'], [
            'Content-Type' => 'application/octet-stream',
        ]);
    }

    public function getVersionInfo(Request $request)
    {
        $platform = $request->query('platform', 'android');

        $versions = [
            'android' => [
                'version' => '2.1.0',
                'build' => '210',
                'release_date' => '2025-02-15',
                'size' => '45.2 MB',
                'min_os' => 'Android 8.0+',
                'features' => [
                    'Real-time inventory sync',
                    'Offline mode support',
                    'Barcode scanning',
                    'Team chat'
                ]
            ],
            'ios' => [
                'version' => '2.1.0',
                'build' => '210',
                'release_date' => '2025-02-15',
                'size' => '52.8 MB',
                'min_os' => 'iOS 14.0+',
                'features' => [
                    'Real-time inventory sync',
                    'Offline mode support',
                    'Barcode scanning',
                    'Team chat'
                ]
            ],
            'windows' => [
                'version' => '2.1.0',
                'build' => '210',
                'release_date' => '2025-06-23',
                'size' => '80.1 MB',
                'min_os' => 'Windows 10+',
                'features' => [
                    'Desktop POS system',
                    'Inventory management',
                    'Advanced reporting',
                    'Multi-store support',
                    'Desktop & Start Menu shortcuts'
                ]
            ]
        ];

        return response()->json([
            'success' => true,
            'platform' => $platform,
            'data' => $versions[$platform] ?? $versions['android']
        ]);
    }

    public function generateSecureLink(Request $request)
    {
        $request->validate([
            'platform' => 'required|in:android,ios,windows,mac'
        ]);

        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required'
            ], 401);
        }

        $expires = now()->addHour();
        $platform = $request->platform;

        $url = url("/api/download/secure/{$platform}?expires={$expires->timestamp}&user={$user->id}");
        $signature = hash_hmac('sha256', $url, config('app.key'));

        $secureUrl = $url . '&signature=' . $signature;

        return response()->json([
            'success' => true,
            'download_url' => $secureUrl,
            'expires_at' => $expires->toDateTimeString()
        ]);
    }

    public function secureDownload(Request $request, $platform)
    {
        $expires = $request->query('expires');
        $userId = $request->query('user');
        $signature = $request->query('signature');

        if (now()->timestamp > $expires) {
            return response()->json([
                'success' => false,
                'message' => 'Download link has expired'
            ], 410);
        }

        $url = url("/api/download/secure/{$platform}?expires={$expires}&user={$userId}");
        $expectedSignature = hash_hmac('sha256', $url, config('app.key'));

        if (!hash_equals($expectedSignature, $signature)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid download link'
            ], 403);
        }

        DownloadLog::create([
            'user_id' => $userId,
            'platform' => $platform,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'type' => 'secure'
        ]);

        return redirect()->route('app.download', ['platform' => $platform]);
    }

    private function logDownload($platform, $userAgent, $ip)
    {
        try {
            DownloadLog::create([
                'platform' => $platform,
                'user_agent' => $userAgent,
                'ip_address' => $ip,
                'type' => 'public'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log download: ' . $e->getMessage());
        }
    }

    private function incrementDownloadCount($platform)
    {
        Cache::increment("download_count_{$platform}");
    }
}
