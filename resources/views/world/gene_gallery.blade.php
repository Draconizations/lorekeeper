@extends('world.layout')

@section('title') Gene Gallery @endsection

@section('content')
{!! breadcrumbs(['World' => 'world', 'Genetics' => 'world/genetics', 'Gallery' => 'world/genetics/gallery']) !!}

<h1>Gene Image Gallery</h1>

@if (count($images))
{!! $images->render() !!}
@foreach($images as $group)
    <div class="row world-entry">
        <div class="col-12">
            @include('world._gene_image_group', ['group' => $group])       
        </div>
    </div>
@endforeach
{!! $images->render() !!}
@else
<p>No images found.</p>
@endif

@endsection