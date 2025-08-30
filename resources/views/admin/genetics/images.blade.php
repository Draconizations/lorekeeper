@extends('admin.layout')

@section('admin-title') Genome Images @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Genetics' => 'admin/genetics']) !!}

<h1>
    Genome Images
    <div class="float-right">
        <a class="btn btn-primary" href="{{ url('admin/genetics/images/create') }}"><i class="fas fa-plus mr-1"></i> New Image</a>
    </div>
</h1>

<p>Bla bla bla.</p>

<hr />

@if(!count($images))
    <p class="text-center">No genome images found. Click the button above to create one.</p>
@else
    {!! $images->render() !!}
    <div class="row ml-md-2">
        <div class="d-flex row flex-wrap col-12 pb-1 px-0 ubt-bottom">
            <div class="col-5 font-weight-bold">Name</div>
            <div class="col-7 font-weight-bold">Type</div>
        </div>
        @foreach($images as $image)
            <div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-top">
                <div class="col-5">{{ $image->imageUrl }}</div>
                <div class="col-5">{{ $image->name }})</div>
                <div class="col-2">
                    <a href="{{ url('admin/genetics/images/edit/'.$image->id) }}" class="btn btn-primary py-0 px-1 w-100">Edit</a>
                </div>
            </div>
        @endforeach
    </div>
    {!! $images->render() !!}
@endif
@endsection

@section('scripts')
@parent
@endsection
