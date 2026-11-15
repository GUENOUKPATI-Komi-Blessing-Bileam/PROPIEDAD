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

<div role="status" data-movement-list-message></div>
<section class="panel">
    <div class="table-scroll">
        <table class="data-table" data-movement-list>
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
            <tbody></tbody>
        </table>
    </div>
</section>
@endsection
