@extends('worldexpansion.layout')

@section('title')
    {{ ucfirst($entry_names) }}
@endsection

@php
    $user_enabled = $user_enabled ?? false;
    $ch_enabled = $ch_enabled ?? false;
@endphp

@section('content')
    {!! breadcrumbs(['World' => 'world', ucfirst($entry_names) => 'world/{{ $entry_names }}']) !!}
    <h1>{{ ucfirst($entry_names) }}</h1>

    <div>
        {!! Form::open(['method' => 'GET', 'class' => '']) !!}
        <div class="form-inline justify-content-end">
            <div class="form-group ml-3 mb-3">
                {!! Form::text('name', Request::get('name'), ['class' => 'form-control', 'placeholder' => 'Name']) !!}
            </div>
            <div class="form-group ml-3 mb-3">
                {!! Form::select('type_id', $categories, Request::get('name'), ['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="form-inline justify-content-end">
            <div class="form-group ml-3 mb-3">
                {!! Form::select(
                    'sort',
                    [
                        'alpha' => 'Sort Alphabetically (A-Z)',
                        'alpha-reverse' => 'Sort Alphabetically (Z-A)',
                        'category' => 'Sort by Category',
                        'newest' => 'Newest First',
                        'oldest' => 'Oldest First',
                    ],
                    Request::get('sort') ?: 'category',
                    ['class' => 'form-control'],
                ) !!}
            </div>
            <div class="form-group ml-3 mb-3">
                {!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}
            </div>
        </div>
        {!! Form::close() !!}
    </div>

    {!! $entries->render() !!}
    <div class="row mx-0">
        @foreach ($entries as $entry)
            @include('worldexpansion._entry', [
                'entry'         => $entry,
                'user_enabled' => $user_enabled,
                'ch_enabled'   => $ch_enabled,
            ])
        @endforeach
    </div>
    {!! $entries->render() !!}

    <div class="text-center mt-4 small text-muted">{{ $entries->total() }} result{{ $entries->total() == 1 ? '' : 's' }} found.</div>
@endsection
