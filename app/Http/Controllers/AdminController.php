<?php

namespace App\Http\Controllers;

use App\Models\Ebook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function index()
    {
        $ebooks = Ebook::latest()->get();
        // Get unique sorted genres for the dropdown
        $allGenres = [
            'Fantasy', 'Sci-Fi', 'Krimi / Thriller', 'Romanze',
            'Horror', 'Sachbuch / Bildung', 'Kinderbuch', 'Sonstiges'
        ];

        return view('admin.index', compact('ebooks', 'allGenres'));
    }

    public function updateGenre(Request $request, Ebook $ebook)
    {
        $request->validate([
            'genre' => 'required|string|max:255',
        ]);

        $ebook->update([
            'genre' => $request->genre
        ]);

        return redirect()->back()->with('success', 'Genre erfolgreich aktualisiert!');
    }

    public function destroy(Ebook $ebook)
    {
        // Delete the physical file first
        $path = 'ebooks/' . $ebook->filename;
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }

        // Delete the database record
        $ebook->delete();

        return redirect()->back()->with('success', 'Buch erfolgreich gelöscht!');
    }
}
