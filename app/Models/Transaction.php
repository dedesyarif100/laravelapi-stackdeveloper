<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    public function customer() {
        return $this->hasMany(Customer::class);
    }

    public function product() {
        return $this->hasMany(Product::class);
    }
}
