<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    public $fillable = [
        'can_create_folder',
        'can_update_folder',
        'can_delete_folder',
        'can_create_docment',
        'can_delete_document'
    ];
 
    public $casts = [
        'can_create_folder'  => 'boolean',
        'can_update_folder'  => 'boolean',
        'can_delete_folder'  => 'boolean',
        'can_create_docment' => 'boolean',
        'can_delete_document'=> 'boolean',
    ];
}
