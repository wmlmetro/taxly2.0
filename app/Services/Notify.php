<?php

namespace App\Services;

use App\Models\Notification;
use Illuminate\Support\Facades\Mail;

class Notify
{
  public static function email(int $orgId, string $mailableClass, array $data): Notification
  {
    $notification = Notification::create([
      'organization_id'   => $orgId,
      'type'     => 'email',
      'template' => $mailableClass,
      'status'   => 'pending',
    ]);

    try {
      // assumes you have a Mailable like new InvoiceSubmittedMail($data)
      Mail::to($data['to'])->send(new $mailableClass($data));
      $notification->markSent();
    } catch (\Throwable $e) {
      $notification->markFailed();
    }

    return $notification;
  }

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
