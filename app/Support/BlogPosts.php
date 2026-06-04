<?php

namespace App\Support;

use Carbon\Carbon;

class BlogPosts
{
    public static function all(): array
    {
        return collect(config('brilliant.blog_posts', []))
            ->map(fn (array $post) => self::format($post))
            ->sortByDesc('date_sort')
            ->values()
            ->all();
    }

    public static function find(string $slug): ?array
    {
        foreach (self::all() as $post) {
            if ($post['slug'] === $slug) {
                return $post;
            }
        }

        return null;
    }

    public static function filterByCategory(?string $category): array
    {
        $posts = self::all();
        if (! $category || $category === 'semua') {
            return $posts;
        }

        return array_values(array_filter($posts, fn ($p) => $p['category'] === $category));
    }

    protected static function format(array $post): array
    {
        $date = Carbon::parse($post['date']);

        return array_merge($post, [
            'date_formatted' => $date->translatedFormat('d M Y'),
            'date_sort' => $date->timestamp,
            'read_time' => ($post['read_minutes'] ?? 5).' menit baca',
            'category_label' => config('brilliant.blog_categories')[$post['category']] ?? ucfirst($post['category']),
        ]);
    }
}
