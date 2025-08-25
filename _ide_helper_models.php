<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property int $invoice_id
 * @property string $buyer_response
 * @property string|null $reason_code
 * @property \Illuminate\Support\Carbon $timestamp
 * @property string|null $actor
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Invoice $invoice
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Acceptance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Acceptance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Acceptance query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Acceptance whereActor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Acceptance whereBuyerResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Acceptance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Acceptance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Acceptance whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Acceptance whereReasonCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Acceptance whereTimestamp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Acceptance whereUpdatedAt($value)
 */
	class Acceptance extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $invoice_id
 * @property string|null $pdf_path
 * @property string|null $json_path
 * @property string|null $signature_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Invoice $invoice
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artifact newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artifact newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artifact query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artifact whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artifact whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artifact whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artifact whereJsonPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artifact wherePdfPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artifact whereSignaturePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artifact whereUpdatedAt($value)
 */
	class Artifact extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $entity_ref
 * @property string $verb
 * @property int|null $actor_id
 * @property string|null $payload_hash
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property string|null $updated_at
 * @property-read \App\Models\User|null $actor
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $auditable
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditEvent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditEvent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditEvent query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditEvent whereActorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditEvent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditEvent whereEntityRef($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditEvent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditEvent wherePayloadHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditEvent whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditEvent whereVerb($value)
 */
	class AuditEvent extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $organization_id
 * @property string|null $buyer_organization_ref
 * @property string $total_amount
 * @property array<array-key, mixed>|null $tax_breakdown
 * @property string $vat_treatment
 * @property string $wht_amount
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Acceptance> $acceptances
 * @property-read int|null $acceptances_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Artifact> $artifacts
 * @property-read int|null $artifacts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AuditEvent> $auditEvents
 * @property-read int|null $audit_events_count
 * @property-read \App\Models\Irns|null $irn
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InvoiceItem> $items
 * @property-read int|null $items_count
 * @property-read \App\Models\Organization $organization
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Submission> $submissions
 * @property-read int|null $submissions_count
 * @method static \Database\Factories\InvoiceFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereBuyerOrganizationRef($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereTaxBreakdown($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereVatTreatment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereWhtAmount($value)
 */
	class Invoice extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $invoice_id
 * @property string $description
 * @property int $quantity
 * @property string $price
 * @property string $line_total
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Invoice $invoice
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem whereLineTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem whereUpdatedAt($value)
 */
	class InvoiceItem extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $invoice_id
 * @property string $irn_hash
 * @property string $qr_text
 * @property string|null $qr_image_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Invoice $invoice
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Irns newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Irns newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Irns query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Irns whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Irns whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Irns whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Irns whereIrnHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Irns whereQrImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Irns whereQrText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Irns whereUpdatedAt($value)
 */
	class Irns extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $organization_id
 * @property string $type
 * @property string $template
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Organization|null $org
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereTemplate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereUpdatedAt($value)
 */
	class Notification extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $tenant_id
 * @property string $tin
 * @property string $legal_name
 * @property string|null $address
 * @property array<array-key, mixed>|null $bank_details
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Invoice> $invoices
 * @property-read int|null $invoices_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Notification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\Tenant $tenant
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WebhookEndpoint> $webhookEndpoints
 * @property-read int|null $webhook_endpoints_count
 * @method static \Database\Factories\OrganizationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereBankDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereLegalName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereTin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereUpdatedAt($value)
 */
	class Organization extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $invoice_id
 * @property string $channel
 * @property string|null $atrs_txn_id
 * @property string $status
 * @property int $attempts
 * @property string|null $last_error
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Invoice $invoice
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission whereAtrsTxnId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission whereAttempts($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission whereChannel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission whereLastError($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission whereUpdatedAt($value)
 */
	class Submission extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $brand
 * @property string|null $domain
 * @property array<array-key, mixed>|null $feature_flags
 * @property string $retention_policy
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Organization> $organizations
 * @property-read int|null $organizations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UsageMeter> $usageMeters
 * @property-read int|null $usage_meters_count
 * @method static \Database\Factories\TenantFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereBrand($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereFeatureFlags($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereRetentionPolicy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereUpdatedAt($value)
 */
	class Tenant extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $tenant_id
 * @property array<array-key, mixed>|null $counters
 * @property string $billing_cycle
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Tenant $tenant
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsageMeter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsageMeter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsageMeter query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsageMeter whereBillingCycle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsageMeter whereCounters($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsageMeter whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsageMeter whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsageMeter whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsageMeter whereUpdatedAt($value)
 */
	class UsageMeter extends \Eloquent {}
}

namespace App\Models{
/**
 * @property \Laravel\Sanctum\PersonalAccessToken[] $tokens
 * @method \Laravel\Sanctum\PersonalAccessToken|null currentAccessToken()
 * @property int $id
 * @property int|null $organization_id
 * @property string|null $mfa
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AuditEvent> $auditEvents
 * @property-read int|null $audit_events_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\Organization|null $organization
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereMfa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $organization_id
 * @property string $url
 * @property string $secret
 * @property array<array-key, mixed>|null $subscribed_events
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Organization|null $org
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookEndpoint newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookEndpoint newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookEndpoint query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookEndpoint whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookEndpoint whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookEndpoint whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookEndpoint whereSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookEndpoint whereSubscribedEvents($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookEndpoint whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookEndpoint whereUrl($value)
 */
	class WebhookEndpoint extends \Eloquent {}
}

