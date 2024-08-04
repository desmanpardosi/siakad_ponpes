<?php
/*
    Developer   : Desman Harianto Pardosi
    E-mail      : desman@pardosi.net
    Website     : www.dhp.co.id
*/

namespace App\Http\Controllers;

use App\Exports\ReportsExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Excel;
use PDF;
use Config;
use ZipArchive;
use Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use DataTables;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    
    public function home(Request $req){
        return View::make('home');
    }

    public function settings(Request $req){
        return View::make('settings');
    }

    public function settings_save(Request $req){
        $req->validate([
            'pass'              => 'required|min:6',
            'newpass'           => 'required|min:6|confirmed'
            
        ],
        [
            'pass.required'     => 'Password Lama belum diisi',
            'pass.min'          => 'Password Lama minimal 6 karakter!',
            'newpass.required'  => 'Password Baru belum diisi',
            'newpass.min'       => 'Password Baru minimal 6 karakter!',
            'newpass.confirmed' => 'Konfirmasi Password harus sama dengan Password Baru!',
        ]);

        if(Hash::check($req->pass, Auth::user()->password)){
            $data = [
                "password"      => Hash::make($req->newpass)
            ];

            $updatePass = DB::table('users')->where("id", Auth::user()->id)->update($data);

            if($updatePass){
                $req->session()->flash('success', "Password berhasil diganti.");
            } else {
                $req->session()->flash('success', "Password gagal diganti!");
            }

        } else {
            $req->session()->flash('error', "Password Lama salah!");
        }

        return redirect()->back();
    }

    public function users(Request $req)
    {
        $users = DB::table("users")->select("users.id", "users.username", "users.name", "users.role", "roles.name as role_name")
                ->LeftJoin("roles", "users.role", "=", "roles.role_id");

        if ($req->format == "json") {
            $role = $req->role;

            if(!empty($role)){
                $users = $users->where("role", $role);
            }

            $users = $users->get();

            return response()->json($users);
        } else {
            $search = $req->search;
            if (!empty($search)) {
                $users = $users->where("users.username", "LIKE", "%" . $search . "%")
                        ->orWhere("users.name", "LIKE", "%" . $search . "%")
                        ->paginate(20);

            } else {
                $users = $users->orderBy("users.name", "asc")->paginate(25);
            }

            return View::make('users')->with(compact("users"));
        }
    }

    public function users_save(Request $req){
        $id_user    = $req->id_user;
        $username   = strtolower($req->username);
        $name       = $req->name;
        $password   = $req->password;
        $role       = $req->role;

        $req->validate([
            'username'      => ['unique:users'],
            'name'          => ['required'],
            'role'          => ['required','exists:roles,role_id']
            
        ],
        [
            'username.unique'       => 'Username telah digunakan!',
            'name.required'         => 'Nama belum diisi!',
            'password.min'          => 'Password minimal 6 karakter!',
            'role.required'         => 'Role belum dipilih!',
            'role.exists'           => 'Role tidak berlaku!'
        ]);

        $data = [
            "name"      => $name,
            "role"      => $role,
        ];

        if(empty($id_user)){
            $req->validate([
                'username'      => ['required'],
                'password'      => ['required', 'min:6'],
                
            ],
            [
                'username.required'     => 'Username belum diisi!',
                'password.required'     => 'Password belum diisi!',
                'password.min'          => 'Password minimal 6 karakter!',
            ]);
            $data['username']   = $username;
            $data['password']   = Hash::make($password);
            $add = DB::table('users')->insertGetId($data);

            if($add){
                $req->session()->flash('success', "User berhasil ditambahkan.");
            } else {
                $req->session()->flash('error', "User gagal ditambahkan!");
            }
        } else {

            if(!empty($password)){
                $req->validate([
                    'password'      => ['min:6'],
                    
                ],
                [
                    'password.min'  => 'Password minimal 6 karakter!',
                ]);

                $data['password']   = Hash::make($password);
            }

            $countAdmin = DB::table('users')->where("role", 0)->get()->count();
            $userInfo   = DB::table('users')->where("id", $id_user)->first();

            if($userInfo->role == 0){
                if($countAdmin <= 1){
                    $req->session()->flash('error', "Minimal harus ada 1 Administrator!");
                    return redirect()->back();
                }
            }

            $edit = DB::table('users')->where("id", $id_user)->update($data);

            if($edit){
                $req->session()->flash('success', "User berhasil diubah.");
            } else {
                $req->session()->flash('error', "User gagal diubah!");
            }
        }
        
        return redirect()->back();
    }

    public function users_delete(Request $req)
    {
        $countAdmin = DB::table('users')->where("role", 0)->get()->count();
        $userInfo   = DB::table('users')->where("id", $req->delete_id)->first();

        if($userInfo->role == 0){
            if($countAdmin <= 1){
                $req->session()->flash('error', "Minimal harus ada 1 Administrator!");
                return redirect()->back();
            }
        }

        $del    = DB::table('users')->where("id", $req->delete_id)->delete();
        if ($del) {
            $req->session()->flash('success', "User berhasil dihapus.");
        } else {
            $req->session()->flash('error', "User gagal dihapus!");
        }

        return redirect()->back();
    }

    public function roles(Request $req)
    {
        $roles = DB::table("roles")->orderBy("role_id", "asc")->get();

        if(Auth::user()->role != 0){
            $roles = $roles->reject(function ($roles){
                return in_array($roles->role_id, [0,1]);
            });
        }

        $roles = $roles->reject(function ($roles){
            return in_array($roles->role_id, [3]);
        });

        return response()->json($roles);
    }

    public function randomString($length = 8)
    {
        $code = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        return substr(str_shuffle(str_repeat($code, $length)), 0, $length);
    }

    public function ruangan(Request $req)
    {

        $ruangan = DB::table("ruangan")->select("ruangan.*")->where("NA", "N");

        if ($req->format == "json") {
            $ruangan = $ruangan->get();
            return response()->json($ruangan);
        } else {
            $search = $req->search;

            if (!empty($search)) {
                $ruangan = $ruangan->where("ruangan.nama_ruangan", "LIKE", "%" . $search . "%")
                        ->orderBy("ruangan.nama_ruangan", "asc")            
                        ->paginate(25);

            } else {
                $ruangan = $ruangan->orderBy("ruangan.nama_ruangan", "asc")
                        ->paginate(25);
            }

            foreach($ruangan as $r){
                $r->jumlah_asset =  DB::table("assets")->where("assets.ruangan_id", $r->ruangan_id)->count();
            }

            return View::make('ruangan')->with(compact("ruangan"));
        }
    }

    public function ruangan_save(Request $req){
        $nama_ruangan = $req->nama_ruangan;

        $req->validate([
            'nama_ruangan'    => ['required']
            
        ],
        [
            'nama_ruangan.required'   => 'Nama Ruangan belum diisi!'
        ]);

        $data = [
            "nama_ruangan"    => $nama_ruangan,
            "user_buat"     => Auth::user()->username,
        ];

        $add = DB::table('ruangan')->insertGetId($data);

        if($add){
            $req->session()->flash('success', "Ruangan berhasil ditambahkan.");
        } else {
            $req->session()->flash('error', "Ruangan gagal ditambahkan!");
        }
        
        return redirect()->back();
    }

    public function ruangan_delete(Request $req)
    {
        $update = DB::table('ruangan')->where("ruangan_id", $req->delete_id)->update(["NA" => "Y"]);
        if ($update) {
            $req->session()->flash('success', "Ruangan berhasil dihapus.");
        } else {
            $req->session()->flash('error', "Ruangan gagal dihapus!");
        }

        return redirect()->back();
    }

    public function assets(Request $req)
    {
        $ruangan = $req->ruangan;

        $assets = DB::table("assets")->select("assets.*", "ruangan.nama_ruangan")
                    ->LeftJoin("ruangan", "ruangan.ruangan_id", "=", "assets.ruangan_id")
                    ->where("assets.NA", "N");
        if (!empty($ruangan)) {
            $assets = $assets->where("assets.ruangan_id", $ruangan)
                    ->orderBy("assets.asset_id", "desc")            
                    ->paginate(25);

        } else {
            $assets = $assets->orderBy("assets.asset_id", "desc")
                    ->paginate(25);
        }

        return View::make('assets')->with(compact("assets"));
    }

    public function asset_save(Request $req){

        $req->validate([
            'ruangan'       => 'required|exists:ruangan,ruangan_id',
            'nama_asset'    => 'required'
            
        ],
        [
            'ruangan.required'      => 'Ruangan belum dipilih!',
            'ruangan.exists'        => 'Ruangan tidak tersedia!',
            'nama_asset.required'   => 'Nama Asset belum diisi!'
        ]);

        $data = [
            "nama_asset"    => $req->nama_asset,
            "ruangan_id"    => $req->ruangan,
            "user_buat"     => Auth::user()->username,
        ];

        $add = DB::table('assets')->insertGetId($data);

        if($add){
            $req->session()->flash('success', "Asset berhasil ditambahkan.");
        } else {
            $req->session()->flash('error', "Asset gagal ditambahkan!");
        }
        
        return redirect()->back();
    }

    public function asset_delete(Request $req)
    {
        $update = DB::table('assets')->where("asset_id", $req->delete_id)->update(["NA" => "Y"]);
        if ($update) {
            $req->session()->flash('success', "Asset berhasil dihapus.");
        } else {
            $req->session()->flash('error', "Asset gagal dihapus!");
        }

        return redirect()->back();
    }

    public function guru(Request $req)
    {
        $search = $req->search;

        $guru = DB::table("staff")->select("staff.*")->where("staff.staff_type", 0);
        if (!empty($search)) {
            $guru = $guru->where("staff.nama_lengkap", "LIKE", "%" . $search . "%")
                    ->orWhere("staff.nik", "LIKE", "%" . $search . "%")
                    ->orWhere("staff.no_hp", "LIKE", "%" . $search . "%")
                    ->orderBy("staff.nama_lengkap", "asc")            
                    ->paginate(25);

        } else {
            $guru = $guru->orderBy("staff.nama_lengkap", "asc")
                    ->paginate(25);
        }

        return View::make('guru')->with(compact("guru"));
    }

    public function staff(Request $req)
    {
        $search = $req->search;

        $staff = DB::table("staff")->select("staff.*")->where("staff.staff_type", 1);
        if (!empty($search)) {
            $staff = $staff->where("staff.nama_lengkap", "LIKE", "%" . $search . "%")
                    ->orWhere("staff.nik", "LIKE", "%" . $search . "%")
                    ->orWhere("staff.no_hp", "LIKE", "%" . $search . "%")
                    ->orderBy("staff.nama_lengkap", "asc")            
                    ->paginate(25);

        } else {
            $staff = $staff->orderBy("staff.nama_lengkap", "asc")
                    ->paginate(25);
        }

        return View::make('staff')->with(compact("staff"));
    }

    public function staff_save(Request $req){
        $req->validate([
            'nama_lengkap'      => 'required',
            'nik'               => 'required|numeric|digits:16',
            'no_hp'             => 'nullable|numeric',
            'tgl_lahir'         => 'nullable|date_format:Y-m-d',
            'mulai_mengajar'    => 'nullable|digits:4',
        ],
        [
            'nama_lengkap.required'     => 'Nama Lengkap belum diisi!',
            'nik.required'              => 'NIK belum diisi!',
            'nik.numeric'               => 'NIK harus berupa angka!',
            'nik.digits'                => 'NIK harus 16 digit angka!',
            'no_hp.numeric'             => 'No. HP. harus berupa angka!',
            'tgl_lahir.numeric'         => 'Tgl. Lahir tidak sesuai format (Y-m-d)',
            'mulai_mengajar.digits'     => 'Tahun Mulai Mengajar harus 4 digit angka!',
        ]);

        $data = [
            "staff_type"            =>$req->staff_type,
            "nama_lengkap"          =>$req->nama_lengkap,
            "nik"                   =>$req->nik,
            "tempat_lahir"          =>$req->tempat_lahir,
            "tgl_lahir"             =>$req->tgl_lahir,
            "alamat"                =>$req->alamat,
            "no_hp"                 =>$req->no_hp,
            "pendidikan_terakhir"   =>$req->pendidikan_terakhir,
            "bidang_mengajar"       =>$req->bidang_mengajar,
            "no_sk"                 =>$req->no_sk,
            "mulai_mengajar"        =>$req->mulai_mengajar,
            "status"                =>$req->status,
        ];

        if(empty($req->staff_id)){
            $add = DB::table('staff')->insertGetId($data);

            if($add){
                $req->session()->flash('success', "Guru / Staff berhasil ditambahkan.");
            } else {
                $req->session()->flash('error', "Guru / Staff gagal ditambahkan!");
            }
            
        } else {
            $update = DB::table('staff')->where("staff_id", $req->staff_id)->update($data);

            if($update){
                $req->session()->flash('success', "Data berhasil diganti.");
            } else {
                $req->session()->flash('error', "Data gagal diganti!");
            }
        }

        return redirect()->back();
    }

    public function staff_delete(Request $req)
    {
        $del    = DB::table('staff')->where("staff_id", $req->delete_id)->delete();
        if ($del) {
            $req->session()->flash('success', "Guru / Staff berhasil dihapus.");
        } else {
            $req->session()->flash('error', "Guru / Staff gagal dihapus!");
        }

        return redirect()->back();
    }

    public function santri(Request $req)
    {
        $search = $req->search;

        $santri = DB::table("santri")->select("santri.*", "kelas.kelas_semester")
                    ->LeftJoin("kelas", "kelas.kelas_id", "=", "santri.kelas_id");
        if (!empty($search)) {
            $santri = $santri->where("santri.nama_lengkap", "LIKE", "%" . $search . "%")
                    ->orWhere("santri.nis", "LIKE", "%" . $search . "%")
                    ->orWhere("santri.nik", "LIKE", "%" . $search . "%")
                    ->orWhere("santri.nisn", "LIKE", "%" . $search . "%")
                    ->orderBy("santri.nama_lengkap", "asc")            
                    ->paginate(25);

        } else {
            $santri = $santri->orderBy("santri.nama_lengkap", "asc");

            if(!empty($req->kelas)){
                $santri = $santri->where("santri.kelas_id", $req->kelas);
            }

            $santri = $santri->paginate(25);
        }

        return View::make('santri')->with(compact("santri"));
    }

    public function santri_save(Request $req){
        $req->validate([
            'nis'               => 'required|numeric',
            'nama_lengkap'      => 'required',
            'nik'               => 'required|numeric|digits:16',
            'no_hp'             => 'nullable|numeric',
            'tgl_lahir'         => 'nullable|date_format:Y-m-d',
            'nohp_ortu'         => 'nullable|numeric',
        ],
        [
            'nama_lengkap.required'     => 'Nama Lengkap belum diisi!',
            'nik.required'              => 'NIK belum diisi!',
            'nik.numeric'               => 'NIK harus berupa angka!',
            'nik.digits'                => 'NIK harus 16 digit angka!',
            'no_hp.numeric'             => 'No. HP. harus berupa angka!',
            'tgl_lahir.numeric'         => 'Tgl. Lahir tidak sesuai format (Y-m-d)',
            'no_hp.numeric'             => 'No. HP. Orang Tua harus berupa angka!',
        ]);

        $data = [
            "nis"                   =>$req->nis,
            "nama_lengkap"          =>$req->nama_lengkap,
            "nik"                   =>$req->nik,
            "no_kk"                 =>$req->no_kk,
            "tempat_lahir"          =>$req->tempat_lahir,
            "tgl_lahir"             =>$req->tgl_lahir,
            "alamat"                =>$req->alamat,
            "no_hp"                 =>$req->no_hp,
            "pendidikan_formal"     =>$req->pendidikan_formal,
            "kelas_id"              =>$req->kelas_semester,
            "nisn"                  =>$req->nisn,
            "program_ponpes"        =>$req->program_ponpes,
            "riwayat_mondok"        =>$req->riwayat_mondok,
            "nama_ayah"             =>$req->nama_ayah,
            "nama_ibu"              =>$req->nama_ibu,
            "nohp_ortu"             =>$req->nohp_ortu,
            "alamat_ortu"           =>$req->alamat_ortu,
        ];

        if(empty($req->santri_id)){
            $add = DB::table('santri')->insertGetId($data);

            if($add){
                $checkUser = DB::table('users')->where("username", $req->nis)->get();

                if(count($checkUser) == 0){
                    DB::table('users')->insertGetId(["username" => $req->nis, "name" => $req->nama_lengkap, "password" => Hash::make($req->nis), "role" => 3]);
                } else {
                    DB::table('users')->where("username", $req->nis)->update(["username" => $req->nis, "name" => $req->nama_lengkap, "password" => Hash::make($req->nis), "role" => 3]);
                }
                
                $req->session()->flash('success', "Santri / Santri Wati berhasil ditambahkan.");
            } else {
                $req->session()->flash('error', "Santri / Santri Wati gagal ditambahkan!");
            }
            
        } else {
            $update = DB::table('santri')->where("santri_id", $req->santri_id)->update($data);

            if($update){
                $req->session()->flash('success', "Data berhasil diganti.");
            } else {
                $req->session()->flash('error', "Data gagal diganti!");
            }
        }

        return redirect()->back();
    }

    public function santri_delete(Request $req)
    {
        $del    = DB::table('santri')->where("santri_id", $req->delete_id)->delete();
        if ($del) {
            $req->session()->flash('success', "Santri / Santri Wati berhasil dihapus.");
        } else {
            $req->session()->flash('error', "Santri / Santri Wati gagal dihapus!");
        }

        return redirect()->back();
    }

    public function pemasukan_kategori(Request $req)
    {

        $kategori = DB::table("pemasukan_kategori")->select("pemasukan_kategori.*")
                    ->where("NA", "N");

        if ($req->format == "json") {
            $kategori = $kategori->get();
            return response()->json($kategori);
        } else {
            $search = $req->search;

            if (!empty($search)) {
                $kategori = $kategori->where("pemasukan_kategori.kategori", "LIKE", "%" . $search . "%")
                        ->orderBy("pemasukan_kategori.kategori", "asc")            
                        ->paginate(25);

            } else {
                $kategori = $kategori->orderBy("pemasukan_kategori.kategori", "asc")
                        ->paginate(25);
            }

            return View::make('pemasukan_kategori')->with(compact("kategori"));
        }
    }

    public function pemasukan_kategori_save(Request $req){
        $req->validate([
            'kategori'    => 'required|unique:pemasukan_kategori,kategori'
            
        ],
        [
            'kategori.required'     => 'Kategori belum diisi!',
            'kategori.unique'       => 'Kategori sudah ada!',
        ]);

        $data = [
            "kategori"      => $req->kategori,
            "user_buat"     => Auth::user()->username,
        ];

        $add = DB::table('pemasukan_kategori')->insertGetId($data);

        if($add){
            $req->session()->flash('success', "Kategori Pemasukan berhasil ditambahkan.");
        } else {
            $req->session()->flash('error', "Kategori Pemasukan gagal ditambahkan!");
        }
        
        return redirect()->back();
    }

    public function pemasukan_kategori_delete(Request $req)
    {
        $update = DB::table('pemasukan_kategori')->where("kategori_id", $req->delete_id)->update(["NA" => "Y"]);
        if ($update) {
            $req->session()->flash('success', "Kategori Pemasukan berhasil dihapus.");
        } else {
            $req->session()->flash('error', "Kategori Pemasukan gagal dihapus!");
        }

        return redirect()->back();
    }

    public function pemasukan(Request $req)
    {
        if ($req->ajax()) {
            $pemasukan = DB::table('keuangan')
                        ->select('keuangan.*', 'pemasukan_kategori.kategori')
                        ->LeftJoin('pemasukan_kategori', 'pemasukan_kategori.kategori_id', '=', 'keuangan.kategori_id')
                        ->where("keuangan.jenis", 0);

            if(!empty($req->fd) && !empty($req->td)){
                $pemasukan = $pemasukan->whereBetween('tanggal', [$req->fd." 00:00:00", $req->td." 23:59:59"]);
            }

            $pemasukan = $pemasukan->orderBy("tanggal", "desc")->get();

            $data   = [];
            $no     = 1;
            foreach($pemasukan as $p){
                $data[] = [
                    'no'                => $no,
                    'pemasukan_id'      => $p->keuangan_id,
                    'tanggal'           => date("d/m/Y", strtotime($p->tanggal)),
                    'kategori'          => $p->kategori,
                    'nominal'           => number_format($p->nominal, 0, ",", ".")
                ];
                $no++;
            }

            return DataTables::of($data)
                    ->addIndexColumn()
                    ->make(true);
        }

        return View::make('pemasukan');
    }

    public function pemasukan_save(Request $req){
        $req->validate([
            'tanggal'   => 'required|date_format:Y-m-d',
            'kategori'  => 'required|exists:pemasukan_kategori,kategori_id',
            'nominal'   => 'required|numeric'
            
        ],
        [
            'tanggal.date_format'   => 'Tanggal tidak sesuai format Y-m-d!',
            'kategori.required'     => 'Kategori belum dipilih!',
            'kategori.exists'       => 'Kategori tidak tersedia!',
            'nominal.required'      => 'Jumlah belum diisi!',
            'nominal.numeric'       => 'Jumlah harus angka!'
        ]);

        $data = [
            "tanggal"       => $req->tanggal,
            "jenis"         => 0,
            "kategori_id"   => $req->kategori,
            "nominal"       => $req->nominal,
            "user_buat"     => Auth::user()->username,
        ];

        $add = DB::table('keuangan')->insertGetId($data);

        if($add){
            $req->session()->flash('success', "Pemasukan berhasil ditambahkan.");
        } else {
            $req->session()->flash('error', "Pemasukan gagal ditambahkan!");
        }
        
        return redirect()->back();
    }

    public function pengeluaran_kategori(Request $req)
    {
        $kategori = DB::table("pengeluaran_kategori")->select("pengeluaran_kategori.*")
                    ->where("NA", "N");

        if ($req->format == "json") {
            $kategori = $kategori->get();
            return response()->json($kategori);
        } else {
            $search = $req->search;

            if (!empty($search)) {
                $kategori = $kategori->where("pengeluaran_kategori.kategori", "LIKE", "%" . $search . "%")
                        ->orderBy("pengeluaran_kategori.jenis", "asc")
                        ->orderBy("pengeluaran_kategori.kategori", "asc")
                        ->paginate(25);

            } else {
                $kategori = $kategori->orderBy("pengeluaran_kategori.jenis", "asc")
                        ->orderBy("pengeluaran_kategori.kategori", "asc")
                        ->paginate(25);
            }

            return View::make('pengeluaran_kategori')->with(compact("kategori"));
        }
    }

    public function pengeluaran_kategori_save(Request $req){
        $req->validate([
            'kategori'    => 'required|unique:pengeluaran_kategori,kategori'
            
        ],
        [
            'kategori.required'     => 'Kategori belum diisi!',
            'kategori.unique'       => 'Kategori sudah ada!',
        ]);

        $data = [
            "jenis"         => $req->jenis,
            "kategori"      => $req->kategori,
            "user_buat"     => Auth::user()->username,
        ];

        $add = DB::table('pengeluaran_kategori')->insertGetId($data);

        if($add){
            $req->session()->flash('success', "Kategori Pengeluaran berhasil ditambahkan.");
        } else {
            $req->session()->flash('error', "Kategori Pengeluaran gagal ditambahkan!");
        }
        
        return redirect()->back();
    }

    public function pengeluaran_kategori_delete(Request $req)
    {
        $update = DB::table('pengeluaran_kategori')->where("kategori_id", $req->delete_id)->update(["NA" => "Y"]);
        if ($update) {
            $req->session()->flash('success', "Kategori Pengeluaran berhasil dihapus.");
        } else {
            $req->session()->flash('error', "Kategori Pengeluaran gagal dihapus!");
        }

        return redirect()->back();
    }

    public function pengeluaran(Request $req)
    {
        if ($req->ajax()) {
            $pengeluaran = DB::table('keuangan')
                        ->select('keuangan.*', 'pengeluaran_kategori.*')
                        ->LeftJoin('pengeluaran_kategori', 'pengeluaran_kategori.kategori_id', '=', 'keuangan.kategori_id')
                        ->where("keuangan.jenis", 1);

            if(!empty($req->fd) && !empty($req->td)){
                $pengeluaran = $pengeluaran->whereBetween('tanggal', [$req->fd." 00:00:00", $req->td." 23:59:59"]);
            }

            $pengeluaran = $pengeluaran->orderBy("tanggal", "desc")->get();

            $data   = [];
            $no     = 1;
            foreach($pengeluaran as $p){
                $data[] = [
                    'no'                => $no,
                    'pengeluaran_id'    => $p->keuangan_id,
                    'tanggal'           => date("d/m/Y", strtotime($p->tanggal)),
                    'kategori'          => $p->kategori,
                    'nominal'           => number_format($p->nominal, 0, ",", ".")
                ];
                $no++;
            }

            return DataTables::of($data)
                    ->addIndexColumn()
                    ->make(true);
        }

        return View::make('pengeluaran');
    }

    public function pengeluaran_save(Request $req){
        $req->validate([
            'tanggal'   => 'required|date_format:Y-m-d',
            'kategori'  => 'required|exists:pengeluaran_kategori,kategori_id',
            'nominal'   => 'required|numeric'
            
        ],
        [
            'tanggal.date_format'   => 'Tanggal tidak sesuai format Y-m-d!',
            'kategori.required'     => 'Kategori belum dipilih!',
            'kategori.exists'       => 'Kategori tidak tersedia!',
            'nominal.required'      => 'Jumlah belum diisi!',
            'nominal.numeric'       => 'Jumlah harus angka!'
        ]);

        $data = [
            "tanggal"       => $req->tanggal,
            "jenis"         => 1,
            "kategori_id"   => $req->kategori,
            "nominal"       => $req->nominal,
            "user_buat"     => Auth::user()->username,
        ];

        $add = DB::table('keuangan')->insertGetId($data);

        if($add){
            $req->session()->flash('success', "Pengeluaran berhasil ditambahkan.");
        } else {
            $req->session()->flash('error', "Pengeluaran gagal ditambahkan!");
        }
        
        return redirect()->back();
    }

    public function laporan_keuangan(Request $req)
    {
        if ($req->ajax()) {
            $keuangan = DB::table('keuangan')->select('keuangan.*');

            if(!empty($req->fd) && !empty($req->td)){
                $keuangan = $keuangan->whereBetween('tanggal', [$req->fd." 00:00:00", $req->td." 23:59:59"]);
            }

            $keuangan = $keuangan->orderBy("tanggal", "desc")->get();

            $data   = [];
            $no     = 1;
            foreach($keuangan as $k){
                if($k->jenis == 0){
                    $pemasukan      = number_format($k->nominal, 0, ",", ".");
                    $pengeluaran    = null;
                    $kategori = DB::table('pemasukan_kategori')->select('pemasukan_kategori.kategori')->first();
                    if(!empty($kategori)){
                        $kategori = $kategori->kategori;
                    } else {
                        $kategori = "-";
                    }
                } else {
                    $pemasukan      = null;
                    $pengeluaran    = number_format($k->nominal, 0, ",", ".");
                    $kategori = DB::table('pengeluaran_kategori')->select('pengeluaran_kategori.kategori')->first();
                    if(!empty($kategori)){
                        $kategori = $kategori->kategori;
                    } else {
                        $kategori = "-";
                    }
                }

                $data[] = [
                    'no'            => $no,
                    'tanggal'       => date("d/m/Y", strtotime($k->tanggal)),
                    'kategori'      => $kategori,
                    'pemasukan'     => $pemasukan,
                    'pengeluaran'   => $pengeluaran
                ];

                $no++;
            }

            return DataTables::of($data)
                    ->addIndexColumn()
                    ->make(true);
        }

        return View::make('laporan_keuangan');
    }

    public function kelas(Request $req)
    {

        $kelas = DB::table("kelas")->select("kelas.*")->where("NA", "N");

        if ($req->format == "json") {
            $kelas = $kelas->get();
            return response()->json($kelas);
        } else {
            $search = $req->search;

            if (!empty($search)) {
                $kelas = $kelas->where("kelas.kelas_semester", "LIKE", "%" . $search . "%")
                        ->orderBy("kelas.kelas_semester", "asc")            
                        ->paginate(25);

            } else {
                $kelas = $kelas->orderBy("kelas.kelas_semester", "asc")
                        ->paginate(25);
            }

            foreach($kelas as $r){
                $r->jumlah_santri =  DB::table("santri")->where("santri.kelas_id", $r->kelas_id)->count();
            }

            return View::make('kelas')->with(compact("kelas"));
        }
    }

    public function kelas_save(Request $req){
        $req->validate([
            'kelas_semester'    => 'required|unique:kelas,kelas_semester'
            
        ],
        [
            'kelas_semester.required'   => 'Kelas / Semester belum diisi!',
            'kelas_semester.unique'     => 'Kelas / Semester sudah ada!'
        ]);

        $data = [
            "kelas_semester"    => $req->kelas_semester,
            "user_buat"     => Auth::user()->username,
        ];

        $add = DB::table('kelas')->insertGetId($data);

        if($add){
            $req->session()->flash('success', "Kelas berhasil ditambahkan.");
        } else {
            $req->session()->flash('error', "Kelas gagal ditambahkan!");
        }
        
        return redirect()->back();
    }

    public function kelas_delete(Request $req)
    {
        $update = DB::table('kelas')->where("kelas_id", $req->delete_id)->update(["NA" => "Y"]);
        if ($update) {
            $req->session()->flash('success', "Kelas berhasil dihapus.");
        } else {
            $req->session()->flash('error', "Kelas gagal dihapus!");
        }

        return redirect()->back();
    }

    public function mapel(Request $req)
    {

        $mapel = DB::table("mapel")->select("mapel.*", "kelas.kelas_semester", "users.name as guru")
                ->LeftJoin("kelas", "kelas.kelas_id", "=", "mapel.kelas_id")
                ->LeftJoin("users", "users.id", "=", "mapel.guru")
                ->where("mapel.NA", "N");

        if ($req->format == "json") {
            $mapel = $mapel->get();
            return response()->json($mapel);
        } else {
            $search = $req->search;

            if (!empty($search)) {
                $mapel = $mapel->where("mapel.mapel", "LIKE", "%" . $search . "%")
                        ->orderBy("mapel.mapel", "asc")            
                        ->paginate(25);

            } else {
                $mapel = $mapel->orderBy("mapel.mapel", "asc")
                        ->paginate(25);
            }

            return View::make('mapel')->with(compact("mapel"));
        }
    }

    public function mapel_save(Request $req){
        $req->id = $req->guru;
        $req->validate([
            'mapel'     => 'required',
            'kelas'     => 'required|exists:kelas,kelas_id',
            'guru'        => 'required|exists:users,id,role,2'
        ],
        [
            'mapel.required'    => 'Mata Pelajaran belum diisi!',
            'kelas.required'    => 'Kelas / Semester belum dipilih!',
            'kelas.exists'      => 'Kelas / Semester tidak tersedia!',
            'guru.required'     => 'Guru belum dipilih!',
            'guru.exists'       => 'Guru tidak tersedia!',
        ]);

        $data = [
            "mapel"         => $req->mapel,
            "kelas_id"      => $req->kelas,
            "guru"          => $req->guru,
            "user_buat"     => Auth::user()->username,
        ];

        $add = DB::table('mapel')->insertGetId($data);

        if($add){
            $req->session()->flash('success', "Mata Pelajaran berhasil ditambahkan.");
        } else {
            $req->session()->flash('error', "Mata Pelajaran gagal ditambahkan!");
        }
        
        return redirect()->back();
    }

    public function mapel_delete(Request $req)
    {
        $update = DB::table('mapel')->where("mapel_id", $req->delete_id)->update(["NA" => "Y"]);
        if ($update) {
            $req->session()->flash('success', "Mata Pelajaran berhasil dihapus.");
        } else {
            $req->session()->flash('error', "Mata Pelajaran gagal dihapus!");
        }

        return redirect()->back();
    }

    public function presensi(Request $req)
    {

        $mapel = DB::table("mapel")->select("mapel.*", "kelas.kelas_semester", "users.name as guru")
                ->LeftJoin("kelas", "kelas.kelas_id", "=", "mapel.kelas_id")
                ->LeftJoin("users", "users.id", "=", "mapel.guru")
                ->where("mapel.NA", "N");

        if ($req->format == "json") {
            $mapel = $mapel->get();
            return response()->json($mapel);
        } else {
            $search = $req->search;

            if (!empty($search)) {
                $mapel = $mapel->where("mapel.mapel", "LIKE", "%" . $search . "%")
                        ->orderBy("mapel.mapel", "asc")            
                        ->paginate(25);

            } else {
                $mapel = $mapel->orderBy("mapel.mapel", "asc")
                        ->paginate(25);
            }

            return View::make('mapel')->with(compact("mapel"));
        }
    }

    public function presensi_save(Request $req){
        $req->id = $req->guru;
        $req->validate([
            'mapel'     => 'required|exists:mapel,mapel_id',
            'santri'    => 'required|exists:santri,santri_id',
            'tanggal'   => 'required'
        ],
        [
            'mapel.required'    => 'Mata Pelajaran belum dipilih!',
            'mapel.exists'      => 'Mata Pelajaran tidak tersedia!',
            'santri.required'   => 'Santri / Santri Wati belum dipilih!',
            'santri.exists'     => 'Santri / Santri Wati tidak tersedia!',
        ]);

        $data = [
            "mapel"         => $req->mapel,
            "kelas_id"      => $req->kelas,
            "guru"          => $req->guru,
            "user_buat"     => Auth::user()->username,
        ];

        $add = DB::table('mapel')->insertGetId($data);

        if($add){
            $req->session()->flash('success', "Mata Pelajaran berhasil ditambahkan.");
        } else {
            $req->session()->flash('error', "Mata Pelajaran gagal ditambahkan!");
        }
        
        return redirect()->back();
    }

    public function presensi_delete(Request $req)
    {
        $update = DB::table('absensi')->where("absensi_id", $req->delete_id)->update(["NA" => "Y"]);
        if ($update) {
            $req->session()->flash('success', "Absensi berhasil dihapus.");
        } else {
            $req->session()->flash('error', "Absensi gagal dihapus!");
        }

        return redirect()->back();
    }

    public function jp(Request $req)
    {
        $nama_hari = ["Senin", "Selasa", "Rabu", "Kamis", "Jum'at", "Sabtu", "Minggu"];

        $jp = DB::table("jam_pelajaran")->select("jam_pelajaran.*")
                ->where("jam_pelajaran.NA", "N");

        if ($req->format == "json") {
            $hari = $req->hari;

            if(!empty($hari)){
                $jp = $jp->where("hari", $hari);
            }

            $jp = $jp->get();

            foreach($jp as $j){
                $j->nama_hari = $nama_hari[$j->hari];
            }

            return response()->json($jp);
        } else {
            $jp = $jp->orderBy("jam_pelajaran.hari", "asc")->orderBy("jam_pelajaran.jam", "asc")->paginate(25);

            foreach($jp as $j){
                $j->nama_hari = $nama_hari[$j->hari];
            }

            return View::make('jp')->with(compact("jp"));
        }
    }

    public function jp_save(Request $req){
        $req->validate([
            'hari'      => 'required|min:0|max:6',
            'jam'       => 'required',
        ],
        [
            'hari.required'     => 'Hari belum dipilih!',
            'hari.min'          => 'Hari tidak berlaku!',
            'hari.max'          => 'Hari tidak berlaku!'
        ]);

        $data = [
            "hari"      => $req->hari,
            "jam"       => $req->jam,
        ];

        $add = DB::table('jam_pelajaran')->insertGetId($data);

        if($add){
            $req->session()->flash('success', "Jam Pelajaran berhasil ditambahkan.");
        } else {
            $req->session()->flash('error', "Jam Pelajaran gagal ditambahkan!");
        }
        
        return redirect()->back();
    }

    public function jp_delete(Request $req)
    {
        $update = DB::table('jam_pelajaran')->where("jp_id", $req->delete_id)->update(["NA" => "Y"]);
        if ($update) {
            $req->session()->flash('success', "Jam Pelajaran berhasil dihapus.");
        } else {
            $req->session()->flash('error', "Jam Pelajaran gagal dihapus!");
        }

        return redirect()->back();
    }

    public function jadwal(Request $req)
    {
        $nama_hari = ["Senin", "Selasa", "Rabu", "Kamis", "Jum'at", "Sabtu", "Minggu"];

        $jadwal = DB::table("jadwal_pelajaran")->select("jadwal_pelajaran.*", "jam_pelajaran.*", "users.name", "mapel.*", "kelas.kelas_semester")
                ->LeftJoin("jam_pelajaran", "jam_pelajaran.jp_id", "=", "jadwal_pelajaran.jp_id")
                ->LeftJoin("mapel", "mapel.mapel_id", "=", "jadwal_pelajaran.mapel_id")
                ->LeftJoin("kelas", "kelas.kelas_id", "=", "mapel.kelas_id")
                ->LeftJoin("users", "users.id", "=", "mapel.guru")
                ->where("jadwal_pelajaran.NA", "N");

        $jadwal = $jadwal->orderBy("jam_pelajaran.hari", "asc")->orderBy("mapel.mapel", "asc")->paginate(25);

        foreach($jadwal as $j){
            $j->nama_hari = $nama_hari[$j->hari];
        }

        return View::make('jadwal')->with(compact("jadwal"));
    }

    public function jadwal_save(Request $req){
        $req->validate([
            'jp'        => 'required|exists:jam_pelajaran,jp_id',
            'mapel'     => 'required|exists:mapel,mapel_id',
        ],
        [
            'jp.required'       => 'Jam Pelajaran belum dipilih!',
            'jp.exists'         => 'Jam Pelajaran tidak ditemukan!',
            'mapel.required'    => 'Mata Pelajaran belum dipilih!',
            'mapel.exists'      => 'Mata Pelajaran tidak ditemukan!',
        ]);

        $exists = DB::table("jadwal_pelajaran")->where([["jp_id", $req->jp], ["mapel_id", $req->mapel]])->get()->count();

        if($exists == 0){
            $data = [
                "jp_id"     => $req->jp,
                "mapel_id"  => $req->mapel,
            ];

            $add = DB::table('jadwal_pelajaran')->insertGetId($data);

            if($add){
                $req->session()->flash('success', "Jadwal Pelajaran berhasil ditambahkan.");
            } else {
                $req->session()->flash('error', "Jadwal Pelajaran gagal ditambahkan!");
            }
        } else {
            $req->session()->flash('error', "Jadwal Pelajaran sudah ada!");
        }

        return redirect()->back();
    }

    public function jadwal_delete(Request $req)
    {
        $update = DB::table('jadwal_pelajaran')->where("jadwal_id", $req->delete_id)->update(["NA" => "Y"]);
        if ($update) {
            $req->session()->flash('success', "Jadwal Pelajaran berhasil dihapus.");
        } else {
            $req->session()->flash('error', "Jadwal Pelajaran gagal dihapus!");
        }

        return redirect()->back();
    }

}
