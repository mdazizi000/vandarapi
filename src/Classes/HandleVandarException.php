<?php


namespace MohmdAzizi\VandarApi;


use App\Exceptions\VandarException;

class HandleVandarException
{
    /**
     * @throws VandarException
     */
    public static function handle($response)
    {
        if ($response->status() == 400) {
            throw new VandarException('درخواست شما از سرویس وندار اشتباه است.', 400);
        }
        if ($response->status() == 401) {
            throw new VandarException('این خطا در صورتی برمی گردد که یا توکن را در درخواست خود ارسال نکردید یا توکن شما معتبر نمی باشد.', 401);
        }
        if ($response->status() == 200 && $response['status'] == 0) {
            throw new VandarException('ناموفق', 402);
        }
        if ($response->status() == 403) {
            throw new VandarException('شما دسترسی لازم برای دریافت این پاسخ را ندارید.', 403);
        }
        if ($response->status() == 404) {
            throw new VandarException('درخواست ارسال شده با این آدرس در سرویس وندار موجود نیست.', 404);
        }
        if ($response->status() == 405) {
            throw new VandarException('آدرس ارسال شده توسط شما با متد آن همخوانی ندارد لطفا با توجه به مستندات متد خود را اصلاح کنید.', 405);
        }
        if ($response->status() == 406) {
            throw new VandarException('ورودی فرستاده شده از سمت شما برای سرویس وندار باید به فرمت json باشد لطفا فرمت ورودی را اصلاح کنید.', 405);
        }
        if ($response->status() == 410) {
            throw new VandarException('درخواست ارسال شده از سرویس وندار حذف شده است.', 410);
        }
        if ($response->status() == 422) {
            throw new VandarException('یکی از فیلدهایی که برای سرویس ارسال کرده اید اشتباه است.', 422);
        }
        if ($response->status() == 429) {
            throw new VandarException('تعداد درخواست های ارسال شده از سمت شما برای سرویس ما قابل پاسخگویی نیست، لطفا کمی صبر کنید و دوباره خطای خود را ارسال کنید.', 429);
        }
        if ($response->status() == 500) {
            throw new VandarException('خطای نامشخصی در سرور رخ داده است لطفا کمی صبر کنید و دوباره تلاش کنید.', 500);
        }
        if ($response->status() == 503) {
            throw new VandarException('سرویس وندار در حال حاضر موقتا در دسترس نیست، لطفا کمی صبر کنید و دوباره تلاش کنید.', 503);
        }
//        if ($response['status'] == 0) {
//            throw new VandarException($response->errors);
//        }
    }
}
