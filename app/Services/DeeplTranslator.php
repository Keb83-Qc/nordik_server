<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class DeeplTranslator
{
    public function __construct(protected ?string $key = null)
    {
        $this->key = $this->key ?? config('services.deepl.key');
    }

    private function baseUrl(): string
    {
        $isFree = is_string($this->key) && str_ends_with($this->key, ':fx');
        return $isFree ? 'https://api-free.deepl.com' : 'https://api.deepl.com';
    }

    private function authHeaders(): array
    {
        return [
            'Authorization' => 'DeepL-Auth-Key ' . $this->key,
        ];
    }

    public function usage(): array
    {
        $res = Http::withHeaders($this->authHeaders())
            ->get($this->baseUrl() . '/v2/usage');

        if (! $res->successful()) {
            throw new RuntimeException('DeepL usage error: ' . $res->status() . ' ' . $res->body());
        }

        return (array) $res->json();
    }

    /**
     * Texte simple (sans HTML)
     */
    public function translatePlain(string $text, string $source, string $target): string
    {
        $text = trim($text);
        if ($text === '') return '';

        $out = $this->translateBatch([$text], $source, $target, false);

        return (string) ($out[0] ?? '');
    }

    /**
     * HTML ULTRA ROBUSTE:
     * - Préserve 100% des balises (p, h2, table, tr, td, div, etc.)
     * - Traduit uniquement les noeuds texte via DeepL (en batch)
     */
    public function translateHtmlPreserveTags(string $html, string $source, string $target): string
    {
        $html = trim($html);
        if ($html === '') return '';

        // Wrap HTML dans un document complet
        $wrapped = '<!doctype html><html><head><meta charset="utf-8"></head><body>' . $html . '</body></html>';

        $dom = new \DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);

        // Important pour accents / UTF-8
        $dom->loadHTML(mb_convert_encoding($wrapped, 'HTML-ENTITIES', 'UTF-8'));

        libxml_clear_errors();
        libxml_use_internal_errors(false);

        $xpath = new \DOMXPath($dom);

        // Récupère tous les noeuds texte non vides, hors script/style
        $textNodes = $xpath->query('//text()[normalize-space(.) != "" and not(ancestor::script) and not(ancestor::style)]');

        if (! $textNodes || $textNodes->length === 0) {
            return $html;
        }

        // 1) Collecte des textes (en gardant l'ordre)
        $originals = [];
        foreach ($textNodes as $node) {
            /** @var \DOMText $node */
            $val = (string) $node->nodeValue;
            if (trim($val) === '') continue;
            $originals[] = $val;
        }

        if ($originals === []) return $html;

        // 2) Traduction batch (texte simple, PAS html)
        $translations = $this->translateBatch($originals, $source, $target, false);

        // 3) Remplacement des noeuds texte
        $i = 0;
        foreach ($textNodes as $node) {
            $val = (string) $node->nodeValue;
            if (trim($val) === '') continue;

            $node->nodeValue = $translations[$i] ?? $val;
            $i++;
        }

        // 4) Retourne le innerHTML du body
        $body = $dom->getElementsByTagName('body')->item(0);
        if (! $body) return $html;

        $out = '';
        foreach ($body->childNodes as $child) {
            $out .= $dom->saveHTML($child);
        }

        return trim($out);
    }

    /**
     * Batch translator:
     * - DeepL veut text=...&text=... (clé répétée)
     * - $isHtml = true => tag_handling=html (optionnel)
     */
    private function translateBatch(array $texts, string $source, string $target, bool $isHtml): array
    {
        $texts = array_values(array_filter(array_map('trim', $texts), fn($t) => $t !== ''));
        if ($texts === []) return [];

        $chunks = array_chunk($texts, 40);
        $out = [];

        foreach ($chunks as $chunk) {
            $params = [
                'source_lang' => strtoupper($source),
                'target_lang' => strtoupper($target),
                'preserve_formatting' => '1',
                'tag_handling' => 'html',
                'outline_detection' => '0',
                'splitting_tags' => 'p,h1,h2,h3,li,td,th,caption',
                'non_splitting_tags' => 'strong,em,a,span',
            ];

            if ($isHtml) {
                $params['tag_handling'] = 'html';
            }

            $body = $this->buildDeepLFormBody($chunk, $params);

            $res = Http::withHeaders($this->authHeaders())
                ->withBody($body, 'application/x-www-form-urlencoded')
                ->post($this->baseUrl() . '/v2/translate');

            if (! $res->successful()) {
                throw new RuntimeException('DeepL translate error: ' . $res->status() . ' ' . $res->body());
            }

            $translations = (array) data_get($res->json(), 'translations', []);
            foreach ($translations as $t) {
                $out[] = (string) ($t['text'] ?? '');
            }
        }

        // Sécurité longueur
        if (count($out) !== count($texts)) {
            return $texts;
        }

        return $out;
    }

    private function buildDeepLFormBody(array $texts, array $params): string
    {
        $pairs = [];

        foreach ($texts as $t) {
            $pairs[] = 'text=' . rawurlencode($t);
        }

        foreach ($params as $k => $v) {
            $pairs[] = rawurlencode($k) . '=' . rawurlencode((string) $v);
        }

        return implode('&', $pairs);
    }
}
