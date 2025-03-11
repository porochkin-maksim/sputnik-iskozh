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
            100 => 'Транзакции',
            110 => 'Платежи',
            120 => 'Счета',
        };
    }

    public function name(): string
    {
        $code   = (string) $this->value;
        $result = (int) $code[strlen($code) - 1];

        return match ($result) {
            1 => 'Создание',
            2 => 'Просмотр',
            3 => 'Редактирование',
            4 => 'Удаление',
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

    case ROLES_CREATE         = 11;
    case ROLES_VIEW           = 12;
    case ROLES_EDIT           = 13;
    case ROLES_DROP           = 14;

    case USERS_CREATE         = 21;
    case USERS_VIEW           = 22;
    case USERS_EDIT           = 23;
    case USERS_DROP           = 24;

    case NEWS_CREATE          = 31;
    case NEWS_VIEW            = 32;
    case NEWS_EDIT            = 33;
    case NEWS_DROP            = 34;

    case ANNOUNCEMENTS_CREATE = 41;
    case ANNOUNCEMENTS_VIEW   = 42;
    case ANNOUNCEMENTS_EDIT   = 43;
    case ANNOUNCEMENTS_DROP   = 44;

    case FOLDERS_CREATE       = 51;
    case FOLDERS_VIEW         = 52;
    case FOLDERS_EDIT         = 53;
    case FOLDERS_DROP         = 54;

    case FILES_CREATE         = 61;
    case FILES_VIEW           = 62;
    case FILES_EDIT           = 63;
    case FILES_DROP           = 64;

    case ACCOUNTS_CREATE      = 71;
    case ACCOUNTS_VIEW        = 72;
    case ACCOUNTS_EDIT        = 73;
    case ACCOUNTS_DROP        = 74;

    case PERIODS_CREATE       = 81;
    case PERIODS_VIEW         = 82;
    case PERIODS_EDIT         = 83;
    case PERIODS_DROP         = 84;

    case SERVICES_CREATE      = 91;
    case SERVICES_VIEW        = 92;
    case SERVICES_EDIT        = 93;
    case SERVICES_DROP        = 94;

    case TRANSACTIONS_CREATE  = 101;
    case TRANSACTIONS_VIEW    = 102;
    case TRANSACTIONS_EDIT    = 103;
    case TRANSACTIONS_DROP    = 104;

    case PAYMENTS_CREATE      = 111;
    case PAYMENTS_VIEW        = 112;
    case PAYMENTS_EDIT        = 113;
    case PAYMENTS_DROP        = 114;

    case INVOICES_CREATE      = 121;
    case INVOICES_VIEW        = 122;
    case INVOICES_EDIT        = 123;
    case INVOICES_DROP        = 124;
}
