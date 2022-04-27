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
      'judul' => [
        'rules'   => 'required|is_unique[komik.judul]',
        'errors'  => [
          'required'  => '{field} komik harus diisi',
          'is_unique' => '{field} komik sudah terdaftar, mohon tambahkan komik lain'
        ]
      ]
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

  public function delete($id)
  {

    $this->komikModel->delete($id);
    session()->setFlashdata('pesan', 'Data komik berhasil dihapus!');

    return redirect()->to('/komik');
  }

  public function edit($slug)
  {
    $data = [
      'title' => 'Form Edit Data Komik',
      'validation' => \Config\Services::validation(),
      'komik' => $this->komikModel->getKomik($slug)
    ];

    return view('komik/edit', $data); 
  }

  public function update($id)
  {
    // cek judul komik
    $komikOld = $this->komikModel->getKomik($this->request->getVar('slug'));
    if($komikOld['judul'] == $this->request->getVar('judul')) {
      $rule_judul = 'required';
    } else {
      $rule_judul = 'required|is_unique[komik.judul]';
    }

    // validasi pada edit data
    if(!$this->validate([
      'judul' => [
        'rules'   => $rule_judul,
        'errors'  => [
          'required'  => '{field} komik harus diisi',
          'is_unique' => '{field} komik sudah terdaftar, mohon tambahkan komik lain'
        ]
      ]
    ])) {
      $validation = \Config\Services::validation();

      return redirect()->to('/komik/edit/' . $this->request->getVar('slug'))->withInput()->with('validation', $validation);
    }

    $judul = $this->request->getVar('judul');
    $slug = url_title($this->request->getVar('judul'), '-', true);

    $this->komikModel->save([
      'id' => $id,
      'judul' => $judul,
      'slug' => $slug,
      'penulis' => $this->request->getVar('penulis'),
      'penerbit' => $this->request->getVar('penerbit'),
      'sampul' => $this->request->getVar('sampul')
    ]);

    session()->setFlashdata('pesan', 'Data komik ' . $judul . ' berhasil diubah!');

    return redirect()->to('/komik');
  }
}
