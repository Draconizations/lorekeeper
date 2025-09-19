@extends('admin.layout')

@section('admin-title') Gene Images @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Gene Images' => 'admin/genetics/images', '$image->id' ? 'Edit Image' : 'Create Image' => 'admin/genetics/images/edit']) !!}

<h1>
    {{ $image->id ? 'Edit' : 'Create' }} Gene Image
    <a href="#" class="btn btn-danger float-right delete-image-button">Delete Gene Image</a>
</h1>

<hr />

{!! Form::open(['url' => $image->id ? 'admin/genetics/images/edit/'.$image->id : 'admin/genetics/images/create', 'files' => true]) !!}

<h3>Basic Information</h3>

<div class="row mb-3">
    <div class="col-12 col-md-6">
        <div class="form-group">
            {!! Form::label('Image Name') !!} {!! add_help('Used  as the title for both the genome\'s image card, and the tab button (if there are tabs).') !!}
            {!! Form::text('name', $image->name, ['class' => 'form-control']) !!}
        </div>
    </div>
    <div class="col-12 col-md-6">
        <div class="form-group">
            {!! Form::label('Image Title (Optional)') !!} {!! add_help('If set, is used as the title for the genome\'s image card instead of the image name. Not used for the tab button.') !!}
            {!! Form::text('title', $image->title, ['class' => 'form-control']) !!}
        </div>
    </div>
</div>

@if ($image->id)
<div class="row mb-3">
    <div class="col col-md-6">
        <img class="mw-100" src="{{ $image->imageUrl }}" alt="The current gene image file" />
    </div>
    <div class="col-12 col-md-6">
    @endif
        <div class="form-group">
            {!! Form::label('Image File') !!} {!! add_help('The actual image. This should be representative of the genome below.') !!}
            <div>{!! Form::file('image') !!}</div>
            <div class="text-muted">Recommended size: None (Choose a standard size for all gene images)</div>
        </div>
    @if ($image->id)
    </div>
</div>
@endif

<div class="form-group">
    {!! Form::label('Description (Optional)') !!}
    {!! Form::textarea('description', $image->description, ['class' => 'form-control wysiwyg']) !!}
</div>

<div class="form-group">
    {!! Form::checkbox('is_visible', 1, $image->id ? $image->is_visible : 1, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
    {!! Form::label('is_visible', 'Is Visible', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If turned off, the image will not be visible in the gallery to regular users.') !!}
</div>

<h3>Associated Genomes</h3>
<p>
    You can add each loci that this image demonstrates here.<br/>
    It is possible to create multiple images for the same genome. The image that was <strong>first created</strong> will have its title (or name) used as the title for image card.
</p>

<div class="alert alert-warning">
    You can only enter each loci <b>once</b>. For regular genes, only the left allele is required.
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
        @php
            $rows = 0;
        @endphp
        @foreach($loci_list as $loci)
            <tr id="allele-row-{{ $rows }}">
                <td>{!! Form::select('loci_ids[]', $locis->pluck('name', 'id'), $loci['id'], ['class' => 'form-control loci-select', 'placeholder' => 'Select Loci']) !!}</td>
                <td class="allele-row-select">
                    @if ($loci['type'] == "gene")
                        <div class="input-group">
                            {!! Form::select('allele_left_ids[]', $loci['alleles'], $loci['left'], ['class' => 'form-control allele-select input-group-prepend', 'placeholder' => 'Select Allele']) !!}
                            {!! Form::select('allele_right_ids[]', $loci['alleles'], $loci['right'], ['class' => 'form-control allele-select input-group-append', 'placeholder' => 'Select Allele']) !!}
                        </div>
                        {!! Form::hidden('loci_positions[]') !!}
                    @else
                        {!! Form::number('loci_positions[]', $loci['position'], [ 'class' => 'form-control', 'min' => 0, 'max' => $loci['length'] ]) !!}
                        {!! Form::hidden('allele_left_ids[]') !!}
                        {!! Form::hidden('allele_right_ids[]') !!}
                    @endif
                    </td>
                <td class="text-right"><a href="#" class="btn btn-danger remove-allele-button">Remove</a></td>
            </tr>
            @php
                $rows++;
            @endphp
        @endforeach
    </tbody>
</table>

<div class="text-right">
    {!! Form::submit($image->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
</div>

{!! Form::close() !!}

@if ($image->id)
    @php
        $group = $images->filter(function ($img) use ($image) {
            return $img->genomeString == $image->genomeString;
        })->values();
    @endphp

    <h3 class="mt-3">Preview</h3>
    <div class="card mb-3">
        <div class="card-body">
            @include('world._gene_image_group', [ 'group' => $group ])
        </div>
    </div>
@endif

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
    $('.delete-image-button').on('click', function(e) {
        e.preventDefault();
        loadModal("{{ url('admin/genetics/images/delete') }}/{{ $image->id }}", 'Delete Image');
    });
    
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
