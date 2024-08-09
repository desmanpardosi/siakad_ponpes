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
use Carbon\Carbon;


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
        $pengumuman = DB::table('pengumuman')->where("NA", "N")->get();
        return View::make('home')->with(compact("pengumuman"));
    }

    public function pengumuman(Request $req){
        $pengumuman = DB::table('pengumuman')->select("pengumuman.*")->where("NA", "N")->paginate(25);
        return View::make('pengumuman')->with(compact("pengumuman"));
    }

    public function pengumuman_save(Request $req){
        $req->validate([
            'judul'             => 'required',
            'deskripsi'         => 'required'
            
        ],
        [
            'judul.required'        => 'Judul belum diisi',
            'deskripsi.required'    => 'Pengumuman belum diisi',
        ]);

        $data = [
            "judul"         => $req->judul,
            "deskripsi"     => $req->deskripsi,
            "user_buat"     => Auth::user()->username
        ];

        $add = DB::table('pengumuman')->insertGetId($data);

        if($add){
            $req->session()->flash('success', "Pengumuman berhasil dibuat.");
        } else {
            $req->session()->flash('error', "Pengumuman gagal dibuat!");
        }


        return redirect()->back();
    }

    public function pengumuman_delete(Request $req)
    {
        $update = DB::table('pengumuman')->where("pengumuman_id", $req->delete_id)->update(["NA" => "Y"]);
        if ($update) {
            $req->session()->flash('success', "Pengumuman berhasil dihapus.");
        } else {
            $req->session()->flash('error', "Pengumuman gagal dihapus!");
        }

        return redirect()->back();
    }

    public function settings(Request $req){
        if(Auth::user()->role == 0 || Auth::user()->role == 1){
            $nama_yayasan       = DB::table("settings")->where("setting_name", "nama_yayasan")->first()->setting_value;
            $ketua_yayasan      = DB::table("settings")->where("setting_name", "ketua_yayasan")->first()->setting_value;
            $kota               = DB::table("settings")->where("setting_name", "kota")->first()->setting_value;

            return View::make('settings')->with(compact("nama_yayasan","ketua_yayasan","kota"));
        } else {
            return View::make('settings');
        }
    }

    public function settings_save(Request $req){
        if(empty($req->type) || $req->type == 0){
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
                    $req->session()->flash('error', "Password gagal diganti!");
                }

            } else {
                $req->session()->flash('error', "Password Lama salah!");
            }
        } else {
            if(Auth::user()->role == 0 || Auth::user()->role == 1){
                $req->validate([
                    'nama_yayasan'  => 'required',
                    'ketua_yayasan' => 'required',
                    'kota'          => 'required',
                    
                ],
                [
                    'nama_yayasan.required'     => 'Nama Yayasan belum diisi!',
                    'ketua_yayasan.required'    => 'Nama Ketua Yayasan belum diisi!',
                    'kota.required'             => 'Kota belum diisi!',
                ]);

                $data = [
                    "nama_yayasan"          => $req->nama_yayasan,
                    "ketua_yayasan"         => $req->ketua_yayasan,
                    "kota"                  => $req->kota,
                    
                ];
        
                foreach($data as $s => $val){
                    $data = [
                        "setting_value"=> $val,
                        
                    ];
                    $update = DB::table('settings')->where("setting_name", $s)->update($data);
                }
                
            } else {
                $req->session()->flash('error', "Anda tidak memiliki hak akses untuk mengubah pengaturan!");
            }
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

        $ruangan = DB::table("ruangan")->select("ruangan.*")->where("NA", "N")->get();

        if ($req->format == "json") {
            return response()->json($ruangan);
        } else {
            if ($req->ajax()) {
                $data   = [];
                $no     = 1;
                foreach($ruangan as $d){
                    $data[] = [
                        'ruangan_id'        => $d->ruangan_id,
                        'no'                => $no,
                        'nama_ruangan'      => $d->nama_ruangan,
                        'tgl_buat'          => date("d/m/Y", strtotime($d->tgl_buat)),
                        'user_buat'         => $d->user_buat,
                        'jumlah_asset'      => DB::table("assets")->where("ruangan_id", $d->ruangan_id)->get()->sum("jumlah")
                    ];
                    $no++;
                }
    
                return DataTables::of($data)
                        ->addIndexColumn()
                        ->addColumn('jumlah_asset', function($row){
                            $btn    = "<center><a href=\"".route('master.assets')."?ruangan=".$row['ruangan_id']."\">".$row['jumlah_asset']."</a></center>";
                            return $btn;
                        })
                        ->addColumn('action', function($row){
                            $btn    = '<center><button title="Edit" type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#del-data" onclick=\'deleteData('.json_encode($row).')\'><i class="fa fa-trash"></i></button></center>';
                            return $btn;
                        })
                        ->rawColumns(['action','jumlah_asset'])
                        ->make(true);
            }

            return View::make('ruangan');
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
        if ($req->ajax()) {
            $ruangan = $req->ruangan;

            $assets = DB::table("assets")->select("assets.*", "ruangan.nama_ruangan")
                    ->LeftJoin("ruangan", "ruangan.ruangan_id", "=", "assets.ruangan_id");

            if (!empty($ruangan)) {
                $assets = $assets->where("assets.ruangan_id", $ruangan)
                        ->orderBy("assets.asset_id", "desc")            
                        ->get();

            } else {
                $assets = $assets->orderBy("assets.asset_id", "desc")
                        ->get();
            }

            $data   = [];
            $no     = 1;
            foreach($assets as $a){
                $data[] = [
                    'asset_id'          => $a->asset_id,
                    'no'                => $no,
                    'nama_ruangan'      => $a->nama_ruangan,
                    'nama_asset'        => $a->nama_asset,
                    'jumlah'            => $a->jumlah,
                    'tgl_buat'          => date("d/m/Y", strtotime($a->tgl_buat))
                ];
                $no++;
            }

            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                        $btn    = '<center><button title="Hapus" type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#del-data" onclick=\'deleteData('.json_encode($row).')\'><i class="fa fa-trash"></i></button></center>';
                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }

        return View::make('assets');
    }

    public function asset_save(Request $req){

        $req->validate([
            'ruangan'       => 'required|exists:ruangan,ruangan_id',
            'nama_asset'    => 'required',
            'jumlah'        => 'required|numeric|min:1'
            
        ],
        [
            'ruangan.required'      => 'Ruangan belum dipilih!',
            'ruangan.exists'        => 'Ruangan tidak tersedia!',
            'nama_asset.required'   => 'Nama Asset belum diisi!',
            'jumlah.required'       => 'Jumlah belum diisi!',
            'jumlah.numeric'        => 'Jumlah harus berupa angka!',
            'jumlah.min'            => 'Jumlah minimal 1!',
        ]);

        $data = [
            "nama_asset"    => $req->nama_asset,
            "ruangan_id"    => $req->ruangan,
            "jumlah"        => $req->jumlah,
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
        $update = DB::table('assets')->where("asset_id", $req->delete_id)->delete();
        if ($update) {
            $req->session()->flash('success', "Asset berhasil dihapus.");
        } else {
            $req->session()->flash('error', "Asset gagal dihapus!");
        }

        return redirect()->back();
    }

    public function guru(Request $req)
    {
        if($req->ajax()){
            $guru = DB::table("staff")->select("staff.*")->where("staff.staff_type", 0)
                    ->orderBy("staff.nama_lengkap", "asc")->get();


            $data   = [];
            $no     = 1;
            foreach($guru as $d){
                if($d->status == 0){
                  $status = "Sertifikasi";
                } else if($d->status == 1){
                  $status = "Honorer";
                } else {
                  $status = "Lainnya";
                }

                $data[] = [
                    'staff_id'              => $d->staff_id,
                    'no'                    => $no,
                    'nama_lengkap'          => $d->nama_lengkap,
                    'nik'                   => $d->nik,
                    'ttl'                   => $d->tempat_lahir.", ".date("d/m/Y", strtotime($d->tgl_lahir)),
                    'alamat'                => $d->alamat,
                    'no_hp'                 => $d->no_hp,
                    'pendidikan_terakhir'   => $d->pendidikan_terakhir,
                    'bidang_mengajar'       => $d->bidang_mengajar,
                    'no_sk'                 => $d->no_sk,
                    'mulai_mengajar'        => $d->mulai_mengajar,
                    'status_code'           => $d->status,
                    'status'                => $status,
                ];
                $no++;
            }

            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                        $btn    = '<center><button title="Hapus" type="button" class="btn btn-success btn-xs" data-toggle="modal" data-target="#tambah-data" onclick=\'editData('.json_encode($row).')\'><i class="fa fa-edit"></i></button> <button title="Hapus" type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#del-data" onclick=\'deleteData('.json_encode($row).')\'><i class="fa fa-trash"></i></button></center>';
                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }

        return View::make('guru');
    }

    public function staff(Request $req)
    {
        if($req->ajax()){
            $staff = DB::table("staff")->select("staff.*")->where("staff.staff_type", 1)
                    ->orderBy("staff.nama_lengkap", "asc")->get();


            $data   = [];
            $no     = 1;
            foreach($staff as $d){
                $statusList = ["Sertifikasi", "Honorer", "Lainnya"];
                $status     = $statusList[$d->status];

                $data[] = [
                    'staff_id'              => $d->staff_id,
                    'no'                    => $no,
                    'nama_lengkap'          => $d->nama_lengkap,
                    'nik'                   => $d->nik,
                    'ttl'                   => $d->tempat_lahir.", ".date("d/m/Y", strtotime($d->tgl_lahir)),
                    'alamat'                => $d->alamat,
                    'no_hp'                 => $d->no_hp,
                    'pendidikan_terakhir'   => $d->pendidikan_terakhir,
                    'bidang_mengajar'       => $d->bidang_mengajar,
                    'no_sk'                 => $d->no_sk,
                    'mulai_mengajar'        => $d->mulai_mengajar,
                    'status_code'           => $d->status,
                    'status'                => $status,
                ];
                $no++;
            }

            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                        $btn    = '<center><button title="Edit" type="button" class="btn btn-success btn-xs" data-toggle="modal" data-target="#tambah-data" onclick=\'editData('.json_encode($row).')\'><i class="fa fa-edit"></i></button> <button title="Hapus" type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#del-data" onclick=\'deleteData('.json_encode($row).')\'><i class="fa fa-trash"></i></button></center>';
                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }

        return View::make('staff');
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
        $santri = DB::table("santri")->select("santri.*", "kelas.kelas_semester")
                    ->LeftJoin("kelas", "kelas.kelas_id", "=", "santri.kelas_id");

        if($req->format == "json"){
            if(!empty($req->kelas)){
                $santri = $santri->where("santri.kelas_id", $req->kelas);
            }

            $santri = $santri->get();
            return response()->json($santri);
        } else {
            if($req->ajax()){
                $santri = $santri->orderBy("santri.nama_lengkap", "asc");

                if(!empty($req->kelas)){
                    $santri = $santri->where("santri.kelas_id", $req->kelas);
                }

                $santri = $santri->get();
    
                $data   = [];
                $no     = 1;
                foreach($santri as $d){
                    $pendidikanFormal   = ["PAUD", "MI", "MTS", "SMK"];
                    $programPonpes      = ["Pondok", "Kursus"];

                    $pendidikan_formal = null;
                    if($d->pendidikan_formal !== null){
                        $pendidikan_formal = $pendidikanFormal[$d->pendidikan_formal];
                    }

                    $program_ponpes = null;
                    if($d->program_ponpes !== null){
                        $program_ponpes = $programPonpes[$d->program_ponpes];
                    }
    
                    $data[] = [
                        'santri_id'             => $d->santri_id,
                        'no'                    => $no,
                        'nama_lengkap'          => $d->nama_lengkap,
                        'nis'                   => $d->nis,
                        'nik'                   => $d->nik,
                        'no_kk'                 => $d->no_kk,
                        'ttl'                   => $d->tempat_lahir.", ".date("d/m/Y", strtotime($d->tgl_lahir)),
                        'tgl_lahir'             => $d->tgl_lahir,
                        'alamat'                => $d->alamat,
                        'no_hp'                 => $d->no_hp,
                        'pendidikan_formal_id'  => $d->pendidikan_formal,
                        'pendidikan_formal'     => $pendidikan_formal,
                        'kelas_id'              => $d->kelas_id,
                        'kelas_semester'        => $d->kelas_semester,
                        'nisn'                  => $d->nisn,
                        'program_ponpes_id'     => $d->program_ponpes,
                        'program_ponpes'        => $program_ponpes,
                        'riwayat_mondok'        => $d->riwayat_mondok,
                        'nama_ayah'             => $d->nama_ayah,
                        'nama_ibu'              => $d->nama_ibu,
                        'nohp_ortu'             => $d->nohp_ortu,
                        'alamat_ortu'           => $d->alamat_ortu
                    ];
                    $no++;
                }
    
                return DataTables::of($data)
                        ->addIndexColumn()
                        ->addColumn('action', function($row){
                            $btn    = '<center><button title="Edit" type="button" class="btn btn-success btn-xs" data-toggle="modal" data-target="#tambah-data" onclick=\'editData('.json_encode($row).')\'><i class="fa fa-edit"></i></button> <button title="Hapus" type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#del-data" onclick=\'deleteData('.json_encode($row).')\'><i class="fa fa-trash"></i></button></center>';
                            return $btn;
                        })
                        ->rawColumns(['action'])
                        ->make(true);
            }

            return View::make('santri')->with(compact("santri"));
        }
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

    public function pemasukan_kategori(Request $req){

        $kategori = DB::table("pemasukan_kategori")->select("pemasukan_kategori.*")
                    ->where("NA", "N")
                    ->orderBy("pemasukan_kategori.kategori", "asc")
                    ->get();

        if ($req->format == "json") {
            return response()->json($kategori);
        } else {
            if($req->ajax()){
                $data   = [];
                $no     = 1;
                foreach($kategori as $d){
                    $data[] = [
                        'kategori_id'   => $d->kategori_id,
                        'no'            => $no,
                        'kategori'      => $d->kategori,
                        'tgl_buat'      => date("d/m/Y", strtotime($d->tgl_buat)),
                        'user_buat'     => $d->user_buat
                    ];
                    $no++;
                }
    
                return DataTables::of($data)
                        ->addIndexColumn()
                        ->addColumn('action', function($row){
                            $btn    = '<center><button title="Edit" type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#tambah-data" onclick=\'editData('.json_encode($row).')\'><i class="fa fa-edit"></i></button> <button title="Hapus" type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#del-data" onclick=\'deleteData('.json_encode($row).')\'><i class="fa fa-trash"></i></button></center>';
                            return $btn;
                        })
                        ->rawColumns(['action'])
                        ->make(true);
            }

            return View::make('pemasukan_kategori');
        }
    }

    public function pemasukan_kategori_save(Request $req){
        $req->validate([
            'kategori'    => 'required'
            
        ],
        [
            'kategori.required'     => 'Kategori belum diisi!',
            'kategori.unique'       => 'Kategori sudah ada!',
        ]);

        $data = [
            "kategori"      => $req->kategori,
            "user_buat"     => Auth::user()->username,
        ];

        if(empty($req->kategori_id)){
            $add = DB::table('pemasukan_kategori')->insertGetId($data);

            if($add){
                $req->session()->flash('success', "Kategori Pemasukan berhasil ditambahkan.");
            } else {
                $req->session()->flash('error', "Kategori Pemasukan gagal ditambahkan!");
            }
        } else {
            $update = DB::table('pemasukan_kategori')->where("kategori_id", $req->kategori_id)->update($data);

            if($update){
                $req->session()->flash('success', "Kategori Pemasukan berhasil diperbarui.");
            } else {
                $req->session()->flash('error', "Kategori Pemasukan gagal diperbarui!");
            }
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

    public function pengeluaran_kategori(Request $req){

        $kategori = DB::table("pengeluaran_kategori")->select("pengeluaran_kategori.*")
                    ->where("NA", "N")
                    ->orderBy("pengeluaran_kategori.kategori", "asc")
                    ->get();

        if ($req->format == "json") {
            return response()->json($kategori);
        } else {
            if($req->ajax()){
                $data   = [];
                $no     = 1;
                foreach($kategori as $d){
                    if($d->jenis == 0){
                        $jenis = "Dana Guru";
                    } else {
                        $jenis = "Dana Operasional";
                    }

                    $data[] = [
                        'kategori_id'   => $d->kategori_id,
                        'jenis_id'      => $d->jenis,
                        'no'            => $no,
                        'jenis'         => $jenis,
                        'kategori'      => $d->kategori,
                        'tgl_buat'      => date("d/m/Y", strtotime($d->tgl_buat)),
                        'user_buat'     => $d->user_buat
                    ];
                    $no++;
                }
    
                return DataTables::of($data)
                        ->addIndexColumn()
                        ->addColumn('action', function($row){
                            $btn    = '<center><button title="Edit" type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#tambah-data" onclick=\'editData('.json_encode($row).')\'><i class="fa fa-edit"></i></button> <button title="Hapus" type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#del-data" onclick=\'deleteData('.json_encode($row).')\'><i class="fa fa-trash"></i></button></center>';
                            return $btn;
                        })
                        ->rawColumns(['action'])
                        ->make(true);
            }

            return View::make('pengeluaran_kategori');
        }
    }

    public function pengeluaran_kategori_save(Request $req){
        $req->validate([
            'kategori'    => 'required'
            
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

        if(empty($req->kategori_id)){
            $add = DB::table('pengeluaran_kategori')->insertGetId($data);
            if($add){
                $req->session()->flash('success', "Kategori Pengeluaran berhasil ditambahkan.");
            } else {
                $req->session()->flash('error', "Kategori Pengeluaran gagal ditambahkan!");
            }
        } else {
            $update = DB::table('pengeluaran_kategori')->where("kategori_id", $req->kategori_id)->update($data);
            if($update){
                $req->session()->flash('success', "Kategori Pengeluaran berhasil diperbarui.");
            } else {
                $req->session()->flash('error', "Kategori Pengeluaran gagal diperbarui!");
            }
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
                    $kategori = DB::table('pemasukan_kategori')->select('pemasukan_kategori.kategori')->where("kategori_id", $k->kategori_id)->first();
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

    public function download_lap_keuangan(Request $req){
        if($req->jenis_lap == 0){
            $from       = $req->lap_harian_start;
            $to         = $req->lap_harian_end;
            $fd         = Carbon::parse($from)->locale('id');
            $td         = Carbon::parse($to)->locale('id');
            $today      = Carbon::parse(now())->locale('id');
            $periode    = $fd->isoFormat('DD MMMM YYYY')." s/d ".$td->isoFormat('DD MMMM YYYY');
            $today      = $today->isoFormat('DD MMMM YYYY');
        } else if($req->jenis_lap == 1){
            $namaBulan  =[1 => "Januari", 2 => "Februari", 3 => "Maret", 4 => "April", 5 => "Mei", 6 => "Juni", 7 => "Juli", 8 => "Agustus", 9 => "September",
                            10 => "Oktober", 11 => "November", 12 => "Desember"];
            $bulan      = $req->lap_bulanan_bulan;
            $tahun      = $req->lap_bulanan_tahun;
            $from       = $tahun."-".$bulan."-01";
            $to         = $tahun."-".$bulan."-31";
            $fd         = Carbon::parse($from)->locale('id');
            $td         = Carbon::parse($to)->locale('id');
            $today      = Carbon::parse(now())->locale('id');
            $periode    = $namaBulan[$bulan]." ".$tahun;
            $today      = $today->isoFormat('DD MMMM YYYY');
        } else {
            $tahun      = $req->lap_tahunan_tahun;
            $from       = $tahun."-01-01";
            $to         = $tahun."-12-31";
            $fd         = Carbon::parse($from)->locale('id');
            $td         = Carbon::parse($to)->locale('id');
            $today      = Carbon::parse(now())->locale('id');
            $periode    = "TAHUN ".$tahun;
            $today      = $today->isoFormat('DD MMMM YYYY');
        }


        $kategori_pemasukan     = DB::table("pemasukan_kategori")->get();
        $kategori_pengeluaran   = DB::table("pengeluaran_kategori")->get();
        $nama_yayasan           = DB::table("settings")->where("setting_name", "nama_yayasan")->first()->setting_value;
        $ketua_yayasan          = DB::table("settings")->where("setting_name", "ketua_yayasan")->first()->setting_value;
        $kota                   = DB::table("settings")->where("setting_name", "kota")->first()->setting_value;


        foreach($kategori_pemasukan as $k){
            $k->total = DB::table("keuangan")->where([["kategori_id", $k->kategori_id], ["jenis", 0]])->whereBetween('tanggal', [$from, $to])->sum("nominal");
        }

        foreach($kategori_pengeluaran as $k){
            $k->total = DB::table("keuangan")->where([["kategori_id", $k->kategori_id], ["jenis", 1]])->whereBetween('tanggal', [$from, $to])->sum("nominal");
        }

        if($kategori_pemasukan || $kategori_pengeluaran){
            $pdf    = PDF::loadView('download_lapkeu', compact("kategori_pemasukan","kategori_pengeluaran","nama_yayasan","ketua_yayasan","kota","periode","today"));
            $fn     = "lapkeu.pdf";
            return $pdf->setPaper('A4')->download($fn);
        } else {
            $req->session()->flash('error', "Laporan belum tersedia!");
        }

        return redirect()->route("laporan_keuangan");
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
        $nama_hari = ["Senin", "Selasa", "Rabu", "Kamis", "Jum'at", "Sabtu", "Minggu"];

        if(empty($req->jadwal_id)){
            $mapel = DB::table("jadwal_pelajaran")->select("tahun_pelajaran.*", "jadwal_pelajaran.*", "jam_pelajaran.*", "mapel.*", "kelas.kelas_semester", "users.name as guru")
                    ->LeftJoin("tahun_pelajaran", "tahun_pelajaran.tahun_id", "=", "jadwal_pelajaran.tahun_id")
                    ->LeftJoin("jam_pelajaran", "jam_pelajaran.jp_id", "=", "jadwal_pelajaran.jp_id")
                    ->LeftJoin("mapel", "mapel.mapel_id", "=", "jadwal_pelajaran.mapel_id")
                    ->LeftJoin("kelas", "kelas.kelas_id", "=", "mapel.kelas_id")
                    ->LeftJoin("users", "users.id", "=", "mapel.guru")
                    ->where([["jadwal_pelajaran.NA", "N"], ["guru", Auth::user()->id]]);

            $mapel = $mapel->orderBy("jam_pelajaran.hari", "asc")
                    ->orderBy("jam_pelajaran.jam", "asc")
                    ->orderBy("mapel.mapel", "asc")
                    ->paginate(25);

            foreach($mapel  as $m){
                $m->nama_hari = $nama_hari[$m->hari];
            }

            return View::make('presensi')->with(compact("mapel"));
        } else {
            $jadwal     = DB::table("jadwal_pelajaran")->select("tahun_pelajaran.*", "jadwal_pelajaran.*", "jam_pelajaran.*", "mapel.*", "kelas.kelas_semester", "users.name as guru")
                        ->LeftJoin("tahun_pelajaran", "tahun_pelajaran.tahun_id", "=", "jadwal_pelajaran.tahun_id")
                        ->LeftJoin("jam_pelajaran", "jam_pelajaran.jp_id", "=", "jadwal_pelajaran.jp_id")
                        ->LeftJoin("mapel", "mapel.mapel_id", "=", "jadwal_pelajaran.mapel_id")
                        ->LeftJoin("kelas", "kelas.kelas_id", "=", "mapel.kelas_id")
                        ->LeftJoin("users", "users.id", "=", "mapel.guru")
                        ->where("jadwal_pelajaran.jadwal_id", $req->jadwal_id)
                        ->first();

            $jadwal->nama_hari = $nama_hari[$jadwal->hari];

            $presensi   = DB::table("presensi")->select("presensi.*", "jadwal_pelajaran.*", "jam_pelajaran.*", "mapel.*", "kelas.kelas_semester", "santri.*", "users.name as guru")
                        ->LeftJoin("jadwal_pelajaran", "jadwal_pelajaran.jp_id", "=", "presensi.jadwal_id")
                        ->LeftJoin("jam_pelajaran", "jam_pelajaran.jp_id", "=", "jadwal_pelajaran.jp_id")
                        ->LeftJoin("mapel", "mapel.mapel_id", "=", "jadwal_pelajaran.mapel_id")
                        ->LeftJoin("kelas", "kelas.kelas_id", "=", "mapel.kelas_id")
                        ->LeftJoin("santri", "santri.kelas_id", "=", "kelas.kelas_id")
                        ->LeftJoin("users", "users.id", "=", "mapel.guru")
                        ->where("presensi.jadwal_id", $req->jadwal_id)
                        ->orderBy("presensi.presensi_id", "desc")
                        ->paginate(50);

            return View::make('presensi_input')->with(compact("presensi", "jadwal"));
        }
    }

    public function presensi_save(Request $req){
        $req->validate([
            'jadwal'    => 'required|exists:jadwal_pelajaran,jadwal_id',
            'santri'    => 'required|exists:santri,santri_id',
            'tanggal'   => 'required|date_format:Y-m-d'
        ],
        [
            'jadwal.required'       => 'Jadwal Pelajaran belum dipilih!',
            'jadwal.exists'         => 'Jadwal Pelajaran tidak ditemukan!',
            'santri.required'       => 'Santri / Santri Wati belum dipilih!',
            'santri.exists'         => 'Santri / Santri Wati tidak ditemukan!',
            'tanggal.required'      => 'Tanggal belum dipilih!',
            'tanggal.date_format'   => 'Tanggal tidak sesuai format (Y-m-d)!',
        ]);

        $exists = DB::table("presensi")->where([["jadwal_id", $req->jadwal], ["santri_id", $req->santri], ["tgl_presensi", $req->tanggal]])->get()->count();

        if($exists == 0){
            $data = [
                "jadwal_id"     => $req->jadwal,
                "santri_id"     => $req->santri,
                "tgl_presensi"  => $req->tanggal,
            ];

            $add = DB::table('presensi')->insertGetId($data);

            if($add){
                $req->session()->flash('success', "Presensi berhasil ditambahkan.");
            } else {
                $req->session()->flash('error', "Presensi gagal ditambahkan!");
            }
        } else {
            $req->session()->flash('error', "Santri / Santri Wati sudah ditambahkan!");
        }

        return redirect()->back();
    }

    public function presensi_delete(Request $req)
    {
        $update = DB::table('presensi')->where("presensi_id", $req->delete_id)->delete();
        if ($update) {
            $req->session()->flash('success', "Presensi berhasil dihapus.");
        } else {
            $req->session()->flash('error', "Presensi gagal dihapus!");
        }

        return redirect()->back();
    }

    public function tp(Request $req)
    {
        $tp = DB::table("tahun_pelajaran")->select("tahun_pelajaran.*")
                ->where("tahun_pelajaran.NA", "N");

        if ($req->format == "json") {
            $tahun = $req->tahun;

            if(!empty($hari)){
                $tp = $tp->where("tahun_id", $tahun);
            }

            $tp = $tp->get();

            return response()->json($tp);
        } else {
            $tp = $tp->orderBy("tahun_pelajaran.tahun_id", "asc")->paginate(25);

            return View::make('tp')->with(compact("tp"));
        }
    }

    public function tp_save(Request $req){
        $req->validate([
            'tahun'     => 'required',
        ],
        [
            'tahun.required'    => 'Tahun Pelajaran belum diisi! (Contoh: 2024/2025)',
        ]);

        $data = [
            "tahun_pelajaran"   => $req->tahun
        ];

        $add = DB::table('tahun_pelajaran')->insertGetId($data);

        if($add){
            $req->session()->flash('success', "Tahun Pelajaran berhasil ditambahkan.");
        } else {
            $req->session()->flash('error', "Tahun Pelajaran gagal ditambahkan!");
        }
        
        return redirect()->back();
    }

    public function tp_delete(Request $req)
    {
        $update = DB::table('tahun_pelajaran')->where("tahun_id", $req->delete_id)->update(["NA" => "Y"]);
        if ($update) {
            $req->session()->flash('success', "Tahun Pelajaran berhasil dihapus.");
        } else {
            $req->session()->flash('error', "Tahun Pelajaran gagal dihapus!");
        }

        return redirect()->back();
    }

    public function jp(Request $req)
    {
        $nama_hari = ["Senin", "Selasa", "Rabu", "Kamis", "Jum'at", "Sabtu", "Minggu"];

        $jp = DB::table("jam_pelajaran")->select("jam_pelajaran.*")
                ->where("jam_pelajaran.NA", "N")
                ->orderBy("jam_pelajaran.hari", "asc")->orderBy("jam_pelajaran.jam", "asc");

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
            if($req->ajax()){
                $jp = $jp->get();

                foreach($jp as $j){
                    $j->nama_hari = $nama_hari[$j->hari];
                }
    
                $data   = [];
                $no     = 1;
                foreach($jp as $d){
                    $data[] = [
                        'jp_id'         => $d->jp_id,
                        'no'            => $no,
                        'nama_hari'     => $d->nama_hari,
                        'jam'           => $d->jam
                    ];
                    $no++;
                }
    
                return DataTables::of($data)
                        ->addIndexColumn()
                        ->addColumn('action', function($row){
                            $btn    = '<center><button title="Hapus" type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#del-data" onclick=\'deleteData('.json_encode($row).')\'><i class="fa fa-trash"></i></button></center>';
                            return $btn;
                        })
                        ->rawColumns(['action'])
                        ->make(true);
            }

            return View::make('jp');
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
        if($req->ajax()){
            $nama_hari = ["Senin", "Selasa", "Rabu", "Kamis", "Jum'at", "Sabtu", "Minggu"];

            $jadwal = DB::table("jadwal_pelajaran")->select("tahun_pelajaran", "jadwal_pelajaran.*", "jam_pelajaran.*", "users.name", "mapel.*", "kelas.kelas_semester")
                    ->LeftJoin("tahun_pelajaran", "tahun_pelajaran.tahun_id", "=", "jadwal_pelajaran.tahun_id")
                    ->LeftJoin("jam_pelajaran", "jam_pelajaran.jp_id", "=", "jadwal_pelajaran.jp_id")
                    ->LeftJoin("mapel", "mapel.mapel_id", "=", "jadwal_pelajaran.mapel_id")
                    ->LeftJoin("kelas", "kelas.kelas_id", "=", "mapel.kelas_id")
                    ->LeftJoin("users", "users.id", "=", "mapel.guru")
                    ->where("jadwal_pelajaran.NA", "N")
                    ->orderBy("jam_pelajaran.hari", "asc")
                    ->orderBy("mapel.mapel", "asc")
                    ->get();

            foreach($jadwal as $j){
                $j->nama_hari = $nama_hari[$j->hari];
            }

            $data   = [];
            $no     = 1;
            foreach($jadwal as $d){
                $data[] = [
                    'no'                => $no,
                    'tahun_pelajaran'   => $d->tahun_pelajaran,
                    'hari'              => $nama_hari[$d->hari],
                    'jam'               => $d->jam,
                    'mapel'             => $d->mapel,
                    'kelas'             => $d->kelas_semester,
                    'guru'              => $d->name,
                ];
                $no++;
            }

            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                        $btn    = '<center><button title="Hapus" type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#del-data" onclick=\'deleteData('.json_encode($row).')\'><i class="fa fa-trash"></i></button></center>';
                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }

        return View::make('jadwal');
    }

    public function jadwal_save(Request $req){
        $req->validate([
            'jp'        => 'required|exists:jam_pelajaran,jp_id',
            'mapel'     => 'required|exists:mapel,mapel_id',
            'tp'        => 'required|exists:tahun_pelajaran,tahun_id',
        ],
        [
            'jp.required'       => 'Jam Pelajaran belum dipilih!',
            'jp.exists'         => 'Jam Pelajaran tidak ditemukan!',
            'tp.required'       => 'Tahun Pelajaran belum dipilih!',
            'tp.exists'         => 'Tahun Pelajaran tidak ditemukan!',
            'mapel.required'    => 'Mata Pelajaran belum dipilih!',
            'mapel.exists'      => 'Mata Pelajaran tidak ditemukan!',
        ]);

        $exists = DB::table("jadwal_pelajaran")->where([["tahun_id", $req->tp], ["jp_id", $req->jp], ["mapel_id", $req->mapel]])->get()->count();

        if($exists == 0){
            $data = [
                "jp_id"     => $req->jp,
                "tahun_id"  => $req->tp,
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

    public function nilai_huruf($nilai){
        $nilai = abs($nilai);
		$huruf = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
		$nilai_huruf = "";
		if ($nilai < 12) {
			$nilai_huruf = " ". $huruf[$nilai];
		} else if ($nilai <20) {
			$nilai_huruf = $this->nilai_huruf($nilai - 10). " Belas ";
		} else if ($nilai < 100) {
			$nilai_huruf = $this->nilai_huruf($nilai/10)." Puluh ". $this->nilai_huruf($nilai % 10);
		} else if ($nilai < 200) {
			$nilai_huruf = " Seratus" . $this->nilai_huruf($nilai - 100);
		} else if ($nilai < 1000) {
			$nilai_huruf = $this->nilai_huruf($nilai/100) . " Ratus " . $this->nilai_huruf($nilai % 100);
		} else if ($nilai < 2000) {
			$nilai_huruf = " Seribu " . $this->nilai_huruf($nilai - 1000);
		}

        if($nilai<0) {
			$hasil = "Minus ". trim($nilai_huruf);
		} else {
			$hasil = trim($nilai_huruf);
		}

        return $hasil;
    }

    public function nilai(Request $req)
    {
        $nama_hari = ["Senin", "Selasa", "Rabu", "Kamis", "Jum'at", "Sabtu", "Minggu"];

        if(empty($req->mapel_id)){
            $mapel = DB::table("mapel")->select("tahun_pelajaran.*", "jadwal_pelajaran.jadwal_id", "jadwal_pelajaran.jp_id", "jadwal_pelajaran.mapel_id", "jam_pelajaran.hari", "jam_pelajaran.jam", "mapel.mapel", "kelas.kelas_semester", "users.name as guru")
                    ->LeftJoin("jadwal_pelajaran", "jadwal_pelajaran.mapel_id", "=", "mapel.mapel_id")
                    ->LeftJoin("tahun_pelajaran", "tahun_pelajaran.tahun_id", "=", "jadwal_pelajaran.tahun_id")
                    ->LeftJoin("jam_pelajaran", "jam_pelajaran.jp_id", "=", "jadwal_pelajaran.jp_id")
                    ->LeftJoin("kelas", "kelas.kelas_id", "=", "mapel.kelas_id")
                    ->LeftJoin("users", "users.id", "=", "mapel.guru")
                    ->where([["mapel.NA", "N"], ["guru", Auth::user()->id]]);

            $mapel = $mapel->orderBy("jam_pelajaran.hari", "asc")
                    ->orderBy("jam_pelajaran.jam", "asc")
                    ->orderBy("mapel.mapel", "asc")
                    ->groupBy("mapel.mapel_id")
                    ->paginate(25);

            foreach($mapel  as $m){
                $m->nama_hari = $nama_hari[$m->hari];
            }

            return View::make('nilai')->with(compact("mapel"));
        } else {
            $mapel     = DB::table("mapel")->select("tahun_pelajaran.*", "jadwal_pelajaran.*", "jam_pelajaran.*", "mapel.*", "kelas.kelas_semester", "users.name as guru")
                        ->LeftJoin("jadwal_pelajaran", "jadwal_pelajaran.mapel_id", "=", "mapel.mapel_id")            
                        ->LeftJoin("tahun_pelajaran", "tahun_pelajaran.tahun_id", "=", "jadwal_pelajaran.tahun_id")
                        ->LeftJoin("jam_pelajaran", "jam_pelajaran.jp_id", "=", "jadwal_pelajaran.jp_id")
                        ->LeftJoin("kelas", "kelas.kelas_id", "=", "mapel.kelas_id")
                        ->LeftJoin("users", "users.id", "=", "mapel.guru")
                        ->where("mapel.mapel_id", $req->mapel_id)
                        ->groupBy("mapel.mapel_id")
                        ->first();

            $mapel->nama_hari = $nama_hari[$mapel->hari];

            $nilai   = DB::table("nilai")->select("nilai.nilai", "kelas.kelas_semester", "santri.*", "users.name as guru")
                        ->LeftJoin("jadwal_pelajaran", "jadwal_pelajaran.mapel_id", "=", "nilai.mapel_id")
                        ->LeftJoin("jam_pelajaran", "jam_pelajaran.jp_id", "=", "jadwal_pelajaran.jp_id")
                        ->LeftJoin("mapel", "mapel.mapel_id", "=", "jadwal_pelajaran.mapel_id")
                        ->LeftJoin("kelas", "kelas.kelas_id", "=", "mapel.kelas_id")
                        ->LeftJoin("santri", "santri.kelas_id", "=", "kelas.kelas_id")
                        ->LeftJoin("users", "users.id", "=", "mapel.guru")
                        ->where("nilai.mapel_id", $req->mapel_id)
                        ->orderBy("nilai.nilai_id", "desc")
                        ->groupBy("mapel.mapel_id")
                        ->paginate(50);

            foreach($nilai as $n){
                $n->nilai_huruf = $this->nilai_huruf($n->nilai);
            }

            return View::make('nilai_input')->with(compact("nilai", "mapel"));
        }
    }

    public function nilai_save(Request $req){
        $req->validate([
            'mapel'     => 'required|exists:mapel,mapel_id',
            'santri'    => 'required|exists:santri,santri_id',
            'nilai'     => 'required|numeric'
        ],
        [
            'mapel.required'        => 'Mata Pelajaran belum dipilih!',
            'mapel.exists'          => 'Mata Pelajaran tidak ditemukan!',
            'santri.required'       => 'Santri / Santri Wati belum dipilih!',
            'santri.exists'         => 'Santri / Santri Wati tidak ditemukan!',
            'nilai.required'        => 'Nilai belum diisi!',
            'nilai.numeric'         => 'Nilai harus berupa angka!',
        ]);

        $exists = DB::table("nilai")->where([["mapel_id", $req->mapel], ["santri_id", $req->santri]])->get()->count();

        if($exists == 0){
            $data = [
                "mapel_id"      => $req->mapel,
                "santri_id"     => $req->santri,
                "nilai"         => $req->nilai,
            ];

            $add = DB::table('nilai')->insertGetId($data);

            if($add){
                $req->session()->flash('success', "Nilai Santri / Santri Wati berhasil ditambahkan.");
            } else {
                $req->session()->flash('error', "Nilai Santri / Santri Wati gagal ditambahkan!");
            }
        } else {
            $req->session()->flash('error', "Nilai Santri / Santri Wati tersebut sudah ditambahkan!");
        }

        return redirect()->back();
    }

    public function nilai_delete(Request $req)
    {
        $update = DB::table('nilai')->where("nilai_id", $req->delete_id)->delete();
        if ($update) {
            $req->session()->flash('success', "Nilai berhasil dihapus.");
        } else {
            $req->session()->flash('error', "Nilai gagal dihapus!");
        }

        return redirect()->back();
    }

    public function transkrip_nilai(Request $req)
    {
        $santri = DB::table('santri')->select("santri.*")
                ->where("nis", Auth::user()->username)->first();

        $nilai   = DB::table("mapel")->select("nilai.*", "jadwal_pelajaran.*", "jam_pelajaran.*", "mapel.*", "kelas.kelas_semester", "santri.*", "users.name as guru")
        ->LeftJoin("nilai", "nilai.mapel_id", "=", "mapel.mapel_id")            
        ->LeftJoin("jadwal_pelajaran", "jadwal_pelajaran.mapel_id", "=", "mapel.mapel_id")
        ->LeftJoin("jam_pelajaran", "jam_pelajaran.jp_id", "=", "jadwal_pelajaran.jp_id")
        ->LeftJoin("kelas", "kelas.kelas_id", "=", "mapel.kelas_id")
        ->LeftJoin("santri", "santri.kelas_id", "=", "kelas.kelas_id")
        ->LeftJoin("users", "users.id", "=", "mapel.guru")
        ->where("santri.santri_id", $santri->santri_id);

        if(!empty($req->tahun)){
            $nilai = $nilai->where("tahun_id", $req->tahun);
        }
        
        $nilai = $nilai->groupBy("mapel.mapel_id")->get();

        foreach($nilai as $n){
            $n->nilai_huruf = $this->nilai_huruf($n->nilai);
        }
        
        return View::make('transkrip_nilai')->with(compact("santri", "nilai"));
    }

}
