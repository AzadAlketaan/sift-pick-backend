<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
        'email_type',
        'email_subject',
        'email_header',
        'email_body',
        'email_footer',
        'reminder_period',
        'schedule_periods',
        'sensitive_times',
        'sms_message',
        'is_active',
        'is_active_web',
        'is_active_mobile',
        'is_active_sms',
        'is_tracking_web',
        'is_tracking_mobile',
        'is_notification',
        'transactional_message_id',
        'notification_title',
        'notification_message'
    ];

    public function userEmails(): HasMany
    {
        return $this->hasMany(UserEmail::class);
    }

}
