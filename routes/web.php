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
use App\Http\Controllers\EcheanceController;
use App\Http\Controllers\RecuController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\AbsenceController;
use App\Http\Controllers\PlainteController;
use App\Http\Controllers\SuiviPaiementController;
use App\Http\Controllers\LivretCaisseController;
use App\Http\Controllers\ParametreController;
use App\Http\Controllers\DashboardController;

// Page d'accueil → redirige vers login
Route::get('/', function () {
    return redirect('/login');
});

// Authentification
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// ==================== ESPACE DE GESTION (par rôle) ====================
// Chaque groupe indique quels rôles peuvent y accéder.
Route::prefix('admin')->name('admin.')->group(function () {

    // --- Tableau de bord : admin, directeur, comptable ---
    Route::middleware('auth.role:admin,directeur,comptable,secretaire,enseignant')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    });

    // --- Inscriptions et élèves : admin, directeur, secrétaire ---
    Route::middleware('auth.role:admin,directeur,secretaire')->group(function () {
        Route::get('inscriptions/rechercher-eleve/{recherche}', [InscriptionController::class, 'rechercherEleve'])->name('inscriptions.rechercher-eleve');
        Route::get('inscriptions/details-eleve/{id}', [InscriptionController::class, 'detailsEleve'])->name('inscriptions.details-eleve');
        Route::resource('inscriptions', InscriptionController::class);

        Route::get('eleves/create', function () {
            return redirect()->route('admin.inscriptions.create');
        })->name('eleves.create');
        Route::resource('eleves', EleveController::class)->parameters(['eleves' => 'eleve'])->except(['create', 'store']);
    });

    // --- Classes et matières : admin, directeur ---
    Route::middleware('auth.role:admin,directeur')->group(function () {
        Route::resource('classes', ClasseController::class);
        Route::get('matieres/niveau/{niveau}', [MatiereController::class, 'niveau'])->name('matieres.niveau');
        Route::resource('matieres', MatiereController::class);
    });

    // --- Paiements et suivi des paiements (encaissement) : admin, comptable ---
    Route::middleware('auth.role:admin,comptable')->group(function () {
        Route::get('paiements/eleves-classe/{classe}', [PaiementController::class, 'elevesParClasse'])->name('paiements.eleves-classe');
        Route::get('paiements/solde-eleve/{eleve}/{classe}', [PaiementController::class, 'soldeEleve'])->name('paiements.solde-eleve');
        Route::resource('paiements', PaiementController::class);

        Route::get('suivi-paiements', [SuiviPaiementController::class, 'index'])->name('suivi-paiements.index');
        Route::get('suivi-paiements/rechercher', [SuiviPaiementController::class, 'rechercher'])->name('suivi-paiements.rechercher');
        Route::get('suivi-paiements/fiche/{eleve}', [SuiviPaiementController::class, 'fiche'])->name('suivi-paiements.fiche');
        Route::post('suivi-paiements/encaisser', [SuiviPaiementController::class, 'encaisser'])->name('suivi-paiements.encaisser');
    });

    // --- Reçus, livret de caisse, types de frais, échéances : admin, directeur, comptable ---
    Route::middleware('auth.role:admin,directeur,comptable')->group(function () {
        Route::resource('recus', RecuController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
        Route::get('livret-caisse', [LivretCaisseController::class, 'index'])->name('livret-caisse.index');
        Route::resource('types-frais', TypeFraisController::class);
        Route::resource('echeances', EcheanceController::class);
    });

    // --- Absences : admin, directeur, secrétaire, enseignant ---
    Route::middleware('auth.role:admin,directeur,secretaire,enseignant')->group(function () {
        Route::get('absences/eleves-classe/{classe}/{date}', [AbsenceController::class, 'elevesAvecAbsences'])->name('absences.eleves-classe');
        Route::resource('absences', AbsenceController::class);
    });

        // --- Notes et bulletins : admin, directeur, enseignant ---
    Route::middleware('auth.role:admin,directeur,enseignant')->group(function () {
        Route::get('notes/eleves-note/{classe}/{matiere}/{periode}/{annee}', [NoteController::class, 'elevesAvecNotes'])->name('notes.eleves-note');
        Route::get('notes/bulletin/{eleve}/{annee_scolaire}', [NoteController::class, 'bulletin'])->name('notes.bulletin');
        Route::get('notes/eleve/{eleve}/{annee_scolaire}', [NoteController::class, 'notesEleve'])->name('notes.eleve');
        Route::resource('notes', NoteController::class);
    });

    // --- Plaintes : admin, directeur, secrétaire ---
    Route::middleware('auth.role:admin,directeur,secretaire')->group(function () {
        Route::resource('plaintes', PlainteController::class);
    });

    // --- Paramètres et utilisateurs : admin seulement ---
    Route::middleware('auth.role:admin')->group(function () {
        Route::get('parametres', [ParametreController::class, 'index'])->name('parametres.index');
        Route::put('parametres', [ParametreController::class, 'update'])->name('parametres.update');
        Route::resource('utilisateurs', UtilisateurController::class);
    });
});