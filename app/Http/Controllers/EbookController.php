<?php

namespace App\Http\Controllers;

use App\Models\Ebook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;

class EbookController extends Controller
{
    public function index()
    {
        $ebooks = Ebook::latest()->get();
        return view('ebooks.index', compact('ebooks'));
    }

    public function create()
    {
        return view('ebooks.upload');
    }

    public function store(Request $request)
    {
        $request->validate([
            'ebook' => 'required|file|max:51200', // 50MB max
        ]);

        $file = $request->file('ebook');
        $fileType = strtolower($file->getClientOriginalExtension());

        if ($file->getSize() === 0) {
            return back()->withErrors(['ebook' => 'Die hochgeladene Datei ist komplett leer (0 Bytes). Bitte fülle sie mit Text.'])->withInput();
        }

        if (!in_array($fileType, ['txt', 'pdf'])) {
            return back()->withErrors(['ebook' => 'The ebook field must be a file of type: txt, pdf.'])->withInput();
        }

        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('ebooks', $fileName, 'public');

        $absolutePath = Storage::disk('public')->path($filePath);
        $title = $this->extractTitle($absolutePath, $fileType);

        // Parse Chapters
        $chaptersCount = $this->countChapters($absolutePath, $fileType);

        Ebook::create([
            'title' => $title,
            'filename' => $fileName,
            'file_type' => $fileType,
            'chapters_count' => $chaptersCount,
        ]);

        return redirect()->route('ebooks.index')->with('success', 'Ebook erfolgreich hochgeladen!');
    }

    public function stream(Ebook $ebook)
    {
        $path = storage_path('app/public/ebooks/' . $ebook->filename);
        if (file_exists($path)) {
            return response()->file($path);
        }
        return redirect()->back()->with('error', 'Datei nicht gefunden.');
    }

    public function read(Ebook $ebook)
    {
        $path = storage_path('app/public/ebooks/' . $ebook->filename);
        if (!file_exists($path)) {
            return redirect()->back()->with('error', 'Datei nicht gefunden.');
        }

        $textContent = null;
        $currentPage = 1;
        $totalPages = 1;

        if (strtolower($ebook->file_type) === 'txt') {
            $fullTextContent = file_get_contents($path);

            // Convert to UTF-8 to prevent view rendering issues with e()
            if (!mb_check_encoding($fullTextContent, 'UTF-8')) {
                $fullTextContent = mb_convert_encoding($fullTextContent, 'UTF-8', 'ISO-8859-1, Windows-1252');
            }

            // Pagination Logic for TXT
            $charsPerPage = 4000; // About ~600 words per page
            $textLength = mb_strlen($fullTextContent);
            $totalPages = max(1, ceil($textLength / $charsPerPage));

            $currentPage = request()->query('page', 1);
            if (!is_numeric($currentPage) || $currentPage < 1) {
                $currentPage = 1;
            }
            if ($currentPage > $totalPages) {
                $currentPage = $totalPages;
            }

            $offset = ($currentPage - 1) * $charsPerPage;
            $textContent = mb_substr($fullTextContent, $offset, $charsPerPage);

            // Highlight chapters visually
            $pattern = '/\b(Kapitel|Chapter)\s+([0-9IVXLCDM]+)\b/i';
            // Escapes everything except our newly added HTML tags by escaping the user content first
            $textContent = e($textContent);
            $textContent = preg_replace($pattern, '<h2 style="color: var(--primary); margin-top: 2rem; margin-bottom: 1rem; border-bottom: 1px solid var(--glass-border); padding-bottom: 0.5rem;">$0</h2>', $textContent);
        }

        // We already escaped the textContent manually to safely inject HTML chapter tags
        return view('ebooks.read', compact('ebook', 'textContent', 'currentPage', 'totalPages'));
    }

    private function countChapters($filePath, $fileType)
    {
        $text = '';
        if ($fileType === 'pdf') {
            try {
                $parser = new Parser();
                $pdf = $parser->parseFile($filePath);
                $text = $pdf->getText();
            }
            catch (\Exception $e) {
                // If parsing fails, fall back to 0
                return 0;
            }
        }
        elseif ($fileType === 'txt') {
            $text = file_get_contents($filePath);
        }

        // Match "Chapter X" or "Kapitel X"
        // Also matches roman numerals
        $pattern = '/\b(?:Kapitel|Chapter)\s+(?:\d+|[IVXLCDM]+)\b/i';
        if (preg_match_all($pattern, $text, $matches)) {
            // Some books have "Chapter 1" repeated in header on every page, which is tricky.
            // A simple count of unique chapters is better.
            $uniqueChapters = array_unique(array_map('strtolower', $matches[0]));
            return count($uniqueChapters);
        }

        return 0;
    }

    private function extractTitle($filePath, $fileType)
    {
        if ($fileType === 'pdf') {
            try {
                $parser = new Parser();
                $pdf = $parser->parseFile($filePath);
                $details = $pdf->getDetails();

                // Use PDF metadata title if available
                if (isset($details['Title']) && trim($details['Title']) !== '') {
                    // Sometimes metadata titles are something like "Microsoft Word - Document1". We just return it.
                    return mb_substr(trim($details['Title']), 0, 50);
                }

                // Fallback to first line of text
                $text = $pdf->getText();
                $lines = explode("\n", $text);
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (!empty($line)) {
                        return mb_substr($line, 0, 50);
                    }
                }
            }
            catch (\Exception $e) {
                return 'Unbekanntes PDF Dokument';
            }
        }
        elseif ($fileType === 'txt') {
            $content = file_get_contents($filePath);
            if (!mb_check_encoding($content, 'UTF-8')) {
                $content = mb_convert_encoding($content, 'UTF-8', 'ISO-8859-1, Windows-1252');
            }
            $lines = explode("\n", $content);
            foreach ($lines as $line) {
                $line = trim(preg_replace('/\s+/', ' ', $line));
                if (empty($line))
                    continue;
                // If it starts with Chapter/Kapitel, skip it (likely not the title)
                if (preg_match('/^(Kapitel|Chapter)\s/i', $line))
                    continue;

                return mb_substr($line, 0, 60);
            }
        }

        return 'Unbekanntes Buch';
    }
}
