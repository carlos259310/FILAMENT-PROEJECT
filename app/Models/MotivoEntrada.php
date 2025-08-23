<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MotivoEntrada extends Model
{
    use HasFactory;

    protected $table = 'motivos_entrada';

    protected $fillable = [
        'nombre',
        'descripcion',
        // agrega aquí otros campos que tenga tu tabla
    ];

    public function motivo()
    {
        return $this->hasMany(MotivoEntrada::class, 'id_motivo');
    }
}
