<?php

namespace App\Enums;

enum AdminPermission: string
{
    case Dashboard = 'dashboard';
    case Projects = 'projects';
    case Categories = 'categories';
    case Messages = 'messages';
    case Settings = 'settings';
    case Users = 'users';

    public function label(): string
    {
        return match ($this) {
            self::Dashboard => 'لوحة التحكم',
            self::Projects => 'الأعمال',
            self::Categories => 'الفئات',
            self::Messages => 'الرسائل',
            self::Settings => 'إعدادات الموقع',
            self::Users => 'المستخدمون والصلاحيات',
        };
    }

    public function routeName(): string
    {
        return match ($this) {
            self::Dashboard => 'admin.dashboard',
            self::Projects => 'admin.projects.index',
            self::Categories => 'admin.categories.index',
            self::Messages => 'admin.messages.index',
            self::Settings => 'admin.settings.edit',
            self::Users => 'admin.users.index',
        };
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $permission) {
            $options[$permission->value] = $permission->label();
        }

        return $options;
    }
}
