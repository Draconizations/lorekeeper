@extends('worldexpansion.layout')

@section('title')
    {{ $category->name }}
@endsection

@section('content')
    @if (Auth::check() && Auth::user()->hasPower('manage_world'))
        <a data-toggle="tooltip" title="[ADMIN] Edit {{ ucfirst($category_name) }}" href="{{ url('admin/world/' . $entry_name . '-' . $category_names . '/edit/') . '/' . $category->id }}" class="mb-2 float-right"><i class="fas fa-crown"></i></a>
    @endif
    {!! breadcrumbs(['World' => 'world', ucfirst($entry_name) . ' ' . ucfirst($category_names) => 'world/{{ $entry_name }}-{{ $category_names }}']) !!}
    <h1 style="clear: both;">
        @if ($category->thumbUrl)
            <img class="rounded mr-2" src="{{ $category->thumbUrl }}" style="max-height:1.5em;vertical-align:middle;" />
        @endif
        {!! $category->displayName !!}
    </h1>
    <div>
        @isset($category->summary)
            <div class="world-entry-text my-2 font-italic">{!! $category->summary !!}</div>
        @endisset
    </div>

    @if ($category->image_extension)
        <div class="mx-n4"><img src="{{ $category->imageUrl }}" class="w-100" /></div>
    @elseif ($category->parsed_description)
        <hr />
    @endif

    @isset($category->parsed_description)
        <div class="world-entry-text mt-3 mb-4">
            {!! $category->parsed_description !!}
        </div>
    @endisset

    @if (count($entries))
        <h4 class="mt-3">All {{ $category->names ? $category->names : $category->name }} ({{ count($entries) }})</h4>
        <ul>
            @foreach ($entries->groupBy('type_id') as $group => $entry)
                @foreach ($entry as $key => $child)
                    <li>
                        <strong>{!! $child->displayName !!}</strong>
                        @if ($child->parent)
                            (part of {!! $child->parent->displayName !!})
                        @endif
                    </li>
                @endforeach
            @endforeach
        </ul>
    @else
        <h5 class="mt-3 mb-0 text-center col-12">There aren't any {{ $category->names ? $category->names : $category->name }} yet</h5>
    @endif
@endsection
