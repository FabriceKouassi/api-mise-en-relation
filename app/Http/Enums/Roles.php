<?php

namespace App\Http\Enums;

class Roles
{
    const DEMANDEUR = 'demandeur';
    const PRESTATAIRE = 'prestataire';
    const ADMIN = 'admin';

    /**
     * Recupere tous les roles
     * 
     * @return Array
    */

    public static function all():array
    {
        return [
            self::DEMANDEUR,
            self::PRESTATAIRE,
            self::ADMIN,
        ];
    }
}