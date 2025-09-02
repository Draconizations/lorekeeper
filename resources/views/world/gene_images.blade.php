@extends('world.layout')

@section('title') {{ $loci->name }} Images @endsection

@section('content')
{!! breadcrumbs(['World' => 'world', 'Genetics' => 'world/genetics', 'Gallery' => 'world/genetics/gallery']) !!}
<h1>Gene Images</h1>
<hr/>
<div class="row world-entry">
    <div class="col-12">
        <h3>
            @if (!$loci->is_visible)
                <i class="fas fa-eye-slash"></i>
            @endif
            {!! $loci->displayName !!}
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
    </div>
</div>
@if (count($images))
    {!! $images->render() !!}
    @foreach ($images->getCollection()->toArray() as $group)
        @include('world._gene_image_group', ['group' => $group])
    @endforeach
    {!! $images->render() !!}
@else
    <p>No images found.</p>
@endif

@endsection
