<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $table = 'reports'; // Define o nome da tabela

    // Campos que podem ser preenchidos em massa
    protected $fillable = ['report_type', 'report_data'];
}
