<?php

namespace Agenta\AgentaNovaPoshta\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


/**
 * App\Models\NovaPoshtaRegion
 *
 * @property int $id
 * @property string $ref
 * @property string $areas_center
 * @property string $description_ru
 * @property string $description_uk
 * @property int $active
 * @property-read Collection|NovaPoshtaCity[] $cities
 * @property-read int|null $cities_count
 * @method static Builder|NovaPoshtaRegion newModelQuery()
 * @method static Builder|NovaPoshtaRegion newQuery()
 * @method static Builder|NovaPoshtaRegion query()
 * @method static Builder|NovaPoshtaRegion whereActive($value)
 * @method static Builder|NovaPoshtaRegion whereAreasCenter($value)
 * @method static Builder|NovaPoshtaRegion whereDescriptionRu($value)
 * @method static Builder|NovaPoshtaRegion whereDescriptionUk($value)
 * @method static Builder|NovaPoshtaRegion whereId($value)
 * @method static Builder|NovaPoshtaRegion whereRef($value)
 * @mixin \Eloquent
 */
class NovaPoshtaRegion extends Model
{
    protected $guarded = [];
    public $timestamps = false;

    /**
     * Все населенные пункты в области
     *
     * @return HasMany
     */
    public function cities(): HasMany
    {
        return $this->hasMany(NovaPoshtaCity::class, 'area', 'ref')
            ->where('description_ru', '!=', '')
            ->where('description_uk', '!=', '')
            ;
    }

    /**
     * Все населенные пункты области где есть доступные типы отделений
     *
     * @return Builder|HasMany
     */
    public function citiesWithWarehouses() {
        return $this->cities()->with('warehousesAllowed')->has('warehousesAllowed', '>', 0);
    }

    public function citiesWithWarehousesCargoOnly() {
        return $this->cities()->with('warehousesCargoOnly')->has('warehousesCargoOnly', '>', 0);
    }

}
