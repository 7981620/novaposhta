<div>

    <style>
        @-webkit-keyframes loader-1 {
            0% {
                -webkit-transform: rotate(0deg);
                transform: rotate(0deg);
            }
            100% {
                -webkit-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }

        @keyframes loader-1 {
            0% {
                -webkit-transform: rotate(0deg);
                transform: rotate(0deg);
            }
            100% {
                -webkit-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }

        .loader {
            height: 8px;
            width: 36px;
        }

        .loader-box {
            display: inline-block;
            height: 8px;
            width: 8px;
            margin: 0 2px;
            background-color: rgb(0, 146, 255);
            animation-name: fadeOutIn;
            animation-duration: 500ms;
            animation-iteration-count: infinite;
            animation-direction: alternate;
        }

        .loader-box:nth-child(1) {
            animation-delay: 250ms;
        }

        /* (1/2) * <animation-duration: 500ms */
        .loader-box:nth-child(2) {
            animation-delay: 500ms;
        }

        /* (2/2) * <animation-duration: 500ms */
        .loader-box:nth-child(3) {
            animation-delay: 750ms;
        }

        /* (3/2) * <animation-duration: 500ms */

        @keyframes fadeOutIn {
            0% {
                background-color: rgba(0, 146, 255, 1);
            }
            100% {
                background-color: rgba(0, 146, 255, 0);
            }
        }
    </style>
    {{--
        <form action="" method="POST">
        @csrf
    --}}
    <div>
        <select wire:model="selectedRegion" name="np_region_id" class="select-novaposhta">
            <option value="" selected>выберите область</option>
            @foreach($regions as $region)
                <option value="{{ $region->id }}">{{ $region->description_ru }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <select wire:model="selectedCity" name="np_city_id" class="select-novaposhta" @if(is_null($selectedRegion)) disabled @endif>
            <option value="" selected>выберите город</option>
            @foreach($cities as $city)
                <option value="{{ $city->id }}">{{ $city->type_ru }} {{ $city->description_ru }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <select wire:model="selectedWarehouse" name="np_warehouse_id" class="select-novaposhta" @if(is_null($selectedCity)) disabled @endif>
            <option value="" selected>выберите отделение</option>
            @foreach($warehouses as $warehouse)
                <option value="{{ $warehouse->id }}">{{ $warehouse->description_ru }}</option>
            @endforeach
        </select>
    </div>

    <div wire:loading wire:loading.delay>
        <span class="loader">
            <span class="loader-box"></span>
            <span class="loader-box"></span>
            <span class="loader-box"></span>
        </span>
    </div>

    {{--

        @if(!is_null($selectedWarehouse))
            <button type="submit">Отправить</button>
        @endif
        </form>

    --}}

</div>
