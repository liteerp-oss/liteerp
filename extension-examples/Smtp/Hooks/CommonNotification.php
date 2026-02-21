<?php

namespace Extensions\Smtp\Hooks;

use App\Supports\Hooks\HookContext;
use App\Contracts\Hooks\HookInterface;
use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookResult;
use App\Supports\Hooks\HookTiming;
use Extensions\Smtp\Models\SmtpModel;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class CommonNotification implements HookInterface
{
    public function __construct() {}
    public static function supports(HookContext $context): bool
    {
        return $context->action === HookAction::CREATE
            && $context->phase === HookPhase::RESPONSE
            && $context->timing === HookTiming::ON
            && $context->module === 'CommonNotification';
    }

    public function handle(HookContext $context): HookResult
    {
        if (empty($context->payload['business_id'])) {
            if (SmtpModel::count() == false) {
                return HookResult::pass($context->payload);
            }
        }
        $business_id = $context->payload['business_id'];
        $setting = SmtpModel::where('business_id',$business_id);
        if ($setting->count() == false) {
            return HookResult::pass($context->payload);
        }
        $smtp = $setting->first();
        Config::set('mail.mailers.smtp-runtime', [
            'transport'  => 'smtp',
            'host'       => $smtp->host,
            'port'       => $smtp->port,
            'encryption' => $smtp->encryption,
            'username'   => $smtp->username,
            'password'   => $smtp->password,
            'timeout'    => null,
            'auth_mode'  => null,
        ]);
        Config::set('mail.from', [
            'address' => $smtp->from_email,
            'name'    => $smtp->from_name,
        ]);
        $context->payload = [
            ...$context->payload,
            'mailer' => 'smtp-runtime'
        ];
        return HookResult::pass($context->payload);
    }
}
