<?php

namespace Tots\AuthTfaBasic\Services;

use Illuminate\Support\Facades\DB;
use Tots\Auth\Models\TotsUser;
use Tots\AuthTfaBasic\Models\TotsUserCode;

/**
 * Description of Model
 *
 * @author matiascamiletti
 */
class TotsUserCodeService
{
    public function create($email, $provider, $verifyIfExist = true)
    {
        // Expire old codes
        $this->expiredAll($email);
        // Verify if exist email user
        $userId = null;
        if($verifyIfExist){
            $user = TotsUser::where('email', $email)->first();
            if($user === null){
                throw new \Exception('User not found');
            }
            $userId = $user->id;
        }
        // Create new code
        $code = new TotsUserCode();
        $code->user_id = $userId;
        $code->sent = $email;
        $code->code = $this->generateCode();
        $code->status = TotsUserCode::STATUS_PENDING;
        $code->provider = $provider;
        $code->expired_at = date('Y-m-d H:i:s', strtotime('+30 minutes'));
        $code->save();

        return $code;
    }

    public function valid($email, $code, $provider)
    {
        // Verify if exist code
        $code = TotsUserCode::where('sent', $email)
            ->where('code', $code)
            ->where('provider', $provider)
            ->where('status', TotsUserCode::STATUS_PENDING)
            ->where('expired_at', '>=', date('Y-m-d H:i:s'))
            ->first();
        if($code === null){
            throw new \Exception('Code not found');
        }
        // Update code
        $code->status = TotsUserCode::STATUS_VERIFIED;
        $code->save();

        return $code;
    }

    public function use($email, $code, $provider)
    {
        // Verify if exist code
        $code = TotsUserCode::where('sent', $email)
            ->where('code', $code)
            ->where('provider', $provider)
            ->whereIn('status', [TotsUserCode::STATUS_PENDING, TotsUserCode::STATUS_VERIFIED])
            ->where('expired_at', '>=', date('Y-m-d H:i:s'))
            ->first();
        if($code === null){
            throw new \Exception('Code not found');
        }
        // Update code
        $code->status = TotsUserCode::STATUS_USED;
        $code->save();

        return $code;
    }

    public function expiredAll($email)
    {
        TotsUserCode::where('sent', $email)->update(['status' => TotsUserCode::STATUS_EXPIRED]);
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