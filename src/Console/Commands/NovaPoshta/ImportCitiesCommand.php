<?php

namespace Agenta\AgentaNovaPoshta\Console\Commands\NovaPoshta;

use Agenta\AgentaNovaPoshta\Models\NovaPoshtaCity;
use Agenta\AgentaNovaPoshta\Models\NovaPoshtaWarehouse;
use Illuminate\Console\Command;
use Daaner\NovaPoshta\Models\Address;

class ImportCitiesCommand extends Command
{
    protected $signature = 'np:import_cities';
    protected $description = 'Новая почта - импорт населенных пунктов в базу данных';

    protected $resCount;

    public function handle()
    {

        if (config('novaposhta.api_key') === 'api_key' | !config('novaposhta.api_key')) {
            $this->error('Не указан ключ API Новой Почты (добавьте в .env NP_API_KEY=...');
            return;
        }

        $this->resCount = config('agentanovaposhta.chunk_size');

        \Artisan::call('np:import_regions');

        $adr = new Address;
        $this->info('Получение информации об объеме данных...');
        $adr->setLimit(1);
        $adr->setPage(1);
        $citiesCount = $adr->getCities();
        if(!isset($citiesCount['info']) | !isset($citiesCount['info']['totalCount'])) {
            Log::info("NP: not get total count of cities");
            $this->warn('not get total count of cities');
            return;
        }
        $totalCities = $citiesCount['info']['totalCount'];
        $pagesTotal = (int) ceil($totalCities / $this->resCount);
        $this->info('Населенных пунктов '.$totalCities . ', страниц для обработки - ' . $pagesTotal);

        $spinner = $this->spinner($pagesTotal);
        $spinner->start();

        $this->proceed($citiesCount);

        if ($pagesTotal > 1) {
            for ($pageCurrent = 2; $pageCurrent <= $pagesTotal; $pageCurrent++) {
//                $this->info('Получение страницы ' . $pageCurrent . ' из ' . $pagesTotal);
                $spinner->setMessage('обработка страницы №' . $pageCurrent . ' из ' . $pagesTotal);
                $spinner->advance();
                $adr->setLimit($this->resCount);
                $adr->setPage($pageCurrent);
                $cities = $adr->getCities();
                $this->proceed($cities);
            }
        }
        $spinner->finish();
        $this->info(PHP_EOL.'...завершено');


    }

    /**
     * Обновление моделей
     *
     * @param $cities
     */
    private function proceed($cities): void
    {
        if (!empty($cities)) {
            if ($cities['success'] === true) {
                if ($data = $cities['result']) {

                    foreach ($data as $item) {

                        if ($item['Description']) {
                            try {
                                NovaPoshtaCity::updateOrCreate(
                                    [
                                        'ref' => $item['Ref'],
                                    ],
                                    [
                                        'area' => $item['Area'],
                                        'city_id' => $item['CityID'],
                                        'area_description_uk' => $item['AreaDescription'],
                                        'area_description_ru' => $item['AreaDescriptionRu'],
                                        'description_ru' => $item['DescriptionRu'],
                                        'description_uk' => $item['Description'],
                                        'type_uk' => $item['SettlementTypeDescription'],
                                        'type_ru' => $item['SettlementTypeDescriptionRu'],
                                    ]);
                            } catch (\Exception $exception) {
                                $this->error($exception->getMessage());
                                return;
                            }
                        } else {
//                            $this->warn('[-] пустое название');
                            if ($empty = NovaPoshtaCity::whereRef($item['Ref'])) {
                                NovaPoshtaWarehouse::whereCityRef($item['Ref'])->delete();
                                $empty->delete();
                            }
                        }

//                        $this->info("\t".$item['DescriptionRu']);

                    }

                }

            } else {
//                $this->error('ответ статуса '.$cities['success'].' , пропускаю страницу...');
            }

        } else {
//            $this->warn('[-] нет данных в ответе...');
        }
    }

}
