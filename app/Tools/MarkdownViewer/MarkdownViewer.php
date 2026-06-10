<?php

namespace App\Tools\MarkdownViewer;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\MarkdownConverter;

class MarkdownViewer
{
    private MarkdownConverter $converter;

    public function __construct()
    {
        $environment = new Environment([
            'html_input'         => 'allow',  // User pastes own content; <a id="..."> anchors must survive
            'allow_unsafe_links' => false,
            'heading_permalink'  => [
                'apply_id_to_heading' => true,
                'insert'              => 'none',
                'id_prefix'           => '',
                'fragment_prefix'     => '',
            ],
        ]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new GithubFlavoredMarkdownExtension());
        $environment->addExtension(new HeadingPermalinkExtension());

        $this->converter = new MarkdownConverter($environment);
    }

    public function render(string $markdown): string
    {
        if (blank($markdown)) {
            return '';
        }

        return (string) $this->converter->convert($markdown);
    }

    public function toFullHtml(string $markdown, string $title = 'Document'): string
    {
        $body = $this->mergeAnchorsIntoHeadings($this->render($markdown));

        $css = <<<'CSS'
            body {
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif;
                font-size: 16px;
                line-height: 1.6;
                color: #24292e;
                max-width: 800px;
                margin: 40px auto;
                padding: 0 20px;
            }
            h1, h2, h3, h4, h5, h6 {
                margin-top: 24px;
                margin-bottom: 16px;
                font-weight: 600;
                line-height: 1.25;
            }
            h1 { font-size: 2em; border-bottom: 1px solid #eaecef; padding-bottom: 0.3em; }
            h2 { font-size: 1.5em; border-bottom: 1px solid #eaecef; padding-bottom: 0.3em; }
            h3 { font-size: 1.25em; }
            p { margin-top: 0; margin-bottom: 16px; }
            a { color: #0366d6; text-decoration: none; }
            a:hover { text-decoration: underline; }
            code {
                font-family: "SFMono-Regular", Consolas, "Liberation Mono", Menlo, monospace;
                font-size: 85%;
                background-color: rgba(27,31,35,0.05);
                border-radius: 3px;
                padding: 0.2em 0.4em;
            }
            pre {
                background-color: #f6f8fa;
                border-radius: 6px;
                font-size: 85%;
                line-height: 1.45;
                overflow: auto;
                padding: 16px;
                margin-bottom: 16px;
            }
            pre code {
                background-color: transparent;
                padding: 0;
                font-size: 100%;
            }
            blockquote {
                border-left: 4px solid #dfe2e5;
                color: #6a737d;
                margin: 0 0 16px 0;
                padding: 0 16px;
            }
            table {
                border-collapse: collapse;
                margin-bottom: 16px;
                width: 100%;
            }
            th, td {
                border: 1px solid #dfe2e5;
                padding: 6px 13px;
            }
            th { background-color: #f6f8fa; font-weight: 600; }
            tr:nth-child(even) { background-color: #f6f8fa; }
            ul, ol { margin-bottom: 16px; padding-left: 2em; }
            li { margin-bottom: 4px; }
            hr { border: 0; border-top: 1px solid #eaecef; margin: 24px 0; }
            img { max-width: 100%; }
        CSS;

        $escapedTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

        return <<<HTML
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>{$escapedTitle}</title>
                <style>{$css}</style>
            </head>
            <body>
            {$body}
            </body>
            </html>
            HTML;
    }

    /**
     * Move the id from empty <a id="xyz"> anchors onto the heading that follows.
     * Browsers handle standalone <a id> fine, but dompdf requires the id to be
     * on a block element to resolve internal links correctly.
     */
    private function mergeAnchorsIntoHeadings(string $html): string
    {
        // CommonMark wraps standalone <a id> in <p>, so match both:
        //   <p><a id="xyz"></a></p>\n<hN ...>
        //   <a id="xyz"></a>\n<hN ...>
        return preg_replace_callback(
            '~(?:<p>\s*)?<a\s+(?:id|name)=["\']([^"\']+)["\']\s*(?:></a>|/>)(?:\s*</p>)?\s*\n?(<h[1-6])([^>]*)>~i',
            static function (array $m): string {
                $attrs = preg_replace('/\s+id="[^"]*"/i', '', $m[3]);
                return $m[2] . ' id="' . htmlspecialchars($m[1], ENT_QUOTES) . '"' . $attrs . '>';
            },
            $html
        ) ?? $html;
    }
}
