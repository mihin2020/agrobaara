<?php

namespace App\Support;

class MediaVideoUrl
{
    public static function isExternal(string $url): bool
    {
        return str_starts_with($url, 'http://') || str_starts_with($url, 'https://');
    }

    public static function isEmbeddable(string $url): bool
    {
        return (bool) preg_match('/(?:youtube\.com|youtu\.be|vimeo\.com)/i', $url);
    }

    public static function embedUrl(string $url): string
    {
        if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]{11})/', $url, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1];
        }
        if (preg_match('/youtube\.com\/(?:watch\?.*v=|embed\/|shorts\/)([a-zA-Z0-9_-]{11})/', $url, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1];
        }
        if (preg_match('/vimeo\.com\/(\d+)/', $url, $m)) {
            return 'https://player.vimeo.com/video/' . $m[1];
        }

        return $url;
    }

    public static function previewLabel(string $url): string
    {
        if (preg_match('/youtube|youtu\.be/i', $url)) {
            return 'YouTube';
        }
        if (preg_match('/vimeo/i', $url)) {
            return 'Vimeo';
        }
        if (str_ends_with(strtolower($url), '.mp4')) {
            return 'MP4';
        }

        return 'Lien';
    }

    public static function youtubeId(string $url): ?string
    {
        if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]{11})/', $url, $m)) {
            return $m[1];
        }
        if (preg_match('/youtube\.com\/(?:watch\?.*v=|embed\/|shorts\/)([a-zA-Z0-9_-]{11})/', $url, $m)) {
            return $m[1];
        }

        return null;
    }

    public static function thumbnailUrl(string $url): ?string
    {
        if ($id = self::youtubeId($url)) {
            return "https://img.youtube.com/vi/{$id}/hqdefault.jpg";
        }

        return null;
    }
}
