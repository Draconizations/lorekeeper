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
        'sort', 'name','title', 'description', 'parsed_description', 'is_visible',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'genome_images';

    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $createRules = [
        'name' => 'required|between:3,100',
        'description' => 'nullable',
        'image' => 'required|mimes:png',
    ];
    
    /**
     * Validation rules for updating.
     *
     * @var array
     */
    public static $updateRules = [
        'name' => 'required|between:3,100',
        'description' => 'nullable',
        'image' => 'mimes:png',
    ];

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the allele combinations this image belongs to.
     */
    public function locis()
    {
        return $this->belongsToMany(Loci::class, 'image_locis', 'image_id', 'loci_id')->withPivot('position', 'allele_id')->orderBy('locis.sort', 'DESC');
    }

    /**
     * Gets all loci that are associated with this image.
     */
    public function getLoci() {
        $loci = Loci::with('images')->whereHas('images', function ($query) {
                $query->where('image_id', '=', $this->id);
            })->orderBy('sort', 'DESC')->get();
        return $loci;
    }

    /**
     * Gets all the loci associated with this image and parses them into an array.
     */
    public function getLociArray() {
        $locis = $this->getLoci();

        $list = [];

        foreach($locis as $loci) {
            $id = $loci->id;
            if (!array_key_exists($id, $list)) {
                $list[$id] = [
                    'id' => $id,
                    'sort' => $loci->sort,
                    'type' => $loci->type,
                    'alleles' => $loci->alleles->pluck('name', 'id'),
                    'length' => $loci->length,
                    'name'  => $loci->name,
                    'left' => '',
                    'right' => '',
                    'position' => '',
                ];
            }

            foreach($loci->images as $image) {
                if ($image->pivot->image_id !== $this->id) continue;

                $allele = $image->pivot->allele_id;
                $position = $image->pivot->position;
                
                if ($allele && $position == 0) {
                    $list[$id]['left'] = $allele;
                } else if ($allele && $position == 1) {
                    $list[$id]['right'] = $allele;
                } else {
                    $list[$id]['position'] = $position;
                }
            }
        }
        return $list;
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

    /**
     * Gets the sortable genome string for this image.
     * Only used internally, never displayed.
     */
    public function getGenomeStringAttribute() {
        $locis = $this->getLociArray();

        $str = '';
        foreach($locis as $loci) {
            $gene = (1000 - $loci['sort']).'.';

            if ($loci['type'] == 'gene') {
                $left = '-';
                if ($loci['left']) {
                    $l = LociAllele::find($loci['left']);
                    $left = (1000 - $l->sort).'.'.$l->name;
                }
                $right = '-';
                if ($loci['right']) {
                    $r = LociAllele::find($loci['right']);
                    $right = (1000 - $r->sort).'.'.$r->name;
                }
                $gene .= $left.$right;
            } elseif ($loci['type'] == 'gradient') {
                $gene .= $loci['position'];
            } elseif ($loci['type'] == 'numeric') {
                $gene .= $loci['position'];
            }

            $separator = '_';
            $str = $str.$separator.$gene;
        }
        return trim($str);
    }

    /**
     * Get the genome of this image and display it pretty.
     */
    public function getGenomeDisplayAttribute() {
        $locis = $this->getLociArray();

        $display = '';
        foreach($locis as $loci) {
            $divOpen = '<a class="d-inline py-1 text-monospace mr-2" data-toggle="tooltip" style="word-wrap: break-word;" title="'. $loci['name'] .'" href="'.url('world/genetics/gallery/'.$loci['id']).'">';
            $gene = '';

            if ($loci['type'] == 'gene') {
                $left = $loci['left'] ? LociAllele::find($loci['left'])->displayName : '_';
                $right = $loci['right'] ? LociAllele::find($loci['right'])->displayName : '_';
                $gene = $left.$right;
            } elseif ($loci['type'] == 'gradient') {
                $gene = str_pad($gene, $loci['position'], '+');
                $gene = str_pad($gene, $loci['length'], '-');
            } elseif ($loci['type'] == 'numeric') {
                $gene = $loci['position'];
            }

            $divClose = '</a>';
            $display = $display.$divOpen.$gene.$divClose;
        }

        $display = $display."<div class='clearfix'></div>";
        return $display;
    }

    
    /**
     * Sorts a collection of images into groups and paginates them.
     */
    public static function collectImages($images) {
        $image_groups = [];

        foreach ($images as $image) {
            $str = $image->genomeString;
            if (!isset($image_groups[$str])) {
                $image_groups[$str] = [ ];
            }

            array_push($image_groups[$str], $image);
        }

        return collect($image_groups);
    }
}
