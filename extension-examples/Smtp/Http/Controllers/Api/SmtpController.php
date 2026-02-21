<?php

namespace Extensions\Smtp\Http\Controllers\Api;

use App\Contracts\Events\ExtensionEvent;
use App\Exceptions\BadException;
use App\Http\Controllers\Controller;
use Core\Permission\Application\UseCases\GetPermission;
use Core\Permission\Infrastructure\Helpers\PermissionNode;
use Extensions\Smtp\Http\Requests\IndexRequest;
use Extensions\Smtp\Http\Requests\StoreRequest;
use Extensions\Smtp\Models\SmtpModel;
use Illuminate\Http\JsonResponse;
use Extensions\Smtp\Http\Requests\SendRequest;
use Illuminate\Support\Facades\Notification;
use Extensions\Smtp\Notifications\SendTest;
use Illuminate\Support\Facades\Config;

class SmtpController extends Controller
{
      function __construct(
            private PermissionNode $permissionNode,
            private ExtensionEvent $extensionEvent
      ) {
           $this->permissionNode->setNode('smtp');
      }
      /**
       * GET /api/smtp
       * Get SMTP config by business
       */
      public function index(IndexRequest $request): JsonResponse
      {
            $validated = $request->all();
            // $this->permissionNode->getPermission("index")
            $this->extensionEvent->dispatch($this->permissionNode->getPermission("index"),$validated);
            $smtp = SmtpModel::where('business_id',$validated['business_id'])->first();
            return response()->json([
                  'message' => $smtp,
            ]);
      }

      /**
       * POST /api/smtp
       * Create or update SMTP config
       */
      public function store(StoreRequest $request): JsonResponse
      {
            $validated = $request->all();
            $this->extensionEvent->dispatch($this->permissionNode->getPermission("create"),$validated);
            $smtp = SmtpModel::updateOrCreate([
                  'business_id' => $validated['business_id']
            ],$validated);
            return response()->json([
                  'message' => 'SMTP settings saved successfully.'
            ]);
      }

      /**
       * POST /api/smtp/test
       * Send test mail (does NOT save DB)
       */
      public function send(SendRequest $request): JsonResponse
      {
            $validated = $request->all();
            $smtp = SmtpModel::where('business_id',$validated['business_id']);
            if ($smtp->count() == false) {
                  throw new BadException('You do not set smtp');
            }
            $smtp = $smtp->first();
            Config::set('mail.default', 'smtp-runtime');

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
            Notification::route('mail', $validated['to'])
                  ->notify(new SendTest(
                        $validated['subject'],
                        $validated['message']
                  ));
            return response()->json([
                  'message' => 'Test email sent successfully.',
            ]);
      }
}
