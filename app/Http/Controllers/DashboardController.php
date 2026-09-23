<?php

namespace App\Http\Controllers;

use App\Models\BucketFile;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard with dynamic user metrics
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        $files = BucketFile::where('user_id', $userId)
            ->latest()
            ->get();

        $folders = Folder::where('user_id', $userId)
            ->withCount('files')
            ->get();

        $totalBytes = $files->sum('size');
        $totalFiles = $files->count();
        $totalFolders = $folders->count();
        $recentFiles = $files->take(6);

        // Category breakdown
        $categories = [
            'image' => ['bytes' => 0, 'count' => 0, 'percentage' => 0],
            'video' => ['bytes' => 0, 'count' => 0, 'percentage' => 0],
            'audio' => ['bytes' => 0, 'count' => 0, 'percentage' => 0],
            'document' => ['bytes' => 0, 'count' => 0, 'percentage' => 0],
            'archive' => ['bytes' => 0, 'count' => 0, 'percentage' => 0],
            'other' => ['bytes' => 0, 'count' => 0, 'percentage' => 0],
        ];

        foreach ($files as $file) {
            $cat = $file->category ?? 'other';
            if (!isset($categories[$cat])) {
                $categories[$cat] = ['bytes' => 0, 'count' => 0, 'percentage' => 0];
            }
            $categories[$cat]['bytes'] += $file->size;
            $categories[$cat]['count'] += 1;
        }

        // Calculate relative percentages for visual progress bar if bytes > 0
        if ($totalBytes > 0) {
            foreach ($categories as $cat => $data) {
                $categories[$cat]['percentage'] = round(($data['bytes'] / $totalBytes) * 100, 1);
            }
        }

        $totalOriginalBytes = 0;
        foreach ($files as $file) {
            $totalOriginalBytes += ($file->original_size && $file->original_size > $file->size) ? $file->original_size : $file->size;
        }
        $totalSavedBytes = max(0, $totalOriginalBytes - $totalBytes);
        $savingsPercent = $totalOriginalBytes > 0 ? (int) round(($totalSavedBytes / $totalOriginalBytes) * 100) : 0;

        return view('dashboard', [
            'totalFiles' => $totalFiles,
            'totalFolders' => $totalFolders,
            'totalBytes' => $totalBytes,
            'formattedTotalBytes' => $this->formatBytes($totalBytes),
            'totalSavedBytes' => $totalSavedBytes,
            'formattedSavedBytes' => $this->formatBytes($totalSavedBytes),
            'savingsPercent' => $savingsPercent,
            'categories' => $categories,
            'recentFiles' => $recentFiles,
            'starredCount' => $files->where('is_starred', true)->count(),
        ]);
    }

    /**
     * Helper to format bytes
     */
    protected function formatBytes(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 1) . ' GB';
        }
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 1) . ' KB';
        }
        return $bytes . ' B';
    }
}
