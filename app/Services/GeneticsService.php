<?php namespace App\Services;

use App\Models\Character\CharacterGenomeGene;
use App\Models\Character\CharacterGenomeGradient;
use App\Models\Character\CharacterGenomeNumeric;
use App\Services\Service;

use DB;
use Config;

use App\Models\Feature\FeatureCategory;
use App\Models\Feature\Feature;
use App\Models\Genetics\GenomeImage;
use App\Models\Genetics\Loci;
use App\Models\Genetics\LociAllele;
use App\Models\Species\Species;
use App\Models\Species\Subtype;

class GeneticsService extends Service
{
    /**
     * Create a category.
     *
     * @param  array                            $data
     * @param  \App\Models\User\User            $user
     * @return \App\Models\Feature\Loci|bool
     */
    public function createLoci($data, $user)
    {
        DB::beginTransaction();

        try {
            if (!isset($data['name']) || $data['name'] == null || $data['name'] == "") throw new \Exception("Gene groups must have a name.");
            if (!isset($data['type'])) throw new \Exception("Gene groups must have a type.");
            if ( $data['type'] != 'gene'
              && $data['type'] != 'gradient'
              && $data['type'] != 'numeric'
            ) throw new \Exception("Invalid gene type selected.");
            if (!isset($data['length']) || $data['length'] == null || $data['length'] <= 0) throw new \Exception("Must have a length.");
            if ($data['length'] > 255) throw new \Exception("Length must be less than 256.");

            if (!isset($data['chromosome']) || $data['chromosome'] <= 0) $data['chromosome'] = null;
            if (!isset($data['default']) || $data['default'] < 0) $data['default'] = 0;

            if (isset($data['description'])) $data['parsed_description'] = parse($data['description']);
            $data['is_visible'] = isset($data['is_visible']) ? $data['is_visible'] == true : false;

            $loci = Loci::create($data);
            return $this->commitReturn($loci);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Update a category.
     *
     * @param  \App\Models\Feature\Loci         $category
     * @param  array                            $data
     * @param  \App\Models\User\User            $user
     * @return \App\Models\Feature\Loci|bool
     */
    public function updateLoci($category, $data, $user)
    {
        DB::beginTransaction();

        try {
            // Name
            if (!isset($data['name']) || $data['name'] == null || $data['name'] == "") throw new \Exception("Gene groups must have a name.");
            if(Loci::where('name', $data['name'])->where('id', '!=', $category->id)->exists()) throw new \Exception("The name has already been taken.");

            // Type
            if (!isset($data['type'])) $data['type'] = $category->type;
            if ($data['type'] != $category->type) throw new \Exception("Gene group type cannot be changed.");

            // Length
            if (!isset($data['length']) || $data['length'] == null || $data['length'] <= 0) throw new \Exception("Must have a length.");
            if ($data['type'] == "gradient" && $data['length'] > 64) throw new \Exception("Length must be less than 65.");
            if ($data['length'] > 255) throw new \Exception("Length must be less than 256.");

            // Chromosome
            if (!isset($data['chromosome']) || $data['chromosome'] <= 0) $data['chromosome'] = null;

            // Default
            if (!isset($data['default']) || $data['default'] < 0) $data['default'] = 0;

            $data['parsed_description'] = isset($data['description']) ? parse($data['description']) : null;
            $data['is_visible'] = isset($data['is_visible']) ? $data['is_visible'] == true : false;

            // Alleles
            if ($category->type == "gene") {
                // Add New Alleles
                if (isset($data['allele_name'])) foreach($data['allele_name'] as $key => $alleleName) {
                    if($alleleName && $alleleName != "") {
                        $mod = isset($data['modifier'][$key]) ? $data['modifier'][$key] : "";
                        $sum = isset($data['allele_description'][$key]) ? $data['allele_description'][$key] : "";
                        $dom = $data['is_dominant'][$key] == 1;
                        $vis = $data['allele_visibility'][$key] == 1;
                        $allele = LociAllele::create([
                            'loci_id' => $category->id,
                            'is_dominant' => $dom,
                            'name' => $alleleName,
                            'modifier' => $mod == "" ? null : $mod,
                            'summary' => $sum == "" ? null : $sum,
                            'is_visible' => $vis,
                        ]);
                    }
                }

                // Sort Child Alleles
                if (isset($data['allele_sort'])) {
                    $sort = explode(',', $data['allele_sort']);
                    foreach($sort as $index => $s)
                    {
                        $key = $index; //count($sort)-$index-1;
                        $allele = LociAllele::where('id', $s)->first();
                        if (!$allele) throw new \Exception("Trying to edit an allele that does not exist.");
                        if ($allele->loci_id != $category->id) throw new \Exception("Trying to edit an allele that does not belong to this group.");

                        $isDom = $data['edit_allele_dominance'][$key] == 1;
                        $isVis = $data['edit_allele_visibility'][$key] == 1;

                        $name = $data['edit_allele_name'][$key];
                        if (!$name || $name == "") throw new \Exception("Allele names cannot be null.");
                        if (strlen($name) > 5) throw new \Exception("One of the allele names is too long.");

                        $modifier = isset($data['edit_allele_modifier'][$key]) ? $data['edit_allele_modifier'][$key] : "";
                        if (!$modifier) $modifier = "";
                        if (strlen($modifier) > 5) throw new \Exception("One of the allele modifiers is too long.");

                        $summary = $data['edit_allele_description'][$key];
                        if (!$summary) $summary = "";
                        if (strlen($summary) > 255) throw new \Exception("Allele summaries cannot exceed 255 characters in length.");

                        $allele->update([
                            'is_dominant' => $isDom,
                            'sort' => $index,
                            'name' => $name,
                            'modifier' => $modifier == "" ? null : $modifier,
                            'summary' => $summary == "" ? null : $summary,
                            'is_visible' => $isVis,
                        ]);
                    }
                }
            }

            $category->update($data);
            return $this->commitReturn($category);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Sorts category order.
     *
     * @param  array  $data
     * @return bool
     */
    public function sortLoci($data)
    {
        DB::beginTransaction();
        try {
            // explode the sort array and reverse it since the order is inverted
            $sort = array_reverse(explode(',', $data));
            foreach($sort as $key => $s)
            {
                Loci::where('id', $s)->update(['sort' => $key]);
            }
            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Deletes a loci, all instances of characters having those genes, and all the gene's alleles.
     *
     * @param  array  $data
     * @return bool
     */
    public function deleteLoci($loci)
    {
        DB::beginTransaction();
        try {
            $characterGenes = null;
            if ($loci->type == "gene") $characterGenes = CharacterGenomeGene::where("loci_id", $loci->id);
            else if ($loci->type == "gradient") $characterGenes = CharacterGenomeGradient::where("loci_id", $loci->id);
            else if ($loci->type == "numeric") $characterGenes = CharacterGenomeNumeric::where("loci_id", $loci->id);
            else throw new \Exception("Something went wrong, Loci had an impossible type set.");

            if($characterGenes) $characterGenes->delete();
            if ($loci->type == "gene") $loci->alleles()->delete();
            $loci->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Deletes an allele and replaces it with an existing allele from the same locus.
     *
     * @param  array  $data
     * @return bool
     */
    public function deleteLociAllele($data, $loci)
    {
        DB::beginTransaction();
        try {
            if(!$loci) throw new \Exception("Error with Loci.");
            if($loci->type != "gene" || !$loci->alleles->count()) throw new \Exception("This loci doesn't have alleles.");
            if($loci->alleles->count() <= 1) throw new \Exception("You can't delete alleles from a loci with only one allele.");
            $target = LociAllele::find($data['target_allele']);
            if(!$target || $target->loci_id != $loci->id) throw new \Exception("Invalid target.");
            $replacement = LociAllele::find($data['replacement_allele']);
            if(!$replacement || $replacement->loci_id != $loci->id) throw new \Exception("Invalid replacement.");
            if($target->id == $replacement->id) throw new \Exception("Can't replace an allele with itself.");

            CharacterGenomeGene::where('loci_allele_id', $target->id)->update(['loci_allele_id' => $replacement->id]);
            $target->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
    
    /**
     * Creates a genome image
     */
    public function createGenomeImage($data, $user) {
        DB::beginTransaction();

        try {
            $data = $this->fillImageData($data);

            $genomeImage = GenomeImage::create($data);

            $this->attachImageLoci($genomeImage, $data);
            
            if (isset($image) && $image) $this->handleImage($image, $genomeImage->imagePath, $genomeImage->imageFileName);

            return $this->commitReturn($genomeImage);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }


        return $this->rollbackReturn(false);
    }

    public function updateGenomeImage($genomeImage, $data) {
        DB::beginTransaction();

        try {
            $data = $this->fillImageData($data);

            $genomeImage->update($data);

            $genomeImage->locis()->detach();

            $this->attachImageLoci($genomeImage, $data);
            
            if (isset($image) && $image) $this->handleImage($image, $genomeImage->imagePath, $genomeImage->imageFileName);

            return $this->commitReturn($genomeImage);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }


        return $this->rollbackReturn(false);
    }

    private function fillImageData($data) {
        if(isset($data['description']) && $data['description']) $data['parsed_description'] = parse($data['description']);
        $data['is_visible'] = isset($data['is_visible']);

        $image = null;
        if (isset($data['image']) && $data['image']) {
            $image = $data['image'];
            unset($data['image']);
        }

        $usedLoci = [];

        if (isset($data['loci_ids']) && $data['loci_ids']) {
            for ($i = 0; $i < count($data['loci_ids']); $i++) {
                // check if this loci hasn't been set for this image already
                if (isset($usedLoci[$data['loci_ids'][$i]])) {
                    throw new \Exception('Each loci is only allowed to be used once.');
                } else {
                    $usedLoci[$data['loci_ids'][$i]] = true;
                }

                // check if the loci even exists
                $loci = Loci::find($data['loci_ids'][$i]);
                if (!$loci->id) throw new \Exception('Selected loci is invalid.');

                // check if the position doesn't exceed the length
                if ($data['loci_positions'][$i]) {
                    if ($data['loci_positions'][$i] > $loci->length) {
                        throw new \Exception('Loci value may not exceed length.');
                    }
                } else {
                    $left = LociAllele::find($data['allele_left_ids'][$i]);
                    $right = LociAllele::find($data['allele_right_ids'][$i]);

                    if (
                        !$left || $left->loci_id != $loci->id
                        || ($right && $right->loci_id != $loci->id)
                    ) {
                        throw new \Exception('Selected allele is invalid or does not match loci.');
                    }
                }
            }
        } else {
            throw new \Exception('At least one loci association is required.');
        }

        return $data;
    }

    private function attachImageLoci($genomeImage, $data) {
        for ($i = 0; $i < count($data['loci_ids']); $i++) {
            $loci = $data['loci_ids'][$i];
            if ($data['loci_positions'][$i]) {
                $genomeImage->locis()->attach($loci, [ 'position' => $data['loci_positions'][$i]]);
            }
            if ($data['allele_left_ids'][$i]) {
                $genomeImage->locis()->attach($loci, [ 'position' => 0, 'allele_id' => $data['allele_left_ids'][$i]]);
            }
            if ($data['allele_right_ids'][$i]) {
                $genomeImage->locis()->attach($loci, [ 'position' => 1, 'allele_id' => $data['allele_right_ids'][$i]]);
            }
        }
    }
}
