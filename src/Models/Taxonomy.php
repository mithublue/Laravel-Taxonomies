<?php namespace Lecturize\Taxonomies\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Taxonomy
 * @package Lecturize\Taxonomies\Models
 */
class Taxonomy extends Model
{
	use SoftDeletes;

	/**
	 * @inheritdoc
	 */
	protected $fillable = [
		'term_id',
		'taxonomy',
		'desc',
		'parent',
		'sort',
	];

	/**
	 * @inheritdoc
	 */
	protected $dates = ['deleted_at'];

    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct();

        $this->table = config('lecturize.taxonomies.table_taxonomies', 'taxonomies');
    }

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function term() {
		return $this->belongsTo(Term::class);
	}

	/**
     * An example for a related posts model
	 * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
	 */
	public function posts()
	{
		return $this->morphedByMany('App\Models\Posts\Post', 'taxable', 'taxables');
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function parent()
	{
		return $this->belongsTo(Taxonomy::class, 'parent');
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function children()
	{
		return $this->hasMany(Taxonomy::class, 'parent');
	}

	/**
	 * @param  object  $query
	 * @param  string  $taxonomy
	 * @return mixed
	 */
	public function scopeTaxonomy($query, $taxonomy)
	{
		return $query->where('taxonomy', $taxonomy);
	}

	/**
	 * @param  object  $query
	 * @param  string  $term
	 * @param  string  $taxonomy
	 * @return mixed
	 */
	public function scopeTerm($query, $term, $taxonomy = 'major')
	{
		return $query->whereHas('term', function($q) use($term, $taxonomy) {
			$q->where('name', $term);
		});
	}

	/**
	 * @param  object  $query
	 * @param  string  $searchTerm
	 * @param  string  $taxonomy
	 * @return mixed
	 */
	public function scopeSearch($query, $searchTerm, $taxonomy = 'major')
	{
		return $query->whereHas('term', function($q) use($searchTerm, $taxonomy) {
			$q->where('name', 'like', '%'. $searchTerm .'%');
		});
	}

}