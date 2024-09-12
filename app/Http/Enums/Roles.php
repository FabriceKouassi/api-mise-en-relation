<?php

namespace App\Http\Enums;

class Roles
{
    const ADMIN = 'admin';
    const DEMANDEUR = 'demandeur';
    const PRESTATAIRE = 'prestataire';

    /**
     * Recupere tous les roles
     * 
     * @return Array
    */

    public static function all():array
    {
        return [
            self::ADMIN,
            self::DEMANDEUR,
            self::PRESTATAIRE,
        ];
    }
}