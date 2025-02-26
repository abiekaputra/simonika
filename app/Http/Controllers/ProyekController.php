namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProyekController extends Controller
{
    public function index()
    {
        // Pastikan view ini ada di resources/views/proyek.blade.php
        return view('proyek');
    }
}