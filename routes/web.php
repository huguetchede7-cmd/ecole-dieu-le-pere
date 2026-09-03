<?php
use App\Http\Controllers\PaiementController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EleveController;     // ← Ajout important
use App\Http\Controllers\ClasseController;    // ← Ajout recommandé
use App\Http\Controllers\UtilisateurController; // ← Ajout recommandé
use App\Http\Controllers\MatiereController;
use App\Http\Controllers\InscriptionController;
use App\Http\Controllers\TypeFraisController;
use App\Http\Controllers\RecuController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\AbsenceController;
use App\Http\Controllers\PlainteController;

// Page d'accueil → redirige vers login
Route::get('/', function () {
    return redirect('/login');
});

// Authentification
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// ==================== ADMIN ====================
Route::prefix('admin')->name('admin.')->middleware('auth.role:admin')->group(function () {

    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

Route::resource('types-frais', TypeFraisController::class);
Route::get('matieres/niveau/{niveau}', [MatiereController::class, 'niveau'])->name('matieres.niveau');
Route::resource('matieres', MatiereController::class);
Route::get('inscriptions/rechercher-eleve/{matricule}', [InscriptionController::class, 'rechercherEleve'])->name('inscriptions.rechercher-eleve');
Route::resource('inscriptions', InscriptionController::class);
Route::resource('paiements', PaiementController::class);
Route::resource('recus', RecuController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
Route::resource('notes', NoteController::class);
Route::resource('absences', AbsenceController::class);
Route::resource('plaintes', PlainteController::class);

 // Gestion des utilisateurs
    Route::resource('utilisateurs', UtilisateurController::class);

    // Gestion des classes
    Route::resource('classes', ClasseController::class);

    // Gestion des élèves
    Route::resource('eleves', EleveController::class)->parameters(['eleves' => 'eleve'])->except(['create', 'store']);

Route::get('eleves/create', function () {
    return redirect()->route('admin.inscriptions.create');
})->name('eleves.create');

});

// ==================== AUTRES TABLEAUX DE BORD ====================
Route::get('/directeur/dashboard', function () {
    return view('directeur.dashboard');
})->middleware('auth.role:directeur')->name('directeur.dashboard');

Route::get('/enseignant/dashboard', function () {
    return view('enseignant.dashboard');
})->middleware('auth.role:enseignant')->name('enseignant.dashboard');

Route::get('/comptable/dashboard', function () {
    return view('comptable.dashboard');
})->middleware('auth.role:comptable')->name('comptable.dashboard');

Route::get('/secretaire/dashboard', function () {
    return view('secretaire.dashboard');
})->middleware('auth.role:secretaire')->name('secretaire.dashboard');
