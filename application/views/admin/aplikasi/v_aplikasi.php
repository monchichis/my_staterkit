<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2><?php echo $title; ?></h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="#">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a><?php echo $title; ?></a>
                </li>
                
            </ol>
        </div>
    </div>
    <br/>
  <!-- /.content-header -->
  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <!-- Default box -->
      <div class="row">
        <!-- left column -->
        <div class="col-md-12">
          <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Setup Identitas Aplikasi</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form action="<?php echo site_url('Admin/update_aplikasi')?>" method="POST" enctype="multipart/form-data">
                <div class="card-body">
                  <?php foreach($data_aplikasi as $row) : ?>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Nama Aplikasi</label>
                    <input type="hidden" name="id" value="<?php echo $row->id ?>">
                    <input type="text" class="form-control" id="exampleInputEmail1" placeholder="Enter email" name="nama_aplikasi" value="<?php echo $row->nama_aplikasi ?>">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputPassword1">Alamat</label>
                    <textarea class="form-control" rows="3" placeholder="Enter ..." name="alamat" ><?php echo $row->alamat ?></textarea>
                  </div>
                  <div class="form-group">
                    <label for="exampleInputPassword1">Telp</label>
                    <input type="text" name="telp" class="form-control" value="<?php echo $row->telp ?>">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Nama Developer</label>
                    <input type="text" class="form-control" id="exampleInputEmail1" placeholder="Enter Developer Name" name="nama_developer" value="<?php echo $row->nama_developer ?>">
                  </div>
                 
                 
                  
                  <div class="form-group">
                    <label for="exampleInputFile">Logo</label>
                    <div class="input-group">
                      <div class="custom-file">

                        <input type="file" class="custom-file-input" id="exampleInputFile" name="logo">
                        <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                      </div>
                    </div>
                    <center>
                      <img src="<?php echo base_url('assets/dist/aplikasi/') . $row->logo; ?>" class="img-thumbnail img-circle" style="height:150px">
                    </center>
                  </div>
                </div>
              <?php endforeach ; ?>
                <!-- /.card-body -->

                <div class="card-footer">
                  <button type="submit" class="btn btn-primary">Submit</button>
                </div>
              </form>
            </div>
        </div>
      </div>
    </div>
  </section>
</div>