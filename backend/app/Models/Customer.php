<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\EncryptionService;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'segment',
        'total_purchases',
        'total_orders'
    ];

    protected $casts = [
        'total_purchases' => 'decimal:2',
        'total_orders' => 'integer',
    ];
    
    /**
     * Hidden fields (hash columns should not be exposed in API)
     */
    protected $hidden = [
        'phone_hash',
        'address_hash'
    ];

    /**
     * Accessor for phone - returns decrypted value
     */
    public function getPhoneAttribute($value)
    {
        // Decrypt the value from database
        if (!empty($value)) {
            return EncryptionService::decrypt($value);
        }
        
        return null;
    }

    /**
     * Mutator for phone - encrypts before saving
     */
    public function setPhoneAttribute($value)
    {
        if (!empty($value)) {
            // Store encrypted value directly in phone column
            $this->attributes['phone'] = EncryptionService::encrypt($value);
            $this->attributes['phone_hash'] = EncryptionService::hash($value);
        } else {
            $this->attributes['phone'] = null;
            $this->attributes['phone_hash'] = null;
        }
    }

    /**
     * Accessor for address - returns decrypted value
     */
    public function getAddressAttribute($value)
    {
        // Decrypt the value from database
        if (!empty($value)) {
            return EncryptionService::decrypt($value);
        }
        
        return null;
    }

    /**
     * Mutator for address - encrypts before saving
     */
    public function setAddressAttribute($value)
    {
        if (!empty($value)) {
            // Store encrypted value directly in address column
            $this->attributes['address'] = EncryptionService::encrypt($value);
            $this->attributes['address_hash'] = EncryptionService::hash($value);
        } else {
            $this->attributes['address'] = null;
            $this->attributes['address_hash'] = null;
        }
    }

    /**
     * Get masked phone for display in lists
     */
    public function getMaskedPhoneAttribute()
    {
        $phone = $this->phone;
        return EncryptionService::maskPhone($phone);
    }

    /**
     * Get masked address for display in lists
     */
    public function getMaskedAddressAttribute()
    {
        $address = $this->address;
        return EncryptionService::maskAddress($address);
    }

    /**
     * Search by phone (using hash)
     */
    public static function searchByPhone($phone)
    {
        $hash = EncryptionService::hash($phone);
        return self::where('phone_hash', $hash)->get();
    }

    /**
     * Search by address (using hash)
     */
    public static function searchByAddress($address)
    {
        $hash = EncryptionService::hash($address);
        return self::where('address_hash', $hash)->get();
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
