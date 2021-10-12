<?php

namespace Agenta\AgentaNovaPoshta\Console\Commands\NovaPoshta;

use Agenta\AgentaNovaPoshta\Models\NovaPoshtaRegion;
use Illuminate\Console\Command;
use Daaner\NovaPoshta\Models\Address;

class ImportRegionsCommand extends Command
{
    protected $signature = 'np:import_regions';

    protected $description = 'Новая почта - импорт регионов в базу данных';

    public function handle()
    {

        if (config('novaposhta.api_key') === 'api_key' | !config('novaposhta.api_key')) {
            $this->error('Не указан ключ API Новой Почты (добавьте в .env NP_API_KEY=...');
            return;
        }
        
        $adr = new Address;
        $this->info('Получение данных...');
        $data = $adr->getAreas();
        $this->info('данные получены, обработка...');
        if ($data) {
            if ($data['success'] === true) {

                $regions = $data['result'];
                foreach ($regions as $item) {
                    try {
                        NovaPoshtaRegion::updateOrCreate(
                            [
                                'ref' => $item['Ref'],
                            ],
                            [
                                'areas_center' => $item['AreasCenter'],
                                'description_ru' => $item['DescriptionRu'],
                                'description_uk' => $item['Description']
                            ]);

                    } catch (\Exception $exception) {
                        $this->error($exception->getMessage());
                        return;
                    }

                    $this->info('[+] ' . $item['Description']);

                }

            } else {
                $this->warn('ответ не успешный');
            }
        }


    }
}
