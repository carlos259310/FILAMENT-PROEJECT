<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MotivoSalida extends Model
{
    use HasFactory;

    protected $table = 'motivos_salida';
    public $timestamps = false;
    protected $fillable = [
        'nombre'

    ];

    public function motivo()
    {
        return $this->hasMany(MotivoSalida::class, 'id_motivo');
    }
}
