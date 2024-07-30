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
        $search = $req->search;
        if (!empty($search)) {
            $users = DB::table("users")->select("users.*", "roles.name as role_name")
                    ->LeftJoin("roles", "users.role", "=", "roles.role_id")
                    ->where("users.username", "LIKE", "%" . $search . "%")
                    ->orWhere("users.name", "LIKE", "%" . $search . "%")
                    ->paginate(20);

        } else {
            $users = DB::table("users")->select("users.*", "roles.name as role_name")
                    ->LeftJoin("roles", "users.role", "=", "roles.role_id")
                    ->orderBy("users.name", "asc")->paginate(20);
        }

        return View::make('users')->with(compact("users"));
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
            return in_array($roles->role_id, [2,3]);
        });

        return response()->json($roles);
    }

    public function randomString($length = 8)
    {
        $code = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        return substr(str_shuffle(str_repeat($code, $length)), 0, $length);
    }

    public function assets(Request $req)
    {
        $search = $req->search;

        $assets = DB::table("assets")->select("assets.*")
                    ->where("NA", "N");
        if (!empty($search)) {
            $assets = $assets->where("assets.nama_asset", "LIKE", "%" . $search . "%")
                    ->orderBy("assets.nama_asset", "asc")            
                    ->paginate(25);

        } else {
            $assets = $assets->orderBy("assets.nama_asset", "asc")
                    ->paginate(25);
        }

        return View::make('assets')->with(compact("assets"));
    }

    public function asset_save(Request $req){
        $nama_asset = $req->nama_asset;

        $req->validate([
            'nama_asset'    => ['required']
            
        ],
        [
            'nama_asset.required'   => 'Nama Asset belum diisi!'
        ]);

        $data = [
            "nama_asset"    => $nama_asset,
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

        $santri = DB::table("santri")->select("santri.*");
        if (!empty($search)) {
            $santri = $santri->where("santri.nama_lengkap", "LIKE", "%" . $search . "%")
                    ->orWhere("santri.nis", "LIKE", "%" . $search . "%")
                    ->orWhere("santri.nik", "LIKE", "%" . $search . "%")
                    ->orWhere("santri.nisn", "LIKE", "%" . $search . "%")
                    ->orderBy("santri.nama_lengkap", "asc")            
                    ->paginate(25);

        } else {
            $santri = $santri->orderBy("santri.nama_lengkap", "asc")
                    ->paginate(25);
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
            "kelas_semester"        =>$req->kelas_semester,
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
            $pemasukan = DB::table('pemasukan')
                        ->select('pemasukan.*', 'pemasukan_kategori.kategori')
                        ->LeftJoin('pemasukan_kategori', 'pemasukan_kategori.kategori_id', '=', 'pemasukan.kategori_id');

            if(!empty($req->fd) && !empty($req->td)){
                $pemasukan = $pemasukan->whereBetween('tanggal', [$req->fd." 00:00:00", $req->td." 23:59:59"]);
            }

            $pemasukan = $pemasukan->orderBy("tanggal", "desc")->get();

            $data = [];
            foreach($pemasukan as $p){
                $data[] = [
                    'pemasukan_id'      => $p->pemasukan_id,
                    'tanggal'           => date("d/m/Y", strtotime($p->tanggal)),
                    'kategori'          => $p->kategori,
                    'jumlah'            => number_format($p->jumlah, 2, ",", ".")
                ];
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
            'jumlah'    => 'required|numeric'
            
        ],
        [
            'tanggal.date_format'   => 'Tanggal tidak sesuai format Y-m-d!',
            'kategori.required'     => 'Kategori belum dipilih!',
            'kategori.exists'       => 'Kategori tidak tersedia!',
            'jumlah.required'       => 'Jumlah belum diisi!',
            'jumlah.numeric'        => 'Jumlah harus angka!'
        ]);

        $data = [
            "tanggal"       => $req->tanggal,
            "kategori_id"   => $req->kategori,
            "jumlah"        => $req->jumlah,
            "user_buat"     => Auth::user()->username,
        ];

        $add = DB::table('pemasukan')->insertGetId($data);

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
            $pengeluaran = DB::table('pengeluaran')
                        ->select('pengeluaran.*', 'pengeluaran_kategori.*')
                        ->LeftJoin('pengeluaran_kategori', 'pengeluaran_kategori.kategori_id', '=', 'pengeluaran.kategori_id');

            if(!empty($req->fd) && !empty($req->td)){
                $pengeluaran = $pengeluaran->whereBetween('tanggal', [$req->fd." 00:00:00", $req->td." 23:59:59"]);
            }

            $pengeluaran = $pengeluaran->orderBy("tanggal", "desc")->get();

            $data = [];
            foreach($pengeluaran as $p){
                if($p->jenis == 0){
                    $jenis = "Dana Guru";
                } else {
                    $jenis = "Dana Operasional";
                }

                $data[] = [
                    'pengeluaran_id'    => $p->pengeluaran_id,
                    'tanggal'           => date("d/m/Y", strtotime($p->tanggal)),
                    'jenis'             => $jenis,
                    'kategori'          => $p->kategori,
                    'jumlah'            => number_format($p->jumlah, 2, ",", ".")
                ];
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
            'jumlah'    => 'required|numeric'
            
        ],
        [
            'tanggal.date_format'   => 'Tanggal tidak sesuai format Y-m-d!',
            'kategori.required'     => 'Kategori belum dipilih!',
            'kategori.exists'       => 'Kategori tidak tersedia!',
            'jumlah.required'       => 'Jumlah belum diisi!',
            'jumlah.numeric'        => 'Jumlah harus angka!'
        ]);

        $data = [
            "tanggal"       => $req->tanggal,
            "kategori_id"   => $req->kategori,
            "jumlah"        => $req->jumlah,
            "user_buat"     => Auth::user()->username,
        ];

        $add = DB::table('pengeluaran')->insertGetId($data);

        if($add){
            $req->session()->flash('success', "Pengeluaran berhasil ditambahkan.");
        } else {
            $req->session()->flash('error', "Pengeluaran gagal ditambahkan!");
        }
        
        return redirect()->back();
    }

}
