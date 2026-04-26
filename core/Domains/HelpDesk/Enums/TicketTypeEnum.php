<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk\Enums;

use Core\Shared\Enums\EnumCommonTrait;

enum TicketTypeEnum: int
{
    use EnumCommonTrait;

    case INCIDENT = 1;
    case QUESTION = 2;
    case PROPOSAL = 3;
    case SERVICE  = 4;
    case FEEDBACK = 5;

    public static function byCode(string $code): ?self
    {
        return match ($code) {
            'incident' => self::INCIDENT,
            'question' => self::QUESTION,
            'proposal' => self::PROPOSAL,
            'service'  => self::SERVICE,
            'feedback' => self::FEEDBACK,
            default    => null,
        };
    }

    public function name(): string
    {
        return match ($this) {
            self::INCIDENT => 'Авария',
            self::QUESTION => 'Вопрос',
            self::PROPOSAL => 'Предложение',
            self::SERVICE  => 'Заявка на услугу',
            self::FEEDBACK => 'Отзыв',
        };
    }

    public function code(): string
    {
        return match ($this) {
            self::INCIDENT => 'incident',
            self::QUESTION => 'question',
            self::PROPOSAL => 'proposal',
            self::SERVICE  => 'service',
            self::FEEDBACK => 'feedback',
        };
    }

    /** font-awesome */
    public function icon(): string
    {
        return match ($this) {
            // Иконка "череп и кости" для аварий/поломок (символизирует опасность)
            self::INCIDENT => 'fa fa-exclamation-triangle',

            // Иконка "вопросительный знак" для вопросов
            self::QUESTION => 'fa fa-question-circle',

            // Иконка "лампа" для предложений (символизирует идею)
            self::PROPOSAL => 'fa fa-lightbulb-o',

            // Иконка "шестеренки" для заявок на услугу (символизирует действие/настройку)
            self::SERVICE  => 'fa fa-cogs',

            // Иконка "звезда" для отзыва/жалобы (обычно используется для рейтинга/фидбека)
            self::FEEDBACK => 'fa fa-star',
        };
    }

    public function color(): string
    {
        return match ($this) {
            // Красный для аварий/поломок (опасность)
            self::INCIDENT => 'danger',

            // Синий для вопросов (информационный)
            self::QUESTION => 'info',

            // Жёлтый/Золотой для предложений (идеи)
            self::PROPOSAL => 'warning',

            // Серый для заявок на услугу (нейтральный/технический)
            self::SERVICE  => 'secondary',

            // Фиолетовый для отзыва/жалобы (можно менять, если используете другую тему)
            self::FEEDBACK => 'primary',
        };
    }
}