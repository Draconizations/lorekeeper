@extends('worldexpansion.layout')

@section('title')
    {{ ucfirst($entry_name) }} {{ ucfirst($category_names) }}
@endsection

@section('content')
    {!! breadcrumbs(['World' => 'world', ucfirst($entry_name) . ' ' . ucfirst($category_names) => 'world/{{ $entry_name }}-{{ $category_names }}']) !!}
    <h1>{{ ucfirst($entry_name) }} {{ ucfirst($category_names) }}</h1>

    <div>
        {!! Form::open(['method' => 'GET', 'class' => 'form-inline justify-content-end']) !!}
        <div class="form-group mr-3 mb-3">
            {!! Form::text('name', Request::get('name'), ['class' => 'form-control']) !!}
        </div>
        <div class="form-group mb-3">
            {!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}
        </div>
        {!! Form::close() !!}
    </div>

    {!! $categories->render() !!}
    <div class="row mx-0">
        @foreach ($categories as $category)
            <div class="col-12 col-md-4 mb-3">
                <div class="card card-body mb-3 p-3 h-100">
                    <div class="we-entry-image">
                        @isset($category->thumb_extension)
                            <a href="{{ $category->thumbUrl }}" data-lightbox="entry" data-title="{{ $category->name }}"><img src="{{ $category->thumbUrl }}" class="world-entry-image mb-3 mw-100" /></a>
                        @endisset
                    </div>
                    <h3>
                        {!! $category->displayName !!}
                        @if ($category->names)
                            ({!! ucfirst($category->names) !!})
                        @endif
                        @if (isset($category->searchUrl) && $category->searchUrl)
                            <a href="{{ $category->searchUrl }}" class="world-entry-search text-muted float-right"><i class="fas fa-search"></i></a>
                        @endif
                    </h3>
                    <div class="card card-body">
                        <div class="world-entry-text">
                            {!! $category->summary !!}
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    {!! $categories->render() !!}

    <div class="text-center mt-4 small text-muted">{{ $categories->total() }} result{{ $categories->total() == 1 ? '' : 's' }} found.</div>
@endsection
