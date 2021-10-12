<?php

namespace Agenta\AgentaNovaPoshta\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;


/**
 * App\Models\NovaPoshtaCity
 *
 * @property int $id
 * @property string $ref
 * @property string $area
 * @property int $city_id
 * @property string $description_ru
 * @property string $description_uk
 * @property string $area_description_ru
 * @property string $area_description_uk
 * @property string $type_ru
 * @property string $type_uk
 * @property-read NovaPoshtaRegion $region
 * @property-read Collection|NovaPoshtaWarehouse[] $warehouses
 * @property-read int|null $warehouses_count
 * @method static \Illuminate\Database\Eloquent\Builder|NovaPoshtaCity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NovaPoshtaCity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NovaPoshtaCity query()
 * @method static \Illuminate\Database\Eloquent\Builder|NovaPoshtaCity whereArea($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NovaPoshtaCity whereAreaDescriptionRu($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NovaPoshtaCity whereAreaDescriptionUk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NovaPoshtaCity whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NovaPoshtaCity whereDescriptionRu($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NovaPoshtaCity whereDescriptionUk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NovaPoshtaCity whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NovaPoshtaCity whereRef($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NovaPoshtaCity whereTypeRu($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NovaPoshtaCity whereTypeUk($value)
 * @mixin \Eloquent
 */
class NovaPoshtaCity extends Model
{

    protected $guarded = [];
    public $timestamps = false;

    /**
     * Область населенного пункта
     *
     * @return BelongsTo
     */
    public function region(): BelongsTo
    {
        return $this->belongsTo(NovaPoshtaRegion::class, 'area', 'ref');
    }

    /**
     * Все работающие отделения в населенном пункте
     *
     * @return HasMany
     */
    public function warehouses(): HasMany
    {
        return $this->hasMany(
            NovaPoshtaWarehouse::class, 'city_ref', 'ref')
            ->where('active', true);
    }

    /**
     * Только допустимые типы отделений в населенном пункте (из работающих)
     *
     * @return HasMany
     */
    public function warehousesAllowed(): HasMany
    {

        return $this
            ->warehouses()
            ->whereIn('type_ref', config('agentanovaposhta.allowed_warehouse_type'));
    }


    public function warehousesCargoOnly() {
        return $this
            ->warehouses()
            ->where('type_ref', config('agentanovaposhta.type_cargo'));
    }

    /**
     * Сокращает тип населенного пункта (русский)
     *
     * @param $value
     * @return string
     */
    public function getTypeRuAttribute($value): string
    {
        if (Str::lower($value) === 'поселок городского типа') {
            return 'пгт. ';
        }
        return Str::lower(Str::limit($value, 1, '. '));

    }

    /**
     * Сокращает тип населенного пункта (украинский)
     *
     * @param $value
     * @return string
     */
    public function getTypeUkAttribute($value): string
    {
        if (Str::lower($value) === 'селище міського типу') {
            return 'смт. ';
        }
        return Str::lower(Str::limit($value, 1, '. '));

    }


}
