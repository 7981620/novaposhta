<?php

namespace Agenta\AgentaNovaPoshta\Console\Commands\NovaPoshta;

use Agenta\AgentaNovaPoshta\Models\NovaPoshtaCity;
use Agenta\AgentaNovaPoshta\Models\NovaPoshtaWarehouse;
use Illuminate\Console\Command;
use Daaner\NovaPoshta\Models\Address;
use Illuminate\Support\Facades\Log;
use function React\Promise\all;

class UpdateWarehousesCommand extends Command
{
    protected $signature = 'np:update_warehouses';
    protected $description = 'Новая Почта - обновление справочника отделений';
    protected $resCount;
    protected $warehouses;
    protected $import_types;
    protected $allowed_types;

    public function handle(): void
    {

        if (config('novaposhta.api_key') === 'api_key' | !config('novaposhta.api_key')) {
            $this->error('Не указан ключ API Новой Почты (добавьте в .env NP_API_KEY=...');
            return;
        }

        $this->resCount = config('agentanovaposhta.chunk_size');
        $this->import_types = config('agentanovaposhta.import_warehouse_type');
        $this->allowed_types = config('agentanovaposhta.allowed_warehouse_type');


        $this->info('Загрузка населенных пунктов...');
        $cities = NovaPoshtaCity::orderBy('area')->lazy(100);
        if ($cities->isEmpty()) {
            $this->warn('...нет населенных пунктов, сначала выполните импорт командой np:import_cities');
            return;
        }
        $citiesCount = $cities->count();
        $this->info('Населенных пунктов - '.$citiesCount);
        $this->info('Начинаю получение данных...');
        $novaPoshta = new Address;


        $spinner = $this->spinner($citiesCount);
        $spinner->start();


        foreach ($cities as $key => $city) {

            $spinner->setMessage('населенный пункт ' . $key." из ".$citiesCount."\t".$city->area_description_ru." обл., ".$city->type_ru." ".$city->description_ru);
            $spinner->advance();

            $this->warehouses = null;
            $cityRef = $city->ref;

            $novaPoshta->setLimit($this->resCount);
            $novaPoshta->setPage(1);

            //получаем данные

            retry(5, function () use ($novaPoshta, $cityRef) {
                try {
                    $this->warehouses = $novaPoshta->getWarehouses($cityRef, false);
                } catch (\Exception $e) {
                    Log::error($e->getMessage());
                }
            }, 5000, function ($exception) {
                $this->error('retry Exception');
                Log::warning('Повтор обращения к API');
            });


            if (isset($this->warehouses['success']) && $this->warehouses['success'] === true && isset($this->warehouses['info']['totalCount'])) {

                $totalWarehouses = $this->warehouses['info']['totalCount'];
                $pagesTotal = (int) ceil($totalWarehouses / $this->resCount);

                //Одна страница результатов
                if ($pagesTotal === 1 && !empty($this->warehouses)) {
                    $data = collect($this->warehouses['result']);
                    if ($data->isNotEmpty()) {
                        $this->proceedWarehouse($data);
                    }
                }

                //Рельзтатов на несколько страниц
                if ($pagesTotal > 1) {

                    //обновляем результаты первой страницы
                    $data = collect($this->warehouses['result']);
                    if ($data->isNotEmpty()) {
                        $this->proceedWarehouse($data);
                    }

                    //обрабатываем результаты остальных страниц
                    for ($pageCurrent = 2; $pageCurrent <= $pagesTotal; $pageCurrent++) {

                        $novaPoshta->setLimit($this->resCount);
                        $novaPoshta->setPage($pageCurrent);

                        //получаем данные
                        retry(
                            5, function () use ($novaPoshta, $cityRef) {
                            try {
                                $this->warehouses = $novaPoshta->getWarehouses($cityRef, false);
                            } catch (\Exception $e) {
                                Log::error($e->getMessage());
                            }
                        }, 5000, function ($exception) {
                            $this->error('retry Exception');
                            Log::warning('Повтор обращения к API');
                        }
                        );

                        $data = collect($this->warehouses['result']);
                        if ($data->isNotEmpty()) {
                            $this->proceedWarehouse($data);
                        }

                    }

                }


            }

        }

        $spinner->finish();

    }

    /**
     * Обновление моделей
     *
     * @param $data
     */
    private function proceedWarehouse($data): void
    {
        foreach ($data as $item) {

            $typeRef = $item['TypeOfWarehouse'];

            // проверка, что нужно такой тип импортировать
            // если нет, и такой есть в базе - он будет удален
            if (!in_array($typeRef, $this->import_types, true)) {
                if ($deleteWarehouse = NovaPoshtaWarehouse::whereRef($item['Ref'])->first()) {
                    $deleteWarehouse->delete();
                }
            }

            $active = false;
            if ($item['WarehouseStatus'] === 'Working') {
                $active = true;
            }


            $data = [
                'type_ref' => $typeRef,
                'city_ref' => $item['CityRef'],
                'description_ru' => $item['DescriptionRu'],
                'description_uk' => $item['Description'],
                'short_address_ru' => $item['ShortAddressRu'],
                'short_address_uk' => $item['ShortAddress'],
                'number' => $item['Number'],
                'city_uk' => $item['CityDescription'],
                'city_ru' => $item['CityDescriptionRu'],
                'latitude' => $item['Latitude'],
                'longitude' => $item['Longitude'],
                'pos_terminal' => (int) $item['POSTerminal'],
                'max_weight' => $item['TotalMaxWeightAllowed'],
                'region' => $item['SettlementAreaDescription'],
                'city_type_ru' => $item['SettlementTypeDescriptionRu'],
                'city_type_uk' => $item['SettlementTypeDescription'],
                'phone' => $item['Phone'],
                'active' => $active,
            ];

            if($exist = NovaPoshtaWarehouse::where('ref', $item['Ref'])->first()) {

                //update warehouse
                try {
                    $exist->update($data);
                } catch (\Exception $exception) {
                    Log::error('NovaPoshta warehouses update: '.$exception->getMessage());
                    $this->error($exception->getMessage());
                    continue;
                }

    
            } else {
                //create new Warehouse
                $data['ref'] = $item['Ref'];
                try {
                    NovaPoshtaWarehouse::create($data);
                } catch (\Exception $exception) {
                    Log::error('NovaPoshta warehouses create: '.$exception->getMessage());
                    $this->error($exception->getMessage());
                    continue;
                }
            }

        }

    }


}
