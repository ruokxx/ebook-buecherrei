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

    public function users()
    {
        // Get all users except current admin
        $users = \App\Models\User::where('id', '!=', auth()->id())
            ->with(['readingSessions.ebook'])
            ->latest()
            ->get();

        $adminCount = \App\Models\User::where('is_admin', true)->count();

        return view('admin.users', compact('users', 'adminCount'));
    }

    public function toggleAdmin(\App\Models\User $user)
    {
        if ($user->is_admin) {
            // Cannot demote if they are the only admin
            if (\App\Models\User::where('is_admin', true)->count() <= 1) {
                return redirect()->back()->with('error', 'Du kannst dem letzten Admin nicht die Rechte entziehen.');
            }
            $user->update(['is_admin' => false]);
            return redirect()->back()->with('success', $user->name . ' ist nun kein Administrator mehr.');
        }
        else {
            $user->update(['is_admin' => true]);
            return redirect()->back()->with('success', $user->name . ' wurde zum Administrator ernannt.');
        }
    }

    public function deleteUser(\App\Models\User $user)
    {
        if ($user->is_admin) {
            return redirect()->back()->with('error', 'Admins können nicht gelöscht werden.');
        }

        $user->delete();
        return redirect()->back()->with('success', 'Benutzer erfolgreich gelöscht!');
    }

    public function settings()
    {
        $settings = [
            'smtp_host' => \App\Models\Setting::get('smtp_host', '127.0.0.1'),
            'smtp_port' => \App\Models\Setting::get('smtp_port', '2525'),
            'smtp_user' => \App\Models\Setting::get('smtp_user', ''),
            'smtp_password' => \App\Models\Setting::get('smtp_password', ''),
            'smtp_encryption' => \App\Models\Setting::get('smtp_encryption', 'tls'),
            'mail_from_address' => \App\Models\Setting::get('mail_from_address', 'hello@example.com'),
            'mail_from_name' => \App\Models\Setting::get('mail_from_name', 'Bücherei'),
            'verification_email_subject' => \App\Models\Setting::get('verification_email_subject', 'Bitte bestätige deine E-Mail-Adresse'),
            'verification_email_body' => \App\Models\Setting::get('verification_email_body', "Hallo {name},\n\nbitte klicke auf den folgenden Link, um deine E-Mail-Adresse zu bestätigen und deinen Account freizuschalten:\n\n{verification_url}\n\nViele Grüße,\nDein Bücherei Team"),
        ];

        return view('admin.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $data = $request->except('_token');

        foreach ($data as $key => $value) {
            \App\Models\Setting::set($key, $value);
        }

        return redirect()->route('admin.settings')->with('success', 'Einstellungen erfolgreich gespeichert.');
    }
}
