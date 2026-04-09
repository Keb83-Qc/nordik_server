<?php

namespace App\Enums;

/**
 * Identifiants de rôles utilisateur (correspondent aux IDs en base).
 * Centralise toutes les références aux rôles pour éviter les "magic numbers".
 */
class UserRole
{
    public const ADMIN       = 1;
    public const SUPER_ADMIN = 2;
    public const ADVISOR     = 3;
    public const CLIENT      = 4;
    public const STAFF       = 5;
    public const PENDING     = 6; // Compte en attente de validation

    /** Rôles considérés comme administrateurs */
    public const ADMIN_ROLES = [self::ADMIN, self::SUPER_ADMIN];
}
