namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashFlow extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'amount', 'description', 'date'];

    protected $casts = [
        'date' => 'date',
    ];
}