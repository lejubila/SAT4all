<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tools\MarkdownViewerRequest;
use App\Tools\MarkdownViewer\MarkdownViewer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;

class MarkdownViewerController extends Controller
{
    public function index(): View
    {
        return view('tools.markdown-viewer');
    }

    public function preview(MarkdownViewerRequest $request): View
    {
        $html = (new MarkdownViewer())->render((string) ($request->input('markdown') ?? ''));

        return view('tools.partials.markdown-viewer-preview', compact('html'));
    }

    public function exportHtml(MarkdownViewerRequest $request): Response
    {
        $full = (new MarkdownViewer())->toFullHtml(
            (string) ($request->input('markdown') ?? ''),
            'Document'
        );

        return response($full, 200, [
            'Content-Type'        => 'text/html; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="document.html"',
        ]);
    }

    public function exportPdf(MarkdownViewerRequest $request): Response
    {
        $full = (new MarkdownViewer())->toFullHtml(
            (string) ($request->input('markdown') ?? ''),
            'Document'
        );

        return Pdf::loadHtml($full)->download('document.pdf');
    }
}
