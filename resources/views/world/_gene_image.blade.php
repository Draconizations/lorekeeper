@php
    $loci_images = $images->filter(function ($image) use ($loci, $exclusive) {
        $relevant = count($image->locis->filter(function ($lc) use ($loci) {
            return $lc->id == $loci->id;
        })) > 0;
        $include = $exclusive ? count($image->locis->filter(function ($lc) use ($loci, $exclusive) {
            return $lc->id !== $loci->id;
        })) > 0 == !$exclusive : true;

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
            <div class="d-flex flex-column flex-sm-row card-header align-items-center border-0">
                <div style="flex: 1;" class="card-title h4 m-0" data-toggle="collapse" href="#images-{{ $loci->id }}">
                    Show Genome Images {!! add_help('This page only shows images solely associated with this loci. To see possible combinations, click the button on the right.') !!}
                </div>
                <a class="btn btn-primary mt-2 mt-sm-0 ml-sm-2" href="{{ url('world/genetics/'.$loci->id) }}">Show all images</a>
            </div>
        </div>
        <div class="collapse" id="images-{{ $loci->id }}">
        
    @endif
    @foreach ($image_groups as $group)
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