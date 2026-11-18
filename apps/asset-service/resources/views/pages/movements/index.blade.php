@extends('layouts.app')

@section('title', 'Mouvements')
@section('section', 'Suivi des mouvements')

@section('content')
<div class="page-heading">
    <div>
        <p class="eyebrow">Suivi des mouvements</p>
        <h1>Mouvements</h1>
        <p class="page-lead">Consultez les demandes de mouvement enregistrées.</p>
    </div>
    <a class="button button-primary" href="{{ route('movements.create') }}"><span aria-hidden="true">＋</span> Soumettre un mouvement</a>
</div>

@if (session('status'))
    <p class="submission-success" role="status">{{ session('status') }}</p>
@endif

<section class="panel">
    <div class="table-scroll">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Bien</th>
                    <th>Type</th>
                    <th>Destination</th>
                    <th>Date d’effet</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($movements as $movement)
                    <tr>
                        <td>{{ strtoupper(substr($movement->id, 0, 8)) }}</td>
                        <td><a class="table-primary" href="{{ route('assets.show', $movement->code_inventaire) }}">{{ $movement->code_inventaire }} · {{ $movement->libelle }}</a></td>
                        <td>{{ $movement->type }}</td>
                        <td>{{ $movement->service_destination ?? '—' }}@if ($movement->site_destination)<small>{{ $movement->site_destination }}</small>@endif</td>
                        <td>{{ \Illuminate\Support\Carbon::parse($movement->date_effet)->format('d/m/Y') }}</td>
                        <td><span class="status status-warn">{{ $movement->statut === 'pending' ? 'En attente de validation' : $movement->statut }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="6">Aucun mouvement enregistré.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($movements->hasPages())
        <div class="pagination-row">{{ $movements->links() }}</div>
    @endif
</section>
@endsection
