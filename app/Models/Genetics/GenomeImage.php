<?php

namespace App\Models\Genetics;

use Config;
use DB;
use App\Models\Model;
use Illuminate\Validation\Rule;

class GenomeImage extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sort', 'name', 'description', 'parsed_description', 'is_visible',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'genome_images';

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the allele combinations this image belongs to.
     */
    public function alleles()
    {
        return $this->belongsToMany(LociAllele::class, 'image_alleles', 'image_id', 'allele_id')->withPivot('position');
    }

    /**********************************************************************************************

        SCOPES

    **********************************************************************************************/

    /**
     * Scope a query to sort by sort order.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSort($query)
    {
        return $query->orderBy('sort');
    }

    /**
     * Scope a query to only include visible.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVisible($query)
    {
        return $query->where('is_visible', 1);
    }

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/

    /**
     * Gets the file directory containing the model's image.
     *
     * @return string
     */
    public function getImageDirectoryAttribute()
    {
        return 'images/data/genomes';
    }

    /**
     * Gets the file name of the model's image.
     *
     * @return string
     */
    public function getImageFileNameAttribute()
    {
        return $this->id . '-image.png';
    }

    /**
     * Gets the path to the file directory containing the model's image.
     *
     * @return string
     */
    public function getImagePathAttribute()
    {
        return public_path($this->imageDirectory);
    }
    
    /**
     * Gets the URL of the model's image.
     *
     * @return string
     */
    public function getImageUrlAttribute()
    {
        return asset($this->imageDirectory . '/' . $this->shopImageFileName);
    }
}
