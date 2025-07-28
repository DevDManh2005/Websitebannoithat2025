<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UserProfile
 *
 * @property int           $id
 * @property int           $user_id
 * @property string|null   $avatar
 * @property string|null   $dob
 * @property string|null   $gender
 * @property string|null   $province_id    // Thay đổi kiểu dữ liệu thành string
 * @property string|null   $province_name
 * @property string|null   $district_id    // Thay đổi kiểu dữ liệu thành string
 * @property string|null   $district_name
 * @property string|null   $ward_id        // Thay đổi kiểu dữ liệu thành string
 * @property string|null   $ward_name
 * @property string|null   $bio
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|UserProfile whereUserId($value)
 * @mixin \Eloquent
 */
class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'avatar',
        'dob',
        'gender',
        'province_id',
        'province_name',
        'district_id',
        'district_name',
        'ward_id',
        'ward_name',
        'bio',
    ];

    // Thêm casts để Laravel tự động ép kiểu khi truy vấn
    protected $casts = [
        'province_id' => 'string',
        'district_id' => 'string',
        'ward_id' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
