<td>{!! Form::select('loci_ids[]', $locis->pluck('name', 'id'), $loci->id, ['class' => 'form-control loci-select', 'placeholder' => 'Select Loci']) !!}</td>
<td class="allele-row-select">
    @if ($loci->id)
        @if ($loci->type == "gene")
                <div class="input-group">
                    {!! Form::select('allele_left_ids[]', $loci->alleles->pluck('name', 'id'), 0, ['class' => 'form-control allele-select input-group-prepend', 'placeholder' => 'Select Allele']) !!}
                    {!! Form::select('allele_right_ids[]', $loci->alleles->pluck('name', 'id'), null, ['class' => 'form-control allele-select input-group-append', 'placeholder' => 'Select Allele']) !!}
                </div>
                {!! Form::hidden('loci_positions[]') !!}
            @else
            {!! Form::number('loci_positions[]', 1, [ 'class' => 'form-control', 'min' => 0, 'max' => $loci->length ]) !!}
            {!! Form::hidden('allele_left_ids[]') !!}
            {!! Form::hidden('allele_right_ids[]') !!}
        @endif
    @endif
</td>
<td class="text-right"><a href="#" class="btn btn-danger remove-allele-button">Remove</a></td>
