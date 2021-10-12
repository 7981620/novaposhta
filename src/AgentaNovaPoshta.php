<?php

namespace Agenta\AgentaNovaPoshta;

use Agenta\AgentaNovaPoshta\Models\NovaPoshtaRegion;

class AgentaNovaPoshta
{
    /**
     * Список регионов и городов них
     */
    public function getRegionsWithCities()
    {
        return NovaPoshtaRegion::whereActive(true)
            ->with(['cities', 'cities.warehouses'])
            ->get();
    }

    /**
     * Отключает регион
     *
     * @param string $ref
     * @return bool
     */
    public function disableRegion(string $ref): bool
    {
        if ($region = NovaPoshtaRegion::whereRef($ref)->first()) {
            $region->active = false;
            try {
                $region->save();
            } catch (\Exception $e) {
                \Log::error('disable NovaPoshta region ' . $ref . ' - ' . $e->getMessage());
                return false;
            }

            return true;
        }

        return false;

    }

    /**
     * Включает регион
     *
     * @param string $ref
     * @return bool
     */
    public function enableRegion(string $ref): bool
    {
        if ($region = NovaPoshtaRegion::whereRef($ref)->first()) {
            $region->active = true;
            try {
                $region->save();
            } catch (\Exception $e) {
                \Log::error('enable NovaPoshta region ' . $ref . ' - ' . $e->getMessage());
                return false;
            }

            return true;
        }

        return false;

    }


}
