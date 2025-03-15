<?php declare(strict_types=1);

namespace Core\Domains\Access\Enums;

use Core\Enums\EnumCommonTrait;

enum PermissionEnum: int
{
    use EnumCommonTrait;

    private function getSectionCode(): int
    {
        $code = (string) $this->value;
        if (strlen($code) === 2) {
            $result = (int) $code[0];
        }
        else {
            $result = (int) ($code[0] . $code[1]);
        }

        return $result * 10;
    }

    public function sectionName(): string
    {
        return match ($this->getSectionCode()) {
            10  => 'Роли',
            20  => 'Пользователи',
            30  => 'Новости',
            40  => 'Объявления',
            50  => 'Каталоги',
            60  => 'Файлы',
            70  => 'Участки',
            80  => 'Периоды',
            90  => 'Услуги',
            100 => 'Счета',
            110 => 'Транзакции',
            120 => 'Платежи',
            130 => 'Счётчики',
        };
    }

    public function name(): string
    {
        $code   = (string) $this->value;
        $result = (int) $code[strlen($code) - 1];

        return match ($result) {
            1 => 'Просмотр',
            2 => 'Редактирование',
            3 => 'Удаление',
        };
    }

    public static function getCases(bool $grouped = false): array
    {
        $result         = [];
        $currentSection = null;
        foreach (self::cases() as $case) {
            $sectionCode = $case->getSectionCode();
            if ($sectionCode !== $currentSection) {
                $currentSection = $sectionCode;

                if ($grouped) {
                    $result[$sectionCode][$sectionCode] = $case->sectionName();
                }
                else {
                    $result[$sectionCode] = $case->sectionName();
                }
            }

            if ($grouped) {
                $result[$sectionCode][$case->value] = $case->name();
            }
            else {
                $result[$sectionCode][$case->value] = $case->name();
            }
        }
        return $result;
    }

    case ROLES_VIEW        = 11;
    case ROLES_EDIT        = 12;
    case ROLES_DROP        = 13;

    case USERS_VIEW        = 21;
    case USERS_EDIT        = 22;
    case USERS_DROP        = 23;

    case NEWS_VIEW         = 31;
    case NEWS_EDIT         = 32;
    case NEWS_DROP         = 33;

    case ANNOUNCEMENTS_VIE = 41;
    case ANNOUNCEMENTS_EDI = 42;
    case ANNOUNCEMENTS_DRO = 43;

    case FOLDERS_VIEW      = 51;
    case FOLDERS_EDIT      = 52;
    case FOLDERS_DROP      = 53;

    case FILES_VIEW        = 61;
    case FILES_EDIT        = 62;
    case FILES_DROP        = 63;

    case ACCOUNTS_VIEW     = 71;
    case ACCOUNTS_EDIT     = 72;
    case ACCOUNTS_DROP     = 73;

    case PERIODS_VIEW      = 81;
    case PERIODS_EDIT      = 82;
    case PERIODS_DROP      = 83;

    case SERVICES_VIEW     = 91;
    case SERVICES_EDIT     = 92;
    case SERVICES_DROP     = 93;

    case INVOICES_VIEW     = 101;
    case INVOICES_EDIT     = 102;
    case INVOICES_DROP     = 103;

    case TRANSACTIONS_VIEW = 111;
    case TRANSACTIONS_EDIT = 112;
    case TRANSACTIONS_DROP = 113;

    case PAYMENTS_VIEW     = 121;
    case PAYMENTS_EDIT     = 122;
    case PAYMENTS_DROP     = 123;

    case COUNTERS_VIEW     = 131;
    case COUNTERS_EDIT     = 132;
    case COUNTERS_DROP     = 133;
}
