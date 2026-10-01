<div class="menu-label">Principal</div>

<a href="/admin/dashboard" class="menu-item {{ request()->is('admin/dashboard') ? 'active' : '' }}"><span>📊</span> Tableau de bord</a>

<a href="/admin/inscriptions" class="menu-item {{ request()->is('admin/inscriptions*') ? 'active' : '' }}"><span>📝</span> Inscription</a>

<a href="/admin/eleves" class="menu-item {{ request()->is('admin/eleves*') ? 'active' : '' }}"><span>🎒</span> Élèves</a>


<div class="menu-label">Administrative</div>

<a href="/admin/classes" class="menu-item {{ request()->is('admin/classes*') ? 'active' : '' }}"><span>🏫</span> Classes</a>

<a href="/admin/matieres" class="menu-item {{ request()->is('admin/matieres*') ? 'active' : '' }}"><span>📚</span> Matières</a>

<a href="/admin/utilisateurs" class="menu-item {{ request()->is('admin/utilisateurs*') ? 'active' : '' }}"><span>👥</span> Enseignants</a>


<div class="menu-label">Finances</div>

<a href="/admin/types-frais" class="menu-item {{ request()->is('admin/types-frais*') ? 'active' : '' }}"><span>💵</span> Types de frais</a>

<a href="/admin/paiements" class="menu-item {{ request()->is('admin/paiements*') ? 'active' : '' }}"><span>💰</span> Paiements</a>

<a href="/admin/recus" class="menu-item {{ request()->is('admin/recus*') ? 'active' : '' }}"><span>🧾</span> Reçus</a>

<a href="/admin/echeances" class="menu-item {{ request()->is('admin/echeances*') ? 'active' : '' }}"><span>📆</span> Échéances</a>

<a href="#" class="menu-item" style="opacity:0.5; cursor:not-allowed;"><span>📒</span> Livret de caisse <span style="font-size:10px;">(bientôt)</span></a>


<div class="menu-label">Scolarité</div>

<a href="/admin/absences" class="menu-item {{ request()->is('admin/absences*') ? 'active' : '' }}"><span>📅</span> Absences</a>

<a href="/admin/notes" class="menu-item {{ request()->is('admin/notes*') ? 'active' : '' }}"><span>📝</span> Évaluations, résultats et bulletins</a>


<div class="menu-label">Secrétariat</div>

<a href="/admin/plaintes" class="menu-item {{ request()->is('admin/plaintes*') ? 'active' : '' }}"><span>📋</span> Plaintes</a>