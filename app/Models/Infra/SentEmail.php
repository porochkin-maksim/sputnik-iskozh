<?php declare(strict_types=1);

namespace App\Models\Infra;

use App\Models\Interfaces\CastsInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int     $id
 * @property string  $message_id
 * @property string  $recipient_email
 * @property ?string $recipient_name
 * @property string  $subject
 * @property ?string $body
 * @property ?string $mailer
 * @property string  $status
 * @property array   $metadata
 * @property array   $attachments
 * @property Carbon  $sent_at
 * @property ?Carbon $delivered_at
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 */
class SentEmail extends Model implements CastsInterface
{
    public const string TABLE = 'sent_emails';

    protected $table = self::TABLE;

    public const string ID              = 'id';
    public const string MESSAGE_ID      = 'message_id';
    public const string RECIPIENT_EMAIL = 'recipient_email';
    public const string RECIPIENT_NAME  = 'recipient_name';
    public const string SUBJECT         = 'subject';
    public const string BODY            = 'body';
    public const string ATTACHMENTS     = 'attachments';
    public const string MAILER          = 'mailer';
    public const string STATUS          = 'status';
    public const string METADATA        = 'metadata';
    public const string SENT_AT         = 'sent_at';
    public const string DELIVERED_AT    = 'delivered_at';

    protected $guarded = [];

    protected $casts = [
        self::METADATA     => self::CAST_JSON,
        self::ATTACHMENTS  => self::CAST_JSON,
        self::SENT_AT      => self::CAST_DATETIME,
        self::DELIVERED_AT => self::CAST_DATETIME,
    ];

    /**
     * Возможные статусы письма
     */
    public const string STATUS_SENT      = 'sent';
    public const string STATUS_DELIVERED = 'delivered';
    public const string STATUS_OPENED    = 'opened';
    public const string STATUS_CLICKED   = 'clicked';
    public const string STATUS_FAILED    = 'failed';

    /**
     * Проверяет, было ли письмо доставлено
     */
    public function isDelivered(): bool
    {
        return in_array($this->status, [
            self::STATUS_DELIVERED,
            self::STATUS_OPENED,
            self::STATUS_CLICKED,
        ], true);
    }

    /**
     * Проверяет, произошла ли ошибка при доставке
     */
    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Получить имя получателя (если есть) или email
     */
    public function getRecipientDisplayName(): string
    {
        return $this->recipient_name ? : $this->recipient_email;
    }

    /**
     * Обновить статус письма на основе события от почтового провайдера
     */
    public function updateStatusFromEvent(string $event): void
    {
        $newStatus = match ($event) {
            'delivered'                                   => self::STATUS_DELIVERED,
            'opened'                                      => self::STATUS_OPENED,
            'clicked'                                     => self::STATUS_CLICKED,
            'failed', 'bounced', 'rejected', 'complained' => self::STATUS_FAILED,
            default                                       => $this->status,
        };

        if ($newStatus !== $this->status) {
            $this->status = $newStatus;

            if (in_array($newStatus, [self::STATUS_DELIVERED, self::STATUS_OPENED, self::STATUS_CLICKED], true)) {
                $this->delivered_at = $this->delivered_at ?? now();
            }

            $this->save();
        }
    }

    /**
     * Получить заголовки письма для добавления в Mailable
     */
    public function getMailHeaders(): array
    {
        return [
            'X-Message-ID' => $this->message_id,
            'X-Email-ID'   => $this->id,
        ];
    }
}