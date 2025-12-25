@extends('worldexpansion.layout')

@section('title')
    {{ $entry->name }}
@endsection

@php
    $user_enabled = $user_enabled ?? false;
    $ch_enabled = $ch_enabled ?? false;
@endphp

@section('content')
    @if (Auth::check() && Auth::user()->hasPower('manage_world'))
        <a data-toggle="tooltip" title="[ADMIN] Edit {{ ucfirst($entry_name) }}" href="{{ url('admin/world/' . $entry_names . '/edit/') . '/' . $entry->id }}" class="mb-2 float-right"><i class="fas fa-crown"></i></a>
    @endif
    {!! breadcrumbs(['World' => 'world', ucfirst($entry_names) => 'world/' . $entry_names, $entry->style => 'world/' . $entry_names . '/' . $entry->id]) !!}
    <h1 style="clear: both;">
        @if ($entry->thumbUrl)
            <img class="rounded mr-2" src="{{ $entry->thumbUrl }}" style="max-height:1.5em;vertical-align:middle;" />
        @endif
        {!! $entry->displayName !!}
        @if ($entry->scientific_name)
            <span style="opacity:0.5; font-size:0.7em;font-style:italic">· {!! $entry->scientific_name !!}</span>
        @endif
    </h1>
    <div>
        <p class="m-0">
            @if ($entry->type || $entry->parent)
                <span class="h5 mb-2">
                    @if ($entry->type)
                        <a class="badge badge-primary" href="{{ $entry->type->url }}">{{ ucfirst($entry->type->name) }}</a>
                    @endif
                    @if ($entry->parent)
                        <a class="badge badge-secondary" href="{{ $entry->parent->url }}">{{ ucfirst($entry->parent->name) }}</a>
                    @endif
                </span>
                @if (($user_enabled && $entry->is_user_faction) || ($ch_enabled && $entry->is_character_faction))
                    <span>
                        Can be joined by
                        {!! $entry->is_character_faction && $entry->is_user_faction ? 'both' : '' !!}
                        <strong>{!! $user_enabled && $entry->is_user_faction ? 'users' : '' !!}</strong>{!! $entry->is_character_faction && $entry->is_user_faction ? ' and' : '' !!}{!! !$entry->is_character_faction && $entry->is_user_faction ? '.' : '' !!}
                        <strong>{!! $ch_enabled && $entry->is_character_faction ? 'characters.' : '' !!}</strong>
                    </span>
                @elseif (($user_enabled && $entry->is_user_home) || ($ch_enabled && $entry->is_character_home))
                    <span>
                        Can be home to
                        {!! $entry->is_character_home && $entry->is_user_home ? 'both' : '' !!}
                        <strong>{!! $user_enabled && $entry->is_user_home ? 'users' : '' !!}</strong>{!! $entry->is_character_home && $entry->is_user_home ? ' and' : '' !!}{!! !$entry->is_character_home && $entry->is_user_home ? '.' : '' !!}
                        <strong>{!! $ch_enabled && $entry->is_character_home ? 'characters.' : '' !!}</strong>
                    </span>
                @endif
            @elseif ($entry->category)
                <span class="h5 mb-2">
                    <a class="badge badge-primary" href="{{ $entry->category->url }}">{{ ucfirst($entry->category->name) }}</a>
                </span>
            @endif
            {!! $entry->faction ? '・ Part of ' . ucfirst($entry->faction->displayName) : '' !!}{!! $entry->factionRank ? ' (' . $entry->factionRank->name . ')' : null !!}
            @if ($entry->occur_start || $entry->occur_end)
                <span class="text-muted">{!! $entry->occur_start ? format_date($entry->occur_start, false) : '' !!} {!! $entry->occur_end ? '- ' . format_date($entry->occur_end, false) : ($entry->occur_start ? '- ' : '') . 'Ongoing' !!}</span>
            @endif
        </p>
        @if ($entry->birth_date || $entry->death_date)
            <p class="text-muted">{!! $entry->birth_date ? 'Born: ' . format_date($entry->birth_date, false) : 'Born: Unknown' !!} {!! $entry->death_date ? '- Died: ' . format_date($entry->death_date, false) : '- Died: Unknown' !!}</p>
        @endif
        @isset($entry->summary)
            <div class="world-entry-text my-2 font-italic">{!! $entry->summary !!}</div>
        @endisset
    </div>
    @if ($entry->image_extension)
        <div><img src="{{ $entry->imageUrl }}" class="w-100 rounded" /></div>
    @else
        <hr />
    @endif

    @isset($entry->parsed_description)
        <div class="world-entry-text mt-3 mb-4">
            {!! $entry->parsed_description !!}
        </div>
    @endisset

    @if ($entry->ranks && $entry->ranks()->count())
        <div class="mt-3">
            <h3>Faction Ranks & Members</h3>
            <h5>Members: {{ $entry->factionMembers->count() }} ・ <a href="{{ url('world/factions/' . $entry->id . '/members') }}">See All</a></h5>
            <hr />
            <div class="row">
                @if ($entry->ranks()->where('is_open', 0)->count())
                    <div class="col-md">
                        <h4>Leadership Ranks</h4>
                        <div class="list-group">
                            @foreach ($entry->ranks()->where('is_open', 0)->orderBy('sort')->get() as $rank)
                                <div class="list-group-item">
                                    <h5>{{ $rank->name }}</h5>
                                    @if ($rank->description)
                                        <p>{{ $rank->description }}
                                        </p>
                                    @endif
                                    @if ($rank->members()->count())
                                        <ul>
                                            @foreach ($rank->members as $member)
                                                <li>
                                                    {!! $member->memberObject->displayName !!}{{ !$loop->last ? ',' : '' }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                @if ($entry->ranks()->where('is_open', 1)->count())
                    <div class="col-md">
                        <h4>Member Ranks</h4>
                        <div class="list-group">
                            @foreach ($entry->ranks()->where('is_open', 1)->orderBy('sort')->get() as $rank)
                                <div class="list-group-item">
                                    <h5>
                                        {{ $rank->name }}
                                        <small style="opacity:0.7;">{!! $currency ? ' (' . $currency->display($rank->breakpoint) . ')' : ' (' . $rank->breakpoint . ' Standing)' !!}</small>
                                    </h5>
                                    @if ($rank->description)
                                        <p>{{ $rank->description }}
                                        </p>
                                    @endif
                                    @if ($rank->members()->count())
                                        <ul>
                                            @foreach ($rank->members as $member)
                                                <li>
                                                    {!! $member->memberObject->displayName !!}{{ !$loop->last ? ',' : '' }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    @if ((isset($entry->children) && count($entry->children)) || count(allAttachments($entry)) || (isset($entry->members) && count($entry->members)))
        <div class="card card-body mt-3">
            <div class="row">
                @if (isset($entry->children) && count($entry->children))
                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="h-100 d-flex flex-column">
                            <h4 class="mb-2">Contains the following</h4>
                            <div class="card card-body py-3 mb-2">
                                @foreach ($entry->children->groupBy('type_id') as $group => $children)
                                    <p class="mb-0">
                                        <strong>
                                            {{ $loctypes->find($group)->names }}:
                                        </strong>
                                        @foreach ($children as $key => $child)
                                            {!! $child->fullDisplayName !!}@if ($key != count($children) - 1)
                                                ,
                                            @endif
                                        @endforeach
                                    </p>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
                @if (count(allAttachments($entry)))
                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="h-100 d-flex flex-column">
                            <h4 class="mb-2">Associated with</h4>
                            <div class="card card-body py-3 mb-2">
                                @foreach (allAttachments($entry) as $type => $attachments)
                                    <p class="mb-0">
                                        <strong>
                                            {{ $type }}s:
                                        </strong>
                                        @foreach ($attachments as $key => $attachment)
                                            {!! $attachment->displayName !!}@if ($key != count($attachments) - 1)
                                                ,
                                            @endif
                                        @endforeach
                                    </p>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
                @if (isset($entry->members) && count($entry->members))
                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="h-100 d-flex flex-column">
                            <h4 class="mb-2">Members Figures</h4>
                            <div class="card card-body py-3 mb-2">
                                @foreach ($entry->members->groupBy('category_id') as $key => $members)
                                    <p class="mb-0">
                                        <strong>
                                            {{ $figure_categories->find($key) ? $figure_categories->find($key)->name : 'Miscellanous' }}:
                                        </strong>
                                        @foreach ($members as $key => $member)
                                            <strong>{!! $member->displayName !!}</strong>
                                            @if ($key != count($members) - 1 && count($members) > 2)
                                                ,
                                                @endif @if ($key == count($members) - 2)
                                                    and
                                                @endif
                                            @endforeach
                                    </p>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
@endsection
