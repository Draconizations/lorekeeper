@extends('admin.layout')

@section('admin-title') Genome Images @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Genome Images' => 'admin/genetics/images']) !!}

<h1>
    {{ $image->id ? 'Edit' : 'Create' }} Genome Image
    <a href="#" class="btn btn-danger float-right delete-image-button">Delete Genome Image</a>
</h1>

<p>Bla bla bla.</p>

<hr />

{!! Form::open(['url' => $image->id ? 'admin/genetics/images/edit/'.$image->id : 'admin/genetics/images/create', 'files' => true]) !!}

<h3>Basic Information</h3>

<div class="form-group">
    {!! Form::label('Image Label') !!}
    {!! Form::text('name', $image->name, ['class' => 'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label('Image File') !!} {!! add_help('Bla bla bla.') !!}
    <div>{!! Form::file('image') !!}</div>
    <div class="text-muted">Recommended size: None (Choose a standard size for all genome images)</div>
</div>

<div class="form-group">
    {!! Form::label('Description (Optional)') !!}
    {!! Form::textarea('description', $image->description, ['class' => 'form-control wysiwyg']) !!}
</div>

<div class="form-group">
    {!! Form::checkbox('is_visible', 1, $image->id ? $image->is_visible : 1, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
    {!! Form::label('is_visible', 'Is Visible', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If turned off, the image will not be visible in the gallery to regular users.') !!}
</div>

<div class="text-right mb-3">
    <a href="#" class="btn btn-outline-info" id="addAllele">Add Allele</a>
</div>

<table class="table table-sm" id="alleleTable">
    <thead>
        <tr>
            <th width="35%">Loci</th>
            <th width="55%">Value</th>
            <th width="10%"></th>
        </tr>
    </thead>
    <tbody id="alleleTableBody">
        @for($i = 0; $i < count($loci_list); $i++)
            <tr id="allele-row-{{ $i }}">
                <td>{!! Form::select('loci_ids[]', $locis->pluck('name', 'id'), $loci_list->id, ['class' => 'form-control loci-select', 'placeholder' => 'Select Loci']) !!}</td>
                <td class="allele-row-select">
                    @if ($loci->type == "gene")
                        <div class="input-group">
                            {!! Form::select('allele_left_ids[]', $loci->alleles->pluck('name', 'id'), $alleles_left[i]->id, ['class' => 'form-control allele-select input-group-prepend', 'placeholder' => 'Select Allele']) !!}
                            {!! Form::select('allele_right_ids[]', [null, 'N/A'] + $loci->alleles->pluck('name', 'id'), $alleles_right[i]->id, ['class' => 'form-control allele-select input-group-append', 'placeholder' => 'Select Allele']) !!}
                        </div>
                    @else
                        {!! Form::number('loci_positions[]'), 1, [ 'class' => 'form-control', 'min' => 0, 'max' => $loci->length ] !!}
                    @endif
                    </td>
                <td class="text-right"><a href="#" class="btn btn-danger remove-allele-button">Remove</a></td>
            </tr>
        @endfor
    </tbody>
</table>

<div class="text-right">
    {!! Form::submit($image->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
</div>

{!! Form::close() !!}

<div id="alleleRowData" class="hide">
    <table class="table table-sm">
        <tbody id="alleleRow">
            <tr class="allele-row">
                @include('admin.genetics._create_edit_image_allele', ['allele_list' => [], 'loci' => new \App\Models\Genetics\Loci])
            </tr>
        </tbody>
    </table>
</div>
@endsection

@section('scripts')
@parent

<script>
var rowCount = {{ count($loci_list) }};

$( document ).ready(function() {    
    var $alleleTable  = $('#alleleTableBody');
    var $alleleRow = $('#alleleRow .allele-row');
    $('#allelteTableBody .selectize').selectize();

    attachRemoveListener($('#alleleTableBody .remove-allele-button'));

    $('#addAllele').on('click', function(e) {
        e.preventDefault();
        var $clone = $alleleRow.clone();
        $alleleTable.append($clone);
        $clone.attr('id', 'allele-row-' + rowCount);

        attachRefreshListener($('#allele-row-' + rowCount + ' .loci-select'), '#allele-row-' + rowCount);
        attachRemoveListener($('#allele-row-' + rowCount + ' .remove-allele-button'));

        rowCount++;
    });
});

function attachRefreshListener(node, id) {
    node.on('change', function(e) {
        e.preventDefault();
        refreshAlleles(id, e.target.value);
    });
}

function attachRemoveListener(node) {
    node.on('click', function(e) {
        e.preventDefault();
        $(this).parent().parent().remove();
    });
}

function refreshAlleles(row, loci) {
    $.ajax({
        type: "GET",
        url: "{{ url('admin/genetics/images/check-alleles') }}?loci=" + loci,
        dataType: "text"
    }).done(function(res) {
        $(row).html(res);
        $(row + " .selectize").selectize();

        attachRefreshListener($(row + " .loci-select"), row);
        attachRemoveListener($(row + " .remove-allele-button"));
    }).fail(function(jqXHR, textStatus, errorThrown) {
        alert("AJAX call failed: " + textStatus + ", " + errorThrown);
    });
}
</script>
@endsection
