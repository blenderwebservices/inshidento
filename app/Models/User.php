<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids;

    protected $fillable = [
        'name',
        'email',
        'password',
        'company_id',
        'branch_id',
        'rol',
        'tipo_fixer',
        'especialidad',
        'zona_cobertura',
        'fcm_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function incidentsAsNotifier()
    {
        return $this->hasMany(Incident::class, 'notifier_id');
    }

    public function incidentsAsFixer()
    {
        return $this->hasMany(Incident::class, 'fixer_id');
    }

    public function billingReports()
    {
        return $this->hasMany(BillingReport::class, 'fixer_id');
    }
}
