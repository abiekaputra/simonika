<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use Illuminate\Http\Request;

class PenggunaController extends Controller
{
    public function index() {}
    public function create() {}
    public function store(Request $request) {}
    public function show(Pengguna $pengguna) {}
    public function edit(Pengguna $pengguna) {}
    public function update(Request $request, Pengguna $pengguna) {}
    public function destroy(Pengguna $pengguna) {}
}
