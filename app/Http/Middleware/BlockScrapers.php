<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

/**
 * Anti-scraping : bloque les bots connus + quotas IP (plus stricts pour outils CLI).
 */
class BlockScrapers
{
    /** Bots / scrapers bloqués net. */
    private const HARD_BLOCK_UA = [
        'scrapy', 'httpclient', 'python-requests', 'python-urllib',
        'libwww-perl', 'go-http-client', 'java/',
        'phantomjs', 'headlesschrome', 'selenium', 'puppeteer',
        'bytespider', 'petalbot', 'ahrefsbot', 'semrushbot',
        'mj12bot', 'dotbot', 'megaindex', 'dataforseo',
        'gptbot', 'claudebot', 'ccbot', 'anthropic-ai',
        'chatgpt-user', 'amazonbot', 'meta-externalagent',
    ];

    /** Outils CLI : autorisés mais fortement limités. */
    private const SOFT_THROTTLE_UA = [
        'wget', 'curl/', 'httpie', 'postman', 'insomnia',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('up')) {
            return $next($request);
        }

        if ($request->user()?->isStaffUser() || $request->user()?->is_system) {
            return $next($request);
        }

        $ua = strtolower((string) $request->userAgent());
        $ip = $request->ip() ?? 'unknown';

        if ($ua === '' || $this->matchesAny($ua, self::HARD_BLOCK_UA)) {
            abort(403, 'Accès refusé.');
        }

        $isCliTool = $this->matchesAny($ua, self::SOFT_THROTTLE_UA);
        $globalLimit = $isCliTool ? 20 : 90;
        $dataLimit = $isCliTool ? 10 : 25;

        $globalKey = 'scrape:global:'.$ip;
        if (RateLimiter::tooManyAttempts($globalKey, $globalLimit)) {
            return response('Trop de requêtes. Réessayez plus tard.', 429)
                ->header('Retry-After', (string) RateLimiter::availableIn($globalKey));
        }
        RateLimiter::hit($globalKey, 60);

        if ($this->isDataHeavyPath($request)) {
            $dataKey = 'scrape:data:'.$ip;
            if (RateLimiter::tooManyAttempts($dataKey, $dataLimit)) {
                return response('Trop de requêtes sur les listes publiques.', 429)
                    ->header('Retry-After', (string) RateLimiter::availableIn($dataKey));
            }
            RateLimiter::hit($dataKey, 60);
        }

        return $next($request);
    }

    /** @param  list<string>  $needles */
    private function matchesAny(string $ua, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (str_contains($ua, $needle)) {
                return true;
            }
        }

        return false;
    }

    private function isDataHeavyPath(Request $request): bool
    {
        return $request->is('offres')
            || $request->is('offres/*')
            || $request->is('actualites')
            || $request->is('actualites/*')
            || $request->is('bibliotheque')
            || $request->is('mediatheque')
            || $request->is('inscription')
            || $request->is('inscription/*')
            || $request->is('connexion*')
            || $request->is('fichiers/*');
    }
}
