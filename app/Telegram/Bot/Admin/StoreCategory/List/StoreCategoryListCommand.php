<?php

namespace App\Telegram\Bot\Admin\StoreCategory\List;

use App\Http\Controllers\API\StoreController;
use App\Telegram\CommandStepByStep;
use Hekmatinasser\Verta\Verta;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Telegram\Bot\Keyboard\Keyboard;

class StoreCategoryListCommand extends CommandStepByStep
{

    protected string $name = 'store_category_list';

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
        $categories = $store->categories()->paginate(PAGINATION_LISTS, ['*'], 'page', $value_data);

        $txt = [
            emoji('page_facing_up ') . 'لیست دسته بندی ها (' . $categories->count() . ')',
            '',
        ];

        if ($categories->count()) {
            $collect_data = [];
            $last_page = $categories->lastPage();
            $current_page = $categories->currentPage();
            $total = $categories->total();
            $i = get_num_row_paginate($current_page);

            // collect data
            foreach ($categories as $category) {
                $collect_data[] = [
                    'keyboard' => Keyboard::make()->inline()->row([
                        Keyboard::inlineButton(['text' => emoji('x ') . 'حذف', 'callback_data' => 'c_store_category_remove '. $category->id_category]),
                        Keyboard::inlineButton(['text' => emoji('pencil2 ') . 'ویرایش نام', 'callback_data' => 'c_store_category_edit '. $category->id_category]),
                    ]),
                    'text' => join_text([
                        emoji('pushpin ') . '<b>' . $i++ . ' - نام: </b>' . $category->name,
                        '<b>ساخته شده در: </b> ' . Verta::instance($category->created_at)->format(FORMAT_DATE_TIME),
                        ''
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
                    $collect_keyboards[] = Keyboard::inlineButton(['text' => ($current_page == $i ? emoji('heavy_check_mark ') : '') . "$i", 'callback_data' => "c_store_category_list $i"]);
                    if($i > 1 && 5 % $i == 0){
                        $keyboards->row($collect_keyboards);
                        $collect_keyboards = [];
                    }
                }
                if(!empty($collect_keyboards))
                    $keyboards->row($collect_keyboards);

                $this->replyWithMessage([
                    'text' => join_text([
                        emoji('1234 ') . 'صفحه بندی دسته بندی',
                        "کل موارد: <b>$total</b> مورد" . ' - ' . "(صفحه <b>$current_page</b> از <b>$last_page</b>)"
                    ]),
                    'reply_markup' => $keyboards,
                    'parse_mode' => 'HTML'
                ]);
            }

        } else {
            $txt[] = 'شما هیچ دسته بندی ندارید';
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
