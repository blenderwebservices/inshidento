<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, HasUuids;

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isAdmin();
    }

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

    public function branches()
    {
        return $this->belongsToMany(Branch::class);
    }

    /**
     * Devuelve la lista de IDs de sucursales asignadas al usuario.
     * Retorna null si el usuario es Admin o Stakeholder (acceso global a todas las sucursales).
     * Retorna array de UUIDs de sucursales asignadas si es FM o Usuario Notificador.
     */
    public function assignedBranchIds(): ?array
    {
        if ($this->isAdmin() || $this->isStakeholder()) {
            return null;
        }

        if ($this->isFm()) {
            $assigned = $this->branches()->pluck('branches.id')->toArray();
            if (!empty($assigned)) {
                return $assigned;
            }
            return $this->branch_id ? [$this->branch_id] : [];
        }

        return $this->branch_id ? [$this->branch_id] : [];
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

    public function isAdmin(): bool
    {
        return in_array($this->rol, ['admin', 'superadmin']);
    }

    public function isFm(): bool
    {
        return in_array($this->rol, ['fm', 'manager', 'facility_manager']);
    }

    public function isStakeholder(): bool
    {
        return $this->rol === 'stakeholder';
    }

    public function isUser(): bool
    {
        return in_array($this->rol, ['user', 'notifier', 'store_user']);
    }

    public function canCreateIncidents(): bool
    {
        return $this->isAdmin() || $this->isFm() || $this->isUser();
    }

    public function canViewReports(): bool
    {
        return $this->isAdmin() || $this->isFm() || $this->isStakeholder();
    }

    public function canManagePurchaseOrders(): bool
    {
        return $this->isAdmin() || $this->isFm();
    }

    public function canViewPurchaseOrders(): bool
    {
        return $this->isAdmin() || $this->isFm() || $this->isStakeholder();
    }

    public function canViewSuppliers(): bool
    {
        return $this->isAdmin() || $this->isFm() || $this->isStakeholder();
    }
}
