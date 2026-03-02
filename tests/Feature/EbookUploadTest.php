<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EbookUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_uploading_text_ebook()
    {
        Storage::fake('public');

        $content = "Kapitel 1: Der Anfang\n\nEin bisschen Text.\n\nKapitel 2: Die Mitte\n\nNoch mehr Text.\n\nKapitel 3: Das Ende\n\nEnde.";
        $file = UploadedFile::fake()->createWithContent('testbuch.txt', $content);

        $user = \App\Models\User::factory()->create();

        $response = $this->actingAs($user)->post('/upload', [
            'ebook' => $file,
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('ebooks', [
            'title' => 'Ein bisschen Text.',
            'file_type' => 'txt',
            'chapters_count' => 3,
        ]);
    }

    public function test_uploading_pdf_ebook()
    {
        Storage::fake('public');

        // Create a fake PDF file (note: PDFParser might return 0 chapters for a truly fake PDF string because it's not a real PDF structure,
        // but it will test the upload mechanism and file extension handling)
        $file = UploadedFile::fake()->create('testbuch.pdf', 100, 'application/pdf');

        $user = \App\Models\User::factory()->create();

        $response = $this->actingAs($user)->post('/upload', [
            'ebook' => $file,
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('ebooks', [
            'title' => 'Unbekanntes PDF Dokument',
            'file_type' => 'pdf',
        ]);
    }
}
