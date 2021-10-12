<?php

// Типы "отделений"
$NORMAL = '841339c7-591a-42e2-8233-7a0a00f0ed6f'; # Почтовое отделение
$SHOP = '6f8c7162-4b72-4b0a-88e5-906948c6a92f'; # Отделение в магазине
$CARGO = '9a68df70-0267-42a8-bb5c-37f427e36ee4'; # Грузовое отделение
$POSTOMAT = '95dc212d-479c-4ffb-a8ab-8c1b9073d0bc'; # Почтомат
$POSTOMAT_PB = 'f9316480-5f2d-425d-bc2c-ac7cd29decf0'; # Почтомат ПриватБанк


return [
    'chunk_size' => env('AGENTA_NP_CHUNK_SIZE', 100),

// доступными делать только типы:
    'allowed_warehouse_type' => [$NORMAL, $SHOP, $CARGO],
//    'allowed_warehouse_type' => [$NORMAL, $SHOP, $CARGO],

// импортировать в базу данных только эти типы:
    'import_warehouse_type' => [$NORMAL, $SHOP, $CARGO],

// это параметры для внутренних процедур, не меняйте их
    'type_normal' => $NORMAL,
    'type_shop' => $SHOP,
    'type_cargo' => $CARGO,
    'type_postomat' => $POSTOMAT,
    'type_postomat_pb' => $POSTOMAT_PB,


];
