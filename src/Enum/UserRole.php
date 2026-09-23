<?php

namespace App\Enum;

enum UserRole: string
{
    case CANDIDATE = 'candidate';
    case RECRUITER = 'recruiter';
    case ADMINISTRATOR = 'administrator';

    public function label(): string
    {
        return match($this) {
            self::CANDIDATE => 'Candidate',
            self::RECRUITER => 'Recruiter',
            self::ADMINISTRATOR => 'Administrator',
        };
    }

    public function symfonyRole(): string
    {
        return match($this) {
            self::CANDIDATE => 'ROLE_CANDIDATE',
            self::RECRUITER => 'ROLE_RECRUITER',
            self::ADMINISTRATOR => 'ROLE_ADMIN',
        };
    }
}
