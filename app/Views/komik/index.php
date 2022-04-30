<?= $this->extend('layout/template'); ?>

<?= $this->section('content'); ?>

<div class="container">
  <div class="row">
    <div class="col-4">
      <a href="/komik/create" class="btn btn-primary mt-3">Tambah Data Komik</a>
      <h1 class="my-3">Daftar Komik</h1>
      <form action="" method="POST">
        <div class="input-group mb-3">
          <input type="text" class="form-control" placeholder="Masukkan judul komik" name="keyword">
          <button class="btn btn-outline-secondary" type="submit" name="submit">Search</button>
        </div>
      </form>
    </div>
  </div>
  <div class="row">
    <div class="col">
      <?php if(session()->getFlashdata('pesan')) : ?>
        <div class="alert alert-success" role="alert">
          <?= session()->getFlashdata('pesan'); ?>
        </div>
      <?php endif; ?>
      <table class="table">
        <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">Sampul</th>
            <th scope="col">Judul</th>
            <th scope="col">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php $i = 1 + (5 * ($currentPage - 1)); ?>
          <?php foreach($komik as $k) : ?>
            <tr>
              <th scope="row"><?= $i++; ?></th>
              <td><img src="/img/<?= $k['sampul']; ?>" alt="" class="sampul"></td>
              <td><?= $k['judul']; ?></td>
              <td>
                <a href="/komik/<?= $k['slug']; ?>" class="btn btn-success">Detail</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <!-- untuk menghubungkan dan menampilkan semua page -->
      <?= $pager->links('komik', 'komik_pagination'); ?>
    </div>
  </div>
</div>

<?= $this->endSection(); ?>