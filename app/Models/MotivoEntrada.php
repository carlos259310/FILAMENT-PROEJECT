<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MotivoEntrada extends Model
{
    use HasFactory;

    protected $table = 'motivos_entrada';
    public $timestamps = false;
    protected $fillable = [
        'nombre'

    ];


}
