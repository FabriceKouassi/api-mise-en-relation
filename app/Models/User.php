<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'users';
    
    protected $fillable = [
        'lastName',
        'firstName',
        'slug',
        'img',
        'email',
        'role',
        'phone',
        'password',
    ];

    public function getImg()
    {
        // file_exists(public_path('storage/'.config('global.user_image').'/'.$this->img))

        if ($this->img && file_exists(storage_path(config('global.user_image').'/'. $this->img))) {
            $img = asset('storage/'.config('global.user_image').'/'.$this->img);
        } else {
            return asset('/'.config('global.user_image_default'));
        }

        return $img;
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class)->using(ServiceUser::class);
    }

    private function compressImage(string $filename, string $destination): bool
    {
        $filename = storage_path('app/public/') . $filename;
        $destination = storage_path('app/public/') . $destination;
        $info = getimagesize($filename);

        $image = match ($info['mime']) {
            'image/png' => imagecreatefrompng($filename),
            default => imagecreatefromjpeg($filename)
        };

        return imagejpeg($image, $destination, 80);
    }

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

}
