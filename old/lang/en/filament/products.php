<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — ProductResource translation strings
|--------------------------------------------------------------------------
|
| Labels and filter copy for the Products resource at /admin/products.
| Consumed via __('filament/products.<key>').
|
*/

return [

    // ─── Model labels ──────────────────────────────────────────────────
    'model_label'                       => 'Product',
    'plural_model_label'                => 'Products',

    // ─── Navigation ───────────────────────────────────────────────────
    'nav_label'                         => 'Products',

    // ─── Form ──────────────────────────────────────────────────────────
    'sku'                               => 'SKU',
    'active'                            => 'Active',

    // ─── Filters ───────────────────────────────────────────────────────
    'filter_label_category'             => 'Category',
    'active_only'                       => 'Active only',
    'inactive_only'                     => 'Inactive only',

    // ─── Deal items relation manager ───────────────────────────────────
    'deal_items_relation_title'         => 'Deal Items',
    'col_lead'                          => 'Lead',
    'col_unit_price'                    => 'Unit Price',

    // ─── Form field labels ─────────────────────────────────────────────
    'field_name_label'                  => 'Name',
    'field_category_label'              => 'Category',
    'field_description_label'           => 'Description',
    'field_price_label'                 => 'Price',
    'field_currency_label'              => 'Currency',

    // ─── Table column labels ───────────────────────────────────────────
    'col_name'                          => 'Name',
    'col_price'                         => 'Price',
    'col_category'                      => 'Category',
    'col_created_at'                    => 'Created',

    // ─── Deal items relation column labels ─────────────────────────────
    'col_quantity'                      => 'Quantity',
    'col_total'                         => 'Total',
    'col_deal_item_created_at'          => 'Created',
];
