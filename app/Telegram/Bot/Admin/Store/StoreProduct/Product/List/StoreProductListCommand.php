<?php

namespace App\Telegram\Bot\Admin\Store\StoreProduct\Product\List;


use App\Telegram\CommandStepByStep;
use Hekmatinasser\Verta\Verta;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Keyboard\Keyboard;

class StoreProductListCommand extends CommandStepByStep
{
    protected string $name = 'store_product_list';

    public $user;

    public function __construct()
    {
        $this->setCheckUserActive(true);
        $this->user = auth()->user();
    }

    public function handle()
    {
        $value_data = explode(' ', $this->update->callbackQuery->data)[1] ?? 1;
        $store = $this->user->store()->first();
        $products = $store->products()->paginate(PAGINATION_LISTS_PRODUCT, ['*'], 'page', $value_data);

        $txt = [
            emoji('shopping_bags ') . 'لیست محصولات (' . $products->count() . ')',
            '',
        ];

        if ($products->count()) {
            $collect_data = [];
            $last_page = $products->lastPage();
            $current_page = $products->currentPage();
            $total = $products->total();
            $i = get_num_row_paginate($current_page, PAGINATION_LISTS_PRODUCT);

            // collect data
            foreach ($products as $product) {
                $makeItems = [];
                $items = $product->items()->get();
                if($items->count()){
                    foreach ($items as $item) {
                        $quantity = $item->quantity == null ? 'نامحدود' : $item->quantity . 'عدد';
                        $makeItems[] = join_text([
                            emoji('white_small_square ') . '<b>'.$item->title.'</b>',
                            '<b>قیمت:</b> ' . $item->price .' تومان',
                            '<b>موجودی:</b> ' . $quantity,
                            '<b>موجود میباشد:</b> ' . ($item->in_stock ? 'بله' : 'خیر'),
                            ''
                        ]);
                    }
                }

                $collect_data[] = [
                    'keyboard' => Keyboard::make()->inline()->row([
                        Keyboard::inlineButton(['text' => emoji('x ') . 'حذف', 'callback_data' => 'c_store_product_remove '. $product->id_product]),
                    ])->row([
                        Keyboard::inlineButton(['text' => emoji('pencil2 ') . 'ویرایش نام', 'callback_data' => 'c_store_product_edit '. $product->id_product])
                    ])->row([
                        Keyboard::inlineButton(['text' => emoji('memo ') . 'ویرایش آیتم ها', 'callback_data' => 'c_store_product_edit '. $product->id_product])
                    ]),
                    'text' => join_text([
                        emoji('pushpin ') . '<b>' . $i++ . ' - نام: </b>' . $product->title,
                        'آیتم ها: ' . $items->count() . 'مورد',
                        join_text($makeItems),
                        '<b>ساخته شده در: </b> ' . Verta::instance($product->created_at)->format(FORMAT_DATE_TIME),
                    ]),
                ];
            }

            // send each item separately
            foreach ($collect_data as $item) {
                // send reply
                $this->replyWithMessage([
                    'text' => $item['text'],
                    'reply_markup' => $item['keyboard'],
                    'parse_mode' => 'HTML'
                ]);
            }

            // make pagination
            if ($last_page > 1) {

                $keyboards = Keyboard::make()->inline();
                $collect_keyboards = [];
                for ($i = 1; $i <= $last_page; $i++) {
                    $collect_keyboards[] = Keyboard::inlineButton(['text' => ($current_page == $i ? emoji('heavy_check_mark ') : '') . "$i", 'callback_data' => "c_store_product_list $i"]);
                    if($i > 1 && 5 % $i == 0){
                        $keyboards->row($collect_keyboards);
                        $collect_keyboards = [];
                    }
                }
                if(!empty($collect_keyboards))
                    $keyboards->row($collect_keyboards);

                $this->replyWithMessage([
                    'text' => join_text([
                        emoji('1234 ') . 'صفحه بندی محصولات',
                        "کل موارد: <b>$total</b> مورد" . ' - ' . "(صفحه <b>$current_page</b> از <b>$last_page</b>)"
                    ]),
                    'reply_markup' => $keyboards,
                    'parse_mode' => 'HTML'
                ]);
            }

        } else {
            $txt[] = 'شما هیچ محصولی ندارید';
            $this->replyWithMessage([
                'text' => join_text($txt),
                'parse_mode' => 'HTML'
            ]);
        }
    }

    public function actionBeforeMake()
    {
        $this->removeCache();
    }

    function nextSteps(): array
    {
        return [];
    }
}
