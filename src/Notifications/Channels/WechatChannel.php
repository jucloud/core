<?php

namespace JuCloud\Core\Notifications\Channels;

use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Exceptions\InvalidConfigException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Notifications\Notification;
use EasyWeChat\Factory;
use Illuminate\Support\Arr;
use RuntimeException;

/**
 * 微信通知 - 驱动
 *
 * Class WechatChannel
 * @package Discuz\Notifications\Channels
 */
class WechatChannel
{
    protected $settings;

    /**
     * WechatChannel constructor.
     * @param SettingsRepository $settings
     */
    public function __construct(SettingsRepository $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Send the given notification.
     *
     * @param $notifiable
     * @param Notification $notification
     * @throws InvalidArgumentException
     * @throws InvalidConfigException
     * @throws GuzzleException
     */
    public function send($notifiable, Notification $notification)
    {
        if (!empty($notifiable->wechat) && !empty($notifiable->wechat->mp_openid)) {

            // wechat post json
            $build = $notification->toWechat($notifiable);

            // 替换掉内容中的换行符
            $content = str_replace(PHP_EOL, '', Arr::get($build, 'content'));
            $build['content'] = json_decode($content, true);

            // get Wechat Template ID
            $notificationData = $notification->getTplData(Arr::get($build, 'raw.tpl_id'));
            $templateID = $notificationData->template_id;

            $appID = $this->settings->get('offiaccount_app_id', 'wx_offiaccount');
            $secret = $this->settings->get('offiaccount_app_secret', 'wx_offiaccount');

            if (blank($templateID) || blank($appID) || blank($secret)) {
                throw new RuntimeException('notification_is_missing_template_config');
            }

            // to user
            $toUser = $notifiable->wechat->mp_openid;

            // redirect
            $url = Arr::pull($build, 'content.redirect_url');

            $app = Factory::officialAccount([
                'app_id' => $appID,
                'secret' => $secret,
            ]);

            // send
            $app->template_message->send([
                'touser' => $toUser,
                'template_id' => $templateID,
                'url' => $url,
                'data' => $build['content']['data'],
            ]);
        }
    }
}
