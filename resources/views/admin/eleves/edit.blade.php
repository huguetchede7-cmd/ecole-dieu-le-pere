@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Modifier l'Élève</h2>
        <a href="{{ route('admin.eleves.index') }}" class="btn btn-secondary">
            ← Retour à la liste
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.eleves.update', $eleve->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <!-- Informations de l'enfant -->
                    <div class="col-md-6">
                        <h5>Informations de l'enfant</h5>
                        
                        <div class="mb-3">
                            <label class="form-label">Nom <span class="text-danger">*</span></label>
                            <input type="text" name="nom" class="form-control" value="{{ old('nom', $eleve->nom) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Prénom <span class="text-danger">*</span></label>
                            <input type="text" name="prenom" class="form-control" value="{{ old('prenom', $eleve->prenom) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Date de Naissance <span class="text-danger">*</span></label>
                            <input type="date" name="date_naissance" class="form-control" 
                                   value="{{ old('date_naissance', $eleve->date_naissance) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Sexe <span class="text-danger">*</span></label>
                            <select name="sexe" class="form-control" required>
                                <option value="M" {{ old('sexe', $eleve->sexe) == 'M' ? 'selected' : '' }}>Masculin</option>
                                <option value="F" {{ old('sexe', $eleve->sexe) == 'F' ? 'selected' : '' }}>Féminin</option>
                            </select>
                        </div>

                        <!-- Photo -->
                        <div class="mb-3">
                            <label class="form-label">Photo de l'élève</label>
                            @if($eleve->photo)
                                <div class="mb-3">
                                    <img src="{{ asset('storage/' . $eleve->photo) }}" 
                                         alt="Photo actuelle" 
                                         class="img-thumbnail" 
                                         style="max-width: 180px; border-radius: 8px;">
                                    <p class="text-muted small mt-1">Photo actuelle</p>
                                </div>
                            @endif
                            <input type="file" name="photo" class="form-control" accept="image/*">
                            <small class="text-muted">Laissez vide si vous ne souhaitez pas changer la photo</small>
                        </div>
                    </div>

                    <!-- Inscription -->
                    <div class="col-md-6">
                        <h5>Inscription</h5>
                        
                        <div class="mb-3">
                            <label class="form-label">Classe <span class="text-danger">*</span></label>
                            <select name="classe_id" class="form-control" required>
                                <option value="">Sélectionner une classe</option>
                                @foreach($classes as $classe)
                                    <option value="{{ $classe->id }}" 
                                        {{ old('classe_id', $eleve->classe_id) == $classe->id ? 'selected' : '' }}>
                                        {{ $classe->nom }} {{ $classe->niveau ? '(' . $classe->niveau . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Année Scolaire <span class="text-danger">*</span></label>