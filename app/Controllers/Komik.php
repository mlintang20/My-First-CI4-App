<?php

namespace App\Controllers;

use App\Models\KomikModel;

class Komik extends BaseController
{
  protected $komikModel;

  public function __construct()
  {
    $this->komikModel = new KomikModel();
  }

  public function index()
  {
    // $komik = $this->komikModel->findAll();
    $data = [
      'title' => 'Daftar Komik',
      'komik' => $this->komikModel->getKomik()
    ];

    return view('komik/index', $data);
  }

  public function detail($slug)
  {
    $data = [
      'title' => 'Detail Komik',
      'komik' => $this->komikModel->getKomik($slug)
    ];

    // jika judul komik tidak tersedia di tabel daftar komik
    if(empty($data['komik'])) {
      throw new \CodeIgniter\Exceptions\PageNotFoundException('Judul komik ' . $slug . ' tidak tersedia di tabel daftar komik');
    }

    return view('komik/detail', $data);
  }

  public function create()
  {
    // session();

    $data = [
      'title' => 'Form Tambah Data Komik',
      'validation' => \Config\Services::validation()
    ];

    return view('komik/create', $data);
  }

  public function save()
  {
    // validasi input
    if(!$this->validate([
      'judul' => 'required|is_unique[komik.judul]'
    ])) {
      $validation = \Config\Services::validation();

      return redirect()->to('/komik/create')->withInput()->with('validation', $validation);
    }

    $judul = $this->request->getVar('judul');
    $slug = url_title($this->request->getVar('judul'), '-', true);

    $this->komikModel->save([
      'judul' => $judul,
      'slug' => $slug,
      'penulis' => $this->request->getVar('penulis'),
      'penerbit' => $this->request->getVar('penerbit'),
      'sampul' => $this->request->getVar('sampul')
    ]);

    session()->setFlashdata('pesan', 'Data komik ' . $judul . ' berhasil ditambahkan!');

    return redirect()->to('/komik');
  }
}
