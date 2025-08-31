@php
    $loci_images = $images->filter(function ($image) use ($loci, $exclusive) {
        $relevant = count($image->locis->filter(function ($lc) use ($loci) {
            return $lc->id == $loci->id;
        })) > 0;
        $include = count($image->locis->filter(function ($lc) use ($loci, $exclusive) {
            return $lc->id !== $loci->id;
        })) > 0 == !$exclusive;

        return $relevant && $include;
    })->values();

    $image_groups = [];
    
    foreach ($loci_images as $image) {
        $str = $image->genomeString;
        if (!array_key_exists($str, $image_groups)) {
            $image_groups[$str] = [ ];
        }

        array_push($image_groups[$str], $image);
    }
@endphp
@if (count($loci_images))
    @if ($collapse)
        <div class="card">
            <div class="card-header card-title h4 border-0" data-toggle="collapse" href="#images-{{ $loci->id }}">
                <div class="d-flex align-items-center">
                    <span>Show Genome Images {!! add_help('This page only shows images solely associated with this loci. To see possible combinations, click the button on the right.') !!}</span>
                    <a class="btn btn-primary ml-auto" href="{{ url('world/genetics/images?loci='.$loci->id) }}">Show all images</a>
                </div>
            </div>
        </div>
        <div class="collapse" id="images-{{ $loci->id }}">
        
    @endif
    @foreach ($image_groups as $group)
        <div class="card my-2">
            <div class="card-header">
                <h5 class="my-0">{{ $group[0]->name }}</h5>
            </div>
            <div class="card-body">
                <strong>Genome</strong>: {!! $group[0]->genomeDisplay  !!}
                <div class="mt-2">
                    @if (count($group) <= 1)
                        <div class="world-entry-text mt-2">
                            {!! $group[0]->description !!}
                        </div>
                        <img class="genome-image rounded" src="{{ $group[0]->imageUrl.'/'.$group[0]->imageFileName }}" />
                    @else
                        <ul class="nav nav-tabs">
                            @foreach ($group as $image)
                                <li class="nav-item">
                                    <a class="nav-link" id="imageTab-{{ $image->id }}" data-toggle="tab" href="#image-{{ $image->id }}" role="tab">{{ $image->name }}</a>
                                </li>
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
    @endforeach
    @if ($collapse)
    </div>
    @endif
@endif