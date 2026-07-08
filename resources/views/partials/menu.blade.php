<div class="menu-label">Principal</div>
<a href="/admin/dashboard" class="menu-item {{ request()->is('admin/dashboard') ? 'active' : '' }}"><span>📊</span> Tableau de bord</a>

<div class="menu-label">Gestion</div>
<a href="/admin/utilisateurs" class="menu-item {{ request()->is('admin/utilisateurs*') ? 'active' : '' }}"><span>👥</span> Utilisateurs</a>
<a href="/admin/eleves" class="menu-item {{ request()->is('admin/eleves*') ? 'active' : '' }}"><span>🎒</span> Élèves</a>
<a href="/admin/classes" class="menu-item {{ request()->is('admin/classes*') ? 'active' : '' }}"><span>🏫</span> Classes</a>
<a href="/admin/matieres" class="menu-item {{ request()->is('admin/matieres*') ? 'active' : '' }}"><span>📚</span> Matières</a>
<a href="/admin/inscriptions" class="menu-item {{ request()->is('admin/inscriptions*') ? 'active' : '' }}"><span>📝</span> Inscriptions</a>

<div class="menu-label">Scolarité</div>
<a href="/admin/notes" class="menu-item {{ request()->is('admin/notes*') ? 'active' : '' }}"><span>📝</span> Notes</a>
<a href="/admin/absences" class="menu-item {{ request()->is('admin/absences*') ? 'active' : '' }}"><span>📅</span> Absences</a>

<div class="menu-label">Finance</div>
<a href="/admin/types-frais" class="menu-item {{ request()->is('admin/types-frais*') ? 'active' : '' }}"><span>💵</span> Types de frais</a>
<a href="/admin/paiements" class="menu-item {{ request()->is('admin/paiements*') ? 'active' : '' }}"><span>💰</span> Paiements</a>
<a href="/admin/recus" class="menu-item {{ request()->is('admin/recus*') ? 'active' : '' }}"><span>🧾</span> Reçus</a>

<div class="menu-label">Secrétariat</div>
<a href="/admin/plaintes" class="menu-item {{ request()->is('admin/plaintes*') ? 'active' : '' }}"><span>📋</span> Plaintes</a>
