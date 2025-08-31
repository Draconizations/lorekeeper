@if($image)
    {!! Form::open(['url' => 'admin/genetics/images/delete/'.$image->id]) !!}

    <p>You are about to delete the image <strong>{{ $image->name }}</strong>. This is not reversible.</p>
    <p>Are you sure you want to delete <strong>{{ $image->name }}</strong>?</p>

    <div class="text-right">
        {!! Form::submit('Delete Image', ['class' => 'btn btn-danger']) !!}
    </div>

    {!! Form::close() !!}
@else
    Invalid image selected.
@endif
