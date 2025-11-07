<?php

namespace App\Services;

use App\Models\Notification;

class Notify
{
  public static function sms(int $orgId, string $to, string $message): Notification
  {
    $notification = Notification::create([
      'organization_id'   => $orgId,
      'type'     => 'sms',
      'template' => 'generic',
      'status'   => 'pending',
    ]);

    try {
      // integrate provider here (Termii/Twilio/etc.)
      // Sms::send($to, $message);
      $notification->markSent();
    } catch (\Throwable $e) {
      $notification->markFailed();
    }

    return $notification;
  }

  public static function whatsapp(int $orgId, string $to, string $message): Notification
  {
    $n = Notification::create([
      'organization_id'   => $orgId,
      'type'     => 'whatsapp',
      'template' => 'generic',
      'status'   => 'pending',
    ]);

    try {
      // integrate provider here
      $n->markSent();
    } catch (\Throwable $e) {
      $n->markFailed();
    }

    return $n;
  }
}
