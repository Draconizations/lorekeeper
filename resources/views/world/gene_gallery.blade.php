@extends('world.layout')

@section('title') Gene Gallery @endsection

@section('content')
{!! breadcrumbs(['World' => 'world', 'Genetics' => 'world/genetics', 'Gallery' => 'world/genetics/gallery']) !!}

@php
    $images = $images->sortBy(function ($img) {
       return $img->locis->count();
    })->sortBy('genomeString')->values();

    $image_groups = [];
    
    foreach ($images as $image) {
        $str = $image->genomeString;
        if (!array_key_exists($str, $image_groups)) {
            $image_groups[$str] = [ ];
        }

        array_push($image_groups[$str], $image);
    }
@endphp

<h1>Gene Image Gallery</h1>

@foreach($image_groups as $group)
    <div class="row world-entry">
        <div class="col-12">
            @include('world._gene_image_group', ['group' => $group])       
        </div>
    </div>
@endforeach

@endsection