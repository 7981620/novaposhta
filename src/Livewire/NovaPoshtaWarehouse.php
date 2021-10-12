<?php

namespace Agenta\AgentaNovaPoshta\Livewire;

use Agenta\AgentaNovaPoshta\Models\NovaPoshtaCity;
use Agenta\AgentaNovaPoshta\Models\NovaPoshtaRegion;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class NovaPoshtaWarehouse extends Component
{

    public $regions;
    public $cities;
    public $warehouses;
    public $selectedRegion = null;
    public $selectedCity = null;
    public $selectedWarehouse = null;
    public $cargo = false;

    protected $rules = [
        'selectedRegion' => 'required|int|exists:novaposhta_regions,id',
        'selectedCity' => 'required|int|exists:novaposhta_cities,id',
        'selectedWarehouse' => 'required|int|exists:novaposhta_warehouses,id',
    ];


    /**
     * Загрузка параметров моделей при инициализации
     *
     * @param  null  $selectedCity
     * @param  null  $selectedWarehouse
     */
    public function mount($selectedRegion = null, $selectedCity = null, $selectedWarehouse = null): void
    {

        $this->regions = NovaPoshtaRegion::where('active', true)->get()->sortBy('description_ru');

        if (!is_null($selectedRegion) | !is_null($selectedCity) | !is_null($selectedWarehouse) ) {

            if(!is_null($selectedWarehouse)) {

                //было выбрано отделение
                $warehouse = \Agenta\AgentaNovaPoshta\Models\NovaPoshtaWarehouse::find($selectedWarehouse);
                $this->selectedCity = $warehouse->city->id;
                $this->selectedRegion = $warehouse->city->region->id;

                if($this->cargo) {
                    $this->cities = $warehouse->city->region->citiesWithWarehousesCargoOnly->sortBy('type_ru');
                } else {
                    $this->cities = $warehouse->city->region->citiesWithWarehouses->sortBy('type_ru');
                }

                if($this->cargo) {
                    $this->warehouses = $warehouse->city->warehousesCargoOnly;
                } else {
                    $this->warehouses = $warehouse->city->warehousesAllowed;
                }


            } elseif(!is_null($selectedCity)) {

                //выбран город
                $city = NovaPoshtaCity::find($selectedCity);

                $this->selectedRegion = $city->region->id;
                if($this->cargo) {
                    $this->cities = $city->region->citiesWithWarehousesCargoOnly->sortBy('type_ru');
                } else {
                    $this->cities = $city->region->citiesWithWarehouses->sortBy('type_ru');
                }

                if($this->cargo) {
                    $this->warehouses = $city->warehousesCargoOnly;
                } else {
                    $this->warehouses = $city->warehousesAllowed;
                }


            } else {
                //только область
                $region = NovaPoshtaRegion::find($selectedRegion);
                if($this->cargo) {
                    $this->cities = $region->citiesWithWarehousesCargoOnly->sortBy('type_ru');
                } else {
                    $this->cities = $region->citiesWithWarehouses->sortBy('type_ru');
                }
                $this->warehouses = collect();
            }


        } else {

            //нет старых значений

            $this->cities = collect();
            $this->warehouses = collect();

            $this->selectedRegion = $selectedRegion;
            $this->selectedCity = $selectedCity;
            $this->selectedWarehouse = $selectedWarehouse;

        }


    }

    /**
     * @return Application|Factory|View
     */
    public function render()
    {
        return view('agentanovaposhta::livewire.nova-poshta-warehouse');
    }

    /**
     * Выбрали область
     *
     * @param $region
     */
    public function updatedSelectedRegion($region): void
    {
        if (!is_null($region) && $region) {

            if($this->cargo) {
                $this->cities = NovaPoshtaRegion::find($region)->citiesWithWarehousesCargoOnly->sortBy('type_ru');
            } else {
                $this->cities = NovaPoshtaRegion::find($region)->citiesWithWarehouses->sortBy('type_ru');
            }

            $this->selectedWarehouse = null;
            $this->warehouses = collect();

        } else {

            $this->selectedRegion = null;
            $this->selectedCity = null;
            $this->selectedWarehouse = null;
            $this->warehouses = collect();
            $this->cities = collect();

        }

    }


    /**
     * Выбрали город
     *
     * @param $city
     */
    public function updatedSelectedCity($city): void
    {
        if (!is_null($city) && $city) {
            $city = NovaPoshtaCity::find($city);
            if($this->cargo) {
                $this->warehouses = $city->warehousesCargoOnly;
            } else {
                $this->warehouses = $city->warehousesAllowed;
            }
            $this->selectedWarehouse = null;
        } else {
            $this->selectedWarehouse = null;
            $this->selectedCity = null;
            $this->warehouses = collect();
        }
    }

    /**
     * Выбрали отделение
     *
     * @param $warehouse
     */
    public function updatedSelectedWarehouse($warehouse): void
    {
        if (is_null($warehouse) || !$warehouse) {
            $this->selectedWarehouse = null;
        }
    }

}
