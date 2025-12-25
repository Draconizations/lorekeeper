@extends('worldexpansion.layout')

@section('title')
    {{ $faction->style }}: Members
@endsection

@section('content')
    {!! breadcrumbs(['World' => 'world', 'Factions' => 'world/factions', $faction->style => 'world/factions/' . $faction->id, 'Members' => 'world/factions/' . $faction->id . '/members']) !!}
    <h1>{!! $faction->fullDisplayName !!}: Members</h1>

    @if (!count($members))
        <p>No members found.</p>
    @else
        {!! $members->render() !!}
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 25%;">Member</th>
                    <th>Rank</th>
                    <th>Standing</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($members as $member)
                    <tr>
                        <td>{!! $member->displayName !!}</td>
                        <td>{!! $member->factionRank ? $member->factionRank->displayName : '-' !!}</td>
                        <td>{!! $currency->display(
                            $member->getCurrencies(true)->where('id', Settings::get('WE_faction_currency'))->first()
                                ? $member->getCurrencies(true)->where('id', Settings::get('WE_faction_currency'))->first()->quantity
                                : 0,
                        ) !!}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {!! $members->render() !!}
        <div class="text-center mt-4 small text-muted">{{ $members->count() }} result{{ $members->count() == 1 ? '' : 's' }} found.</div>
    @endif

@endsection
