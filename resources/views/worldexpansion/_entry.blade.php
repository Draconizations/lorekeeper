@php
    $user_enabled = $user_enabled ?? false;
    $ch_enabled = $ch_enabled ?? false;
@endphp

<div class="col-12 col-md-6 col-xl-4 mb-2">
    <div class="card card-body h-100 text-center">
        <div class="we-entry-image">
            @isset($entry->thumb_extension)
                <a href="{{ $entry->thumbUrl }}" data-lightbox="entry" data-title="{{ $entry->name }}">
                    <img src="{{ $entry->thumbUrl }}" class="mb-3 w-100" />
                </a>
            @endisset
        </div>
        <div>
            <h3 class="mb-0">
                {!! $entry->displayName !!}
                @if ($entry->scientific_name)
                    <span style="opacity:0.5; font-size:0.7em;font-style:italic">· {!! $entry->scientific_name !!}</span>
                @endif
            </h3>
            @if ($entry->category)
                <div class="h5">
                    <a class="badge badge-primary" href="{{ $entry->category->url }}">
                        {{ ucfirst($entry->category->name) }}
                    </a>
                </div>
            @endif
        </div>

        @if ($entry->parent || $entry->type)
            <p class="mb-0 h5">
                @if ($entry->type)
                    <a class="badge badge-primary" href="{{ $entry->type->url }}">{{ ucfirst($entry->type->name) }}</a>
                @endif
                @if ($entry->parent)
                    <a class="badge badge-secondary" href="{{ $entry->parent->url }}">{{ ucfirst($entry->parent->name) }}</a>
                @endif
            </p>
        @endif

        @if (($user_enabled && $entry->is_user_faction) || ($ch_enabled && $entry->is_character_faction))
            <p class="mb-0">
                Can be joined by
                {!! $entry->is_character_faction && $entry->is_user_faction ? 'both' : '' !!}
                <strong>{!! $user_enabled && $entry->is_user_faction ? 'users' : '' !!}</strong>{!! $entry->is_character_faction && $entry->is_user_faction ? ' and' : '' !!}{!! !$entry->is_character_faction && $entry->is_user_faction ? '.' : '' !!}
                <strong>{!! $ch_enabled && $entry->is_character_faction ? 'characters.' : '' !!}</strong>
            </p>
        @elseif (($user_enabled && $entry->is_user_home) || ($ch_enabled && $entry->is_character_home))
            <p class="mb-0">
                Can be home to
                {!! $entry->is_character_home && $entry->is_user_home ? 'both' : '' !!}
                <strong>{!! $user_enabled && $entry->is_user_home ? 'users' : '' !!}</strong>{!! $entry->is_character_home && $entry->is_user_home ? ' and' : '' !!}{!! !$entry->is_character_home && $entry->is_user_home ? '.' : '' !!}
                <strong>{!! $ch_enabled && $entry->is_character_home ? 'characters.' : '' !!}</strong>
            </p>
        @endif

        @isset($entry->summary)
            <div class="card card-body mt-1">
                <p class="mb-0"> {!! $entry->summary !!}</p>
            </div>
        @endisset
    </div>
</div>
