<?php

namespace App\Telegram\Bot;

use App\Telegram\CommandStepByStep;
use Telegram\Bot\Objects\Update;

class AboutBotCommand extends CommandStepByStep
{

    protected string $name = 'about';

    public function handle()
    {
        $text = join_text([
            emoji('diamonds ').'<b>شاپتلی</b> چیست',
            'با این بات میتونین فروشگاه ساده خودتونو ایجاد کنین و محصولات به همراه عکس و توضیحات اونو داخل بات تلگرام تعریف کنید.',
            'همینطور میتونین برای محصولات خودتون تعداد موجودی و قیمت قرار بدین تا بتونین اونارو بفروشین.',
            '',
            emoji('diamonds ').'<b>فروش در این بات به چه صورت میباشد؟</b>',
            'به صورت کلی شما میتونین شماره کارت و اطلاعات خودتون رو در بات قرار بدین که مشتری بعد از ثبت سفارشش، مبلغ اون محصول رو به حساب شما کارت به کارت بکنه و کد پیگیری رو داخل سفارش خودش ارسال کنه.',
            'و حتی میتونین از گزینه پرداخت هنگام تحویل و یا پرداخت به صورت ارتباط تلفنی نیز استفاده کنین که نیاز به ثبت کردن شماره حساب داخل بات هم نیست.',
            '',
            emoji('diamonds ').'<b>مشتریا چطوری مارو پیدا میکنن ؟</b>',
            'شما میتونین در انتهای ساخت بات فروشگاهی خودتون یک لینک و یا تصویر QR کد مخصوص فروشگاه خودتون رو دریافت کنین تا مشتریان بتونن از اونا برای پیدا کردن فروشگاه شما داخل بات استفاده کنن.',
            '',
            emoji('diamonds ').'<b>این بات تبلیغات هم داره؟</b>',
            'خیر. به هیچ عنوان تبلیغاتی داخل فروشگاه شما ارسال نمیشه و حتی اطلاعات شما و مشتریان نیز محفوظ خواهد بود.',
            '',
            emoji('diamonds ').'<b>هزینه داشتن این بات چقدر هست؟</b>',
            'در ابتدا هر فروشگاهی که ثبت میشه به مدت 1 ماه میتونه به صورت رایگان از این بات استفاده کنه.و در انتهای ماه میتونین تصمیم بگیرین که خرید اشتراک برای این اکانت منطقی هست یا نه. که بعد به مدت هرتایمی که میخوایین میتونین اشتراک فروشگاه رو تمدید کنین.',
            'درصورتی که نمیخوایی دیگه فروشگاه رو تمدید کنین میتونین از طریق تنظیمات فروشگاه خود، فروشگاه خودتونو کامل ببندین که دیگه مشتریان از طریق لینک و یا تصویر QR کد نتونن به فروشگاه شما وصل بشن.',
            '',
            emoji('diamonds ').'<b>تضمین ساخته نشدن فروشگاه با شناسه مشابه فروشگاه شما.</b>',
            'در آینده نیز فروشگاهی با شناسه فروشگاه شما ساخته نخواهد شد.',
            'پس بنابراین نیازی نیست نگران این باشید که مشتریان شما بعدا به فروشگاه های دیگری وصل میشن یا نه.',
            '',
            emoji('diamonds ').'<b>رقابتی نبودن فروشگاه.</b>',
            'توسط این بات نیازی نیست نگران رقابتی بودن فروشگاه ها باشین.',
            'هر فروشگاهی مشتریان خودشونو دارن و از جامعه خودتون مشتریانتون رو میتونین تشویق کنین تا از بات فروشگاهی شما خرید کنن تا دیگه نیازی به ارائه اطلاعات درمورد محصول و ... نباشین.',
            'فقط کافیه مشتریان خرید خودشونو از فروشگاه شما انجام بدن و در انتها اطلاعیه برای شما ارسال میشه که یک خرید تازه دارید.',
            '',
            emoji('diamonds ').'<b>اشتراک ویژه 100 فروشگاه اول</b>', // TODO - changeable
            'ما برای 100 فروشگاه اولی که داخل این بات ساخته بشه، 2ماه اول اشتراک رایگان خواهند داشت.',
            '',
        ]);

        $this->replyWithMessage([
            'text' => $text,
            'parse_mode' => 'HTML'
        ]);
    }

    function nextSteps(): array
    {
        return [];
    }

    function failStepAction($chat_id, Update $update)
    {
        //
    }

    public function actionBeforeMake()
    {
        $this->removeCache();
    }
}
