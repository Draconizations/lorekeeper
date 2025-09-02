@php
    $query = clone $images;
    $query->whereHas('locis', function ($query) use ($loci) {
        $query->where('locis.id', '=', $loci->id);
    });

    $all_query = clone $images;

    $some_images = $query->whereDoesntHave('locis', function ($query) use ($loci) {
        $query->where('locis.id', '!=', $loci->id);
    })->get();
    $all_images = $all_query->get();


    $image_groups = \App\Models\Genetics\GenomeImage::collectImages($some_images);
@endphp

<div class="row world-entry">
    <div class="col-12">
        <h3 class="mb-0">
            @if (!$loci->is_visible)
                <i class="fas fa-eye-slash"></i>
            @endif
            {!! $loci->displayName !!}
            @if (count($all_images))
                <a class="btn btn-primary float-right" href="{{ url('world/genetics/gallery/'.$loci->id) }}">Show all images</a>
            @endif
        </h3>
        @if ($loci->type == "gene")
            <strong>Type</strong>: Standard<br>
            <strong>Alleles</strong>:
            @if(Auth::check() && Auth::user()->hasPower('view_hidden_genetics'))
                @foreach ($loci->alleles as $allele)
                    <div class="d-inline text-monospace {{ $allele->is_visible ? "" : "text-muted font-italic" }} px-1" data-toggle="tooltip" title="{{ $allele->summary }}">{!! $allele->displayName !!}</div>
                @endforeach
            @else
                @foreach ($loci->visibleAlleles as $allele)
                    <div class="d-inline text-monospace px-1" data-toggle="tooltip" title="{{ $allele->summary }}">{!! $allele->displayName !!}</div>
                @endforeach
            @endif
        @else
            <strong>Type</strong>: {{ ucfirst($loci->type) }}<br>
            <strong>Range</strong>: 0-{{ $loci->length }}
        @endif
        <div class="world-entry-text mt-2">
            {!! $loci->description !!}
        </div>
        @if (count($image_groups))
            <div class="card">
                <div class=card-header align-items-center border-0">
                    <div class="card-title h4 m-0" data-toggle="collapse" href="#images-{{ $loci->id }}">
                        Show Base Gene Images {!! add_help('This page only shows images solely associated with this gene. To see all possible combinations with this gene, click the button at the top.') !!}
                    </div>
                </div>
            </div>
            <div class="collapse" id="images-{{ $loci->id }}">
            @foreach ($image_groups as $group)
                @include('world._gene_image_group', ['group' => $group])
            @endforeach
            </div>
        @endif
    </div>
</div>
