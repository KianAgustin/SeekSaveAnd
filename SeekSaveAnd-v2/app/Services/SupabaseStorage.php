<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Uses Supabase Storage when configured, otherwise Laravel's public disk for local development.
 */
class SupabaseStorage
{
    public static function url(string $path): string
    {
        $base = rtrim((string) config('services.supabase.url'), '/');
        $bucket = config('services.supabase.bucket');

        if ($base !== '' && (string) config('services.supabase.key') !== '') {
            return "{$base}/storage/v1/object/public/{$bucket}/{$path}";
        }

        return Storage::disk('public')->url($path);
    }

    public static function upload(UploadedFile $file, string $folder = 'items'): string
    {
        $base = rtrim((string) config('services.supabase.url'), '/');
        $key = (string) config('services.supabase.key');
        $bucket = config('services.supabase.bucket');

        $ext = $file->guessExtension() ?: 'jpg';
        $path = $folder . '/' . Str::uuid() . '.' . $ext;

        if ($base === '' || $key === '') {
            Storage::disk('public')->putFileAs($folder, $file, basename($path));
            return $path;
        }

        $response = Http::withHeaders(self::headers($key))
            ->withBody(file_get_contents($file->getRealPath()), $file->getMimeType() ?: 'application/octet-stream')
            ->post("{$base}/storage/v1/object/{$bucket}/{$path}");

        if (! $response->successful()) {
            throw new \RuntimeException('Supabase upload failed: ' . $response->status());
        }

        return $path;
    }

    public static function delete(?string $path): void
    {
        if (! $path || str_starts_with($path, 'samples/')) {
            return;
        }

        $base = rtrim((string) config('services.supabase.url'), '/');
        $key = (string) config('services.supabase.key');
        $bucket = config('services.supabase.bucket');

        if ($base === '' || $key === '') {
            Storage::disk('public')->delete($path);
            return;
        }

        Http::withHeaders(self::headers($key))
            ->delete("{$base}/storage/v1/object/{$bucket}/{$path}");
    }

    private static function headers(string $key): array
    {
        $headers = ['apikey' => $key];

        // Legacy service_role keys are JWTs and also go in Authorization; new sb_secret_ keys use apikey only.
        if (! str_starts_with($key, 'sb_')) {
            $headers['Authorization'] = 'Bearer ' . $key;
        }

        return $headers;
    }
}
