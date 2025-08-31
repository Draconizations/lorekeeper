<div class="card my-2">
    <div class="card-header">
        <h5 class="my-0">{{ $group[0]->title ? $group[0]->title : $group[0]->name }}</h5>
    </div>
    <div class="card-body">
        <strong>Genome</strong>: {!! $group[0]->genomeDisplay  !!}
        <div class="mt-2">
            @if (count($group) <= 1)
                <div class="world-entry-text mt-2">
                    {!! $group[0]->description !!}
                </div>
                <hr />
                <img class="genome-image rounded" src="{{ $group[0]->imageUrl.'/'.$group[0]->imageFileName }}" />
            @else
                <ul class="nav nav-tabs">
                    @php
                        $i = 0;
                    @endphp
                    @foreach ($group as $image)
                        <li class="nav-item">
                            <a class="nav-link {{ $i == 0 ? 'active' : '' }}" id="imageTab-{{ $image->id }}" data-toggle="tab" href="#image-{{ $image->id }}" role="tab">{{ $image->name }}</a>
                        </li>
                        @php
                            $i++
                        @endphp
                    @endforeach
                </ul>
                <div class="tab-content">
                    @php
                        $i = 0;
                    @endphp
                    @foreach ($group as $image)
                        <div class="tab-pane {{ $i == 0 ? 'show active' : '' }}" id="image-{{ $image->id }}">
                            <div class="world-entry-text mt-2">
                                {!! $image->description !!}
                            </div>
                            <img class="genome-image rounded" src="{{ $image->imageUrl.'/'.$image->imageFileName }}" />
                        </div>
                        @php
                            $i++
                        @endphp
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>