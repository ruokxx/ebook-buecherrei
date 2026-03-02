<div align="center">
  <h1>📚 Meine Ebook Bücherei</h1>
  <p>Eine moderne und interaktive Web-Bibliothek für Ebooks (TXT & PDF) im echten Netflix-Style! 🎬</p>
</div>

---

## ✨ Features

- **🚀 Interaktiver Reader:** Lese TXT und PDF Dateien direkt performant im Browser, ohne sie herunterladen zu müssen.
- **📄 Paginierung:** Perfektes Leseerlebnis für Textdateien durch intelligente Seiten-Aufteilung (Pagination) – kein endloses Scrollen mehr!
- **🎨 Modernes Design:** Atemberaubendes **Glassmorphism-UI** im Dark-Mode. 
- **🎬 Netflix-Dashboard:** Bücherübersicht im cineastischen Filmposter-Look mit Zoom-Hover-Effekten.
- **🔍 Smarte Extraktion:** 
  - Liest automatisch echte Buchtitel aus dem Datei-Inneren aus – nie wieder furchtbare Dateinamen im Dashboard!
  - Durchsucht den Text automatisch nach Kapiteln (z.B. "Kapitel 1") und zählt diese mit.
  - Formatiert gefundene Kapitel im Text-Reader visuell als große und farbige Überschriften.
- **🔒 Admin-Sicherheit:** Exklusiver Zugang zum Buch-Upload. Nur eingeloggte Admins (mit sicherem Passwort) können neue Werke zur Bücherei beisteuern.
- **📱 Responsive:** Komplett Smartphone- und Tablet-optimiert.

---

## 🛠️ Installation

Diese Anwendung baut auf **Laravel 10**, **PHP 8.1** und einer leichten **SQLite** Datenbank auf.

### Voraussetzungen

Stelle sicher, dass Folgendes auf deinem System installiert ist:
- **PHP** (>= 8.1)
- **Composer** (PHP Package Manager)
- **Node.js & npm** (Optional, für evtl. Asset-Builds in der Zukunft, aktuell wird Vanilla CSS genutzt)

### Schritt-für-Schritt Anleitung

**1. Repository klonen**
```bash
git clone https://github.com/ruokxx/ebook-buecherrei.git
cd ebook-buecherrei
```

**2. Abhängigkeiten installieren**
```bash
composer install
```

**3. Umgebung konfigurieren**
Kopiere die Beispiel-Konfigurationsdatei und generiere einen Application-Key:
```bash
cp .env.example .env
php artisan key:generate
```
*(Die in der `.env` hinterlegte Datenbank sollte standardmäßig auf `DB_CONNECTION=sqlite` stehen).*

**4. Datenbank aufsetzen & Admin anlegen**
Führe die Migrationen samt Seeder (erstellt den Admin-Account) aus:
```bash
php artisan migrate --seed
```
*Tipp: Unter Windows kann es nötig sein, zuerst eine leere Datei `database/database.sqlite` zu erstellen, falls Laravel fragt oder sich beschwert.*

**5. Speicher (Storage) verlinken**
Damit hochgeladene PDFs/Bilder im Browser zugänglich sind, muss der Storage-Ordner public gemacht werden:
```bash
php artisan storage:link
```

**6. Anwendung starten**
Starte den lokalen Entwicklungs-Server:
```bash
php artisan serve
```
Die App ist nun unter `http://127.0.0.1:8000` erreichbar! 🎉

---

## 🔐 Admin Logindaten

Nach dem erfolgreichen Ausführen von `php artisan migrate --seed` existiert folgender Standard-Nutzer zum **Hochladen von Büchern**:

- **E-Mail:** `admin@example.com`
- **Passwort:** `password`

*(Den Loginbereich findest du ganz unten auf der Startseite im Footer).*

---

<div align="center">
  Mit ❤️ gebaut aus Begeisterung für Code und schöne Benutzeroberflächen.
</div>
