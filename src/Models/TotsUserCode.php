<?php

namespace Tots\AuthTfaBasic\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Description of Model
 *
 * @author matiascamiletti
 */
class TotsUserCode extends Model
{
    const STATUS_PENDING = 0;
    const STATUS_VERIFIED = 1;
    const STATUS_USED = 2;
    const STATUS_EXPIRED = 3;

    const PROVIDER_EMAIL = 0;

    protected $table = 'tots_user_code';
    
    //protected $casts = ['data' => 'array'];
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    //public $timestamps = false;
}