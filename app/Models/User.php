<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */


    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function userKaryawan()
    {
        return $this->hasOne(UserKaryawan::class);
    }

    public function karyawan()
    {
        return $this->hasOneThrough(
            Karyawan::class,
            UserKaryawan::class,
            'id_user',   // FK on user_karyawan table
            'nik',       // FK on karyawan table
            'id',        // PK on users table
            'nik'        // user_karyawan.NIK
        );
    }

    protected $appends = [
        'branch_code',
        // 'branch_name',
        // 'region'
    ];

    public function getBranchCodeAttribute()
    {
        return $this->karyawan?->branch_code;
    }

    // public function getBranchNameAttribute()
    // {
    //     return $this->karyawan?->branch->nama_cabang; // if exists
    // }

    // public function getRegionAttribute()
    // {
    //     return $this->karyawan?->branch->region;
    // }
}
