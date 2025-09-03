<div class="card my-2">
    <div class="card-header">
        <h5 class="my-0">{{ $group[0]->title ? $group[0]->title : $group[0]->name }}</h5>
    </div>
    <div class="card-body">
        <strong>Genome</strong>: {!! $group[0]->genomeDisplay  !!}
        <div class="mt-2">
            @if (count($group) <= 1)
                <hr />
                <img class="genome-image rounded mw-100" src="{{ $group[0]->imageUrl.'/'.$group[0]->imageFileName }}" />
                <div class="world-entry-text mt-2">
                    {!! $group[0]->description !!}
                </div>
            @else
                <ul class="nav nav-tabs">
                    @foreach ($group as $image)
                        <li class="nav-item">
                            <a class="nav-link {{ $loop->first ? 'active' : '' }}" id="imageTab-{{ $image->id }}" data-toggle="tab" href="#image-{{ $image->id }}" role="tab">{{ $image->name }}</a>
                        </li>
                    @endforeach
                </ul>
                <div class="tab-content">
                    @foreach ($group as $image)
                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="image-{{ $image->id }}">
                            <img class="genome-image rounded mw-100" src="{{ $image->imageUrl.'/'.$image->imageFileName }}" />
                            <div class="world-entry-text mt-2">
                                {!! $image->description !!}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>