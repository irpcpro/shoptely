<?php

namespace App\Telegram\Bot\Admin\Store\StoreProduct\Product\Add\Category;

use App\Telegram\CommandStepByStep;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Keyboard\Keyboard;

class StoreProductCategoryAddCommand extends CommandStepByStep
{

    protected string $name = 'store_product_category_add';

    public $user;

    public function __construct()
    {
        $this->setCheckUserActive(true);
        $this->user = auth()->user();
    }

    public function handle()
    {
        // get id_product
        $get_cache = Cache::get($this->update->getChat()->id);
        if (!empty($get_cache) && $get_cache['id_product']) {
            $product = $this->user->store()->first()->products()->where('id_product', $get_cache['id_product']);
            if ($product->exists()) {
                $product = $product->first();

                $text = join_text([
                    emoji('pushpin ') . 'یک دسته بندی را انتخاب کنید:',
                ]);

                // make category list buttons
                $keyboard = Keyboard::make()->inline();
                $categories = $this->user->store()->first()->categories()->get();
                if ($categories->count()) {
                    foreach ($categories->chunk(2) as $cats) {
                        $collect_cat = [];
                        foreach ($cats as $cat) {
                            $collect_cat[] = Keyboard::inlineButton(['text' => $cat->name, 'callback_data' => 'c_store_product_category_set ' . $cat->id_category]);
                        }
                        $keyboard->row($collect_cat);
                    }
                }

                $this->replyWithMessage([
                    'text' => $text,
                    'reply_markup' => $keyboard,
                    'parse_mode' => 'HTML',
                ]);

                // set extra data for caching
                $this->setExtraCacheData([
                    'id_product' => $product->id_product
                ]);

                $this->setShouldCacheNextStep(true);

            } else {
                $this->replyWithMessage([
                    'text' => 'محصولی با این شناسه یافت نشد',
                ]);
                Log::error('ERROR:: user tries to get id_product which is not for himself in category 1', [
                    'chat_id' => $this->update->getMessage()->chat->id,
                    'id_product' => $get_cache['id_product'],
                ]);
            }
        } else {
            $this->replyWithMessage([
                'text' => join_text([
                    emoji('exclamation ') . 'خطایی رخ داده است.',
                    'دوباره تلاش کنید',
                ])
            ]);
            Log::error('ERROR:: user tries to get id_product which is not for himself in category 2', [
                'chat_id' => $this->update->getMessage()->chat->id,
                'id_product' => $get_cache['id_product'],
            ]);
            return true;
        }
    }

    public function actionBeforeMake()
    {
        //
    }

    function nextSteps(): array
    {
        return [
            StoreProductCategorySetCommand::class
        ];
    }
}
