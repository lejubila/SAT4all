<?php

namespace Tests\Feature\Tools;

use Tests\TestCase;

class MarkdownViewerTest extends TestCase
{
    // ── Page ────────────────────────────────────────────────────────────────

    public function test_shows_page(): void
    {
        $this->get(route('tools.markdown-viewer.index'))
            ->assertOk()
            ->assertSee(__('tools.markdown_viewer.title'));
    }

    // ── Preview ─────────────────────────────────────────────────────────────

    public function test_preview_renders_heading(): void
    {
        $this->post(route('tools.markdown-viewer.preview'), [
            'markdown' => '# Hello World',
        ])->assertOk()
          ->assertSee('<h1', false)
          ->assertSee('Hello World');
    }

    public function test_preview_renders_bold(): void
    {
        $this->post(route('tools.markdown-viewer.preview'), [
            'markdown' => '**bold text**',
        ])->assertOk()
          ->assertSee('<strong>', false);
    }

    public function test_preview_empty_input_returns_placeholder(): void
    {
        $this->post(route('tools.markdown-viewer.preview'), [
            'markdown' => '',
        ])->assertOk()
          ->assertSee(__('tools.markdown_viewer.placeholder_preview'));
    }

    public function test_preview_passes_through_html_anchor(): void
    {
        // html_input=allow: <a id="..."> anchors must survive for internal links to work
        $this->post(route('tools.markdown-viewer.preview'), [
            'markdown' => '<a id="section-1"></a>' . "\n" . '## Section',
        ])->assertOk()
          ->assertSee('id="section-1"', false);
    }

    public function test_preview_blocks_javascript_links(): void
    {
        // allow_unsafe_links=false still blocks javascript: URIs in links
        $this->post(route('tools.markdown-viewer.preview'), [
            'markdown' => '[click](javascript:alert(1))',
        ])->assertOk()
          ->assertDontSee('javascript:', false);
    }

    public function test_preview_too_large_input_returns_error(): void
    {
        $this->post(route('tools.markdown-viewer.preview'), [
            'markdown' => str_repeat('a', 100001),
        ])->assertOk()
          ->assertSee(__('tools.markdown_viewer.error_too_large'));
    }

    // ── Export HTML ─────────────────────────────────────────────────────────

    public function test_export_html_returns_html_file(): void
    {
        $response = $this->post(route('tools.markdown-viewer.export-html'), [
            'markdown' => '# Test',
        ]);

        $response->assertOk();
        $this->assertStringContainsString('text/html', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('attachment', $response->headers->get('Content-Disposition'));
        $this->assertStringContainsString('document.html', $response->headers->get('Content-Disposition'));
    }

    public function test_export_html_contains_rendered_content(): void
    {
        $response = $this->post(route('tools.markdown-viewer.export-html'), [
            'markdown' => '# My Title',
        ]);

        $response->assertOk();
        $this->assertStringContainsString('<h1', $response->getContent());
        $this->assertStringContainsString('My Title', $response->getContent());
    }

    public function test_export_html_contains_full_html_structure(): void
    {
        $response = $this->post(route('tools.markdown-viewer.export-html'), [
            'markdown' => 'Hello',
        ]);

        $response->assertOk();
        $content = $response->getContent();
        $this->assertStringContainsString('<!DOCTYPE html>', $content);
        $this->assertStringContainsString('<html', $content);
        $this->assertStringContainsString('</html>', $content);
    }

    // ── Export PDF ──────────────────────────────────────────────────────────

    public function test_export_pdf_returns_pdf_file(): void
    {
        $response = $this->post(route('tools.markdown-viewer.export-pdf'), [
            'markdown' => '# PDF Test',
        ]);

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('attachment', $response->headers->get('Content-Disposition'));
        $this->assertStringContainsString('document.pdf', $response->headers->get('Content-Disposition'));
    }

    public function test_export_pdf_has_pdf_signature(): void
    {
        $response = $this->post(route('tools.markdown-viewer.export-pdf'), [
            'markdown' => 'Hello PDF',
        ]);

        $response->assertOk();
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }
}
