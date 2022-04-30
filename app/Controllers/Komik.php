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
    // page yang aktif
    $currentPage = $this->request->getVar('page_komik') ? $this->request->getVar('page_komik') : 1;

    $keyword = $this->request->getVar('keyword');
    if($keyword) {
      $komik = $this->komikModel->search($keyword);
    } else {
      $komik = $this->komikModel;
    }

    // $komik = $this->komikModel->findAll();
    $data = [
      'title'       => 'Daftar Komik',
      // 'komik' => $this->komikModel->getKomik()
      'komik'       => $komik->paginate(5, 'komik'),    // set jumlah data yang ditampilkan per page (per halaman)
      'pager'       => $this->komikModel->pager,
      'currentPage' => $currentPage
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
      ],
      'sampul' => [
        'rules'   => 'is_image[sampul]|mime_in[sampul,image/jpg,image/jpeg,image/png]|max_size[sampul,1024]',
        'errors'  => [
          'is_image'  => 'File harus berupa gambar/image',
          'mime_in'   => 'Ekstensi gambar yang diperbolehkan : jpg, jpeg, png',
          'max_size'  => 'Ukuran gambar max 1MB'
        ]
      ]
    ])) {
      // $validation = \Config\Services::validation();
      // return redirect()->to('/komik/create')->withInput()->with('validation', $validation);

      return redirect()->to('/komik/create')->withInput();
    }

    // ambil gambar
    $fileSampul = $this->request->getFile('sampul');
    // apakah tidak ada gambar yang diupload
    if($fileSampul->getError() == 4) {
      $namaFileSampulRandom = 'default.png';
    } else {
      // generate random nama file sampul
      $namaFileSampulRandom = $fileSampul->getRandomName();
      // pindah file ke folder img di folder public
      $fileSampul->move('img', $namaFileSampulRandom);
    }

    $judul = $this->request->getVar('judul');
    $slug = url_title($this->request->getVar('judul'), '-', true);

    $this->komikModel->save([
      'judul' => $judul,
      'slug' => $slug,
      'penulis' => $this->request->getVar('penulis'),
      'penerbit' => $this->request->getVar('penerbit'),
      'sampul' => $namaFileSampulRandom
    ]);

    session()->setFlashdata('pesan', 'Data komik ' . $judul . ' berhasil ditambahkan!');

    return redirect()->to('/komik');
  }

  public function delete($id)
  {
    // cari gambar berdasarkan id
    $komik = $this->komikModel->find($id);

    // cek jika file gambar apakah default.png
    if($komik['sampul'] != 'default.png') {
      // delete gambar
      unlink('img/' . $komik['sampul']);
    }

    $this->komikModel->delete($id);
    session()->setFlashdata('pesan', 'Data komik ' . $komik['judul'] . ' berhasil dihapus!');

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
      ],
      'sampul' => [
        'rules'   => 'is_image[sampul]|mime_in[sampul,image/jpg,image/jpeg,image/png]|max_size[sampul,1024]',
        'errors'  => [
          'is_image'  => 'File harus berupa gambar/image',
          'mime_in'   => 'Ekstensi gambar yang diperbolehkan : jpg, jpeg, png',
          'max_size'  => 'Ukuran gambar max 1MB'
        ]
      ]
    ])) {
      // $validation = \Config\Services::validation();
      // return redirect()->to('/komik/edit/' . $this->request->getVar('slug'))->withInput()->with('validation', $validation);
      
      return redirect()->to('/komik/edit/' . $this->request->getVar('slug'))->withInput();
    }

    $oldSampul = $this->request->getVar('oldSampul');

    // ambil gambar
    $fileSampul = $this->request->getFile('sampul');
    // apakah tidak ada gambar yang diupload
    if($fileSampul->getError() == 4) {
      $namaFileSampulRandom = $oldSampul;
    } else {
      // generate random nama file sampul
      $namaFileSampulRandom = $fileSampul->getRandomName();
      // pindah file ke folder img di folder public
      $fileSampul->move('img', $namaFileSampulRandom);
      // delete file lama jika ada (bukan file default)
      if(!($oldSampul == 'default.png')) {
        unlink('img/' . $oldSampul);
      }
    }

        

    $judul = $this->request->getVar('judul');
    $slug = url_title($this->request->getVar('judul'), '-', true);

    $this->komikModel->save([
      'id' => $id,
      'judul' => $judul,
      'slug' => $slug,
      'penulis' => $this->request->getVar('penulis'),
      'penerbit' => $this->request->getVar('penerbit'),
      'sampul' => $namaFileSampulRandom
    ]);

    session()->setFlashdata('pesan', 'Data komik ' . $judul . ' berhasil diubah!');

    return redirect()->to('/komik');
  }
}
