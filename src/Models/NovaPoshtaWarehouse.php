<?php

namespace Agenta\AgentaNovaPoshta\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
 * App\Models\NovaPoshtaWarehouse
 *
 * @property int $id
 * @property string $ref UUID отделения
 * @property string $type_ref UUID типа отделения (почтомат, отделение, грузовое и пр.)
 * @property string $city_ref
 * @property string $description_ru полное название и адресом
 * @property string $description_uk
 * @property string $short_address_ru краткий адрес
 * @property string $short_address_uk
 * @property string|null $city_ru
 * @property string|null $city_uk
 * @property string|null $region Область
 * @property string|null $city_type_ru
 * @property string|null $city_type_uk
 * @property string|null $longitude
 * @property string|null $latitude
 * @property string $phone Номер телефона
 * @property string $number Номер отделения
 * @property int $max_weight Максимальный вес посылки
 * @property int $pos_terminal POS Терминал в отделении
 * @property int $active Доступно
 * @property-read NovaPoshtaCity $city
 * @method static Builder|NovaPoshtaWarehouse newModelQuery()
 * @method static Builder|NovaPoshtaWarehouse newQuery()
 * @method static Builder|NovaPoshtaWarehouse query()
 * @method static Builder|NovaPoshtaWarehouse whereActive($value)
 * @method static Builder|NovaPoshtaWarehouse whereCityRef($value)
 * @method static Builder|NovaPoshtaWarehouse whereCityRu($value)
 * @method static Builder|NovaPoshtaWarehouse whereCityTypeRu($value)
 * @method static Builder|NovaPoshtaWarehouse whereCityTypeUk($value)
 * @method static Builder|NovaPoshtaWarehouse whereCityUk($value)
 * @method static Builder|NovaPoshtaWarehouse whereDescriptionRu($value)
 * @method static Builder|NovaPoshtaWarehouse whereDescriptionUk($value)
 * @method static Builder|NovaPoshtaWarehouse whereId($value)
 * @method static Builder|NovaPoshtaWarehouse whereLatitude($value)
 * @method static Builder|NovaPoshtaWarehouse whereLongitude($value)
 * @method static Builder|NovaPoshtaWarehouse whereMaxWeight($value)
 * @method static Builder|NovaPoshtaWarehouse whereNumber($value)
 * @method static Builder|NovaPoshtaWarehouse wherePhone($value)
 * @method static Builder|NovaPoshtaWarehouse wherePosTerminal($value)
 * @method static Builder|NovaPoshtaWarehouse whereRef($value)
 * @method static Builder|NovaPoshtaWarehouse whereRegion($value)
 * @method static Builder|NovaPoshtaWarehouse whereShortAddressRu($value)
 * @method static Builder|NovaPoshtaWarehouse whereShortAddressUk($value)
 * @mixin \Eloquent
 */
class NovaPoshtaWarehouse extends Model
{

    protected $guarded = [];
    public $timestamps = false;


    /**
     * Населенный пункт отделения
     *
     * @return BelongsTo
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(NovaPoshtaCity::class, 'city_ref', 'ref');
    }

    public function scopeCargo($query) {
        return $query->where('type_ref', config('agentanovaposhta.type_cargo'));
    }

    public function scopeNormal($query) {
        return $query->where('type_ref', config('agentanovaposhta.type_normal'));
    }

    public function scopePostomat($query) {
        return $query->whereIn('type_ref', [
            config('agentanovaposhta.type_postomat'),
            config('agentanovaposhta.type_postomat_pb'),
        ]);
    }


}
