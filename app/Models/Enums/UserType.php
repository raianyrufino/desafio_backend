<?php

namespace App\Models\Enums;

class UserType extends Enum
{
    /**
     * Indica que um usuário é comum
     */
    const COMUM = 'COMUM';

    /**
     * Indica que um usuário é lojista
     */
    const LOJISTA = 'LOJISTA';
}