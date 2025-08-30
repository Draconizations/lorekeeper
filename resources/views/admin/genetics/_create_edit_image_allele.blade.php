<td>{!! Form::select('loci_ids[]', $locis->pluck('name', 'id'), $loci_id, ['class' => 'form-control loci-select', 'placeholder' => 'Select Loci']) !!}</td>
<td class="allele-row-select">
    @if ($loci_id)
        {!! Form::select('allele_ids[]', $allele_list, $allele_ids, ['class' => 'form-control allele-select', 'placeholder' => 'Select Allele']) !!}
    @endif
</td>
<td>
    @if ($loci_id)
        {!! Form::number('allele_positions[]', 1, ['class' => 'form-control']) !!}
    @endif
</td>
<td class="text-right"><a href="#" class="btn btn-danger remove-allele-button">Remove</a></td>
