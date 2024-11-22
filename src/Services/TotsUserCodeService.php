<?php

namespace Tots\AuthTfaBasic\Services;

use Illuminate\Support\Facades\DB;
use Tots\Auth\Models\TotsUser;
use Tots\AuthTfaBasic\Models\TotsUserCode;
use Tots\Core\Exceptions\TotsException;

/**
 * Description of Model
 *
 * @author matiascamiletti
 */
class TotsUserCodeService
{
    public function create($identifier, $provider, $verifyIfExist = true)
    {
        // Expire old codes
        $this->expiredAll($identifier);
        // Verify if exist email or phone user
        $userId = null;
        if($verifyIfExist){
          $user = TotsUser::where(function ($query) use ($identifier) {
                            $query->where('email', $identifier)
                                  ->orWhere('phone', $identifier);
                            })
                            ->first();
          if($user === null){
              throw new TotsException('Email or phone not exist.', 'not-found-email-nor-phone', 404);
          }
          $userId = $user->id;
        }
        // Create new code
        $code = new TotsUserCode();
        $code->user_id = $userId;
        $code->sent = $identifier;
        $code->code = $this->generateCode();
        $code->status = TotsUserCode::STATUS_PENDING;
        $code->provider = $provider;
        $code->expired_at = date('Y-m-d H:i:s', strtotime('+30 minutes'));
        $code->save();

        return $code;
    }

    public function valid($identifier, $code, $provider)
    {
        // Verify if exist code
        $code = TotsUserCode::where('sent', $identifier)
            ->where('code', $code)
            ->where('provider', $provider)
            ->where('status', TotsUserCode::STATUS_PENDING)
            ->where('expired_at', '>=', date('Y-m-d H:i:s'))
            ->first();
        if($code === null){
            throw new TotsException('Code is invalid', 'code-invalid', 404);
        }
        // Update code
        $code->status = TotsUserCode::STATUS_VERIFIED;
        $code->save();

        return $code;
    }

    public function use($identifier, $code, $provider)
    {
        // Verify if exist code
        $code = TotsUserCode::where('sent', $identifier)
            ->where('code', $code)
            ->where('provider', $provider)
            ->whereIn('status', [TotsUserCode::STATUS_PENDING, TotsUserCode::STATUS_VERIFIED])
            ->where('expired_at', '>=', date('Y-m-d H:i:s'))
            ->first();
        if($code === null){
            throw new TotsException('Code is invalid', 'code-invalid', 404);
        }
        // Update code
        $code->status = TotsUserCode::STATUS_USED;
        $code->save();

        return $code;
    }

    public function expiredAll($identifier)
    {
        TotsUserCode::where('sent', $identifier)->update(['status' => TotsUserCode::STATUS_EXPIRED]);
    }

    public function generateCode()
    {
        $code = '';
        for($i=0; $i<6; $i++){
            $code .= rand(0,9);
        }
        return $code;
    }
}