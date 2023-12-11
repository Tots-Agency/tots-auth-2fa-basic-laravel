<?php

namespace Tots\AuthTfaBasic\Repositories;

use Tots\AuthTfaBasic\Models\TotsUserCode;

/**
 * Description of Model
 *
 * @author matiascamiletti
 */
class TotsUserCodeRepository
{
    public function removeByUserId($userId)
    {
        TotsUserCode::where('user_id', $userId)->delete();
    }
}