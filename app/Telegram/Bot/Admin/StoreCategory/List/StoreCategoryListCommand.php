<?php

namespace App\Telegram\Bot\Admin\StoreCategory\List;

use App\Http\Controllers\API\StoreController;
use App\Telegram\CommandStepByStep;
use Hekmatinasser\Verta\Verta;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

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
        $store = $this->user->store()->first();
        $categories = $store->categories()->get();

        $txt = [
            emoji('page_facing_up ') . 'لیست دسته بندی ها ('.$categories->count().')',
            '',
        ];

        if($categories->count()){
            $i = 1;
            foreach ($categories as $category){

                $link_edit = '<a href="tg://resolve?domain='.env("TELEGRAM_SHOPTELY_ADMIN_ID").'&store_category_edit=/'.$category->id_category.'">ویرایش</a>';
//                $link_edit = '<a href="https://t.me/shoptelyadmin_bot?start=store_category_edit%3D%2F5">ویرایش</a>';
//                $link_edit = '<a href="tg://msg?to=@shoptelyadmin_bot&text=/store_category_edit%20555">ویرایش</a>';
                $link_edit = '/store_category_edit_'. $category->id_category;
//                $link_edit = '<a href="tg://msg?to=83524826&text=/store_category_edit%20564">ویرایش</a>';
//                $link_edit = '<a href="https://t.me/shoptelyadmin_bot?text=/get_news">ویرایش</a>';



                $message = 'Hello, here is a link: https://www.example.com';
                $url_pattern = '/https?:\/\/[\w\-\.\/]+/';
                preg_match_all($url_pattern, $message, $matches);
                foreach ($matches[0] as $match) {
                    $encoded_link = base64_encode($match);
                    $message = str_replace($match, $encoded_link, $message);
                }

                $txt[] = join_text([
                    '<b>'.$i++.' - نام: </b>' . $category->name,
                    $link_edit,
                    '<b>ساخته شده در: </b> ' . Verta::instance($category->created_at)->format(FORMAT_DATE_TIME),
                    ''
                ]);
            }
        }else{
            $txt[] = 'شما هیچ دسته بندی ندارید';
        }

        $this->replyWithMessage([
            'text' => join_text($txt),
            'parse_mode' => 'HTML'
        ]);
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
