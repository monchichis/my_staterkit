#
# TABLE STRUCTURE FOR: mst_bagian
#

DROP TABLE IF EXISTS `mst_bagian`;

CREATE TABLE `mst_bagian` (
  `id_bagian` int(11) NOT NULL AUTO_INCREMENT,
  `nama_bagian` text NOT NULL,
  `status_bagian` int(11) NOT NULL,
  PRIMARY KEY (`id_bagian`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

INSERT INTO `mst_bagian` (`id_bagian`, `nama_bagian`, `status_bagian`) VALUES (5, 'HRD', 1);
INSERT INTO `mst_bagian` (`id_bagian`, `nama_bagian`, `status_bagian`) VALUES (6, 'OB', 1);


#
# TABLE STRUCTURE FOR: mst_jabatan
#

DROP TABLE IF EXISTS `mst_jabatan`;

CREATE TABLE `mst_jabatan` (
  `id_jabatan` int(11) NOT NULL AUTO_INCREMENT,
  `nama_jabatan` text NOT NULL,
  `gaji` text NOT NULL,
  `status_jabatan` int(11) NOT NULL,
  PRIMARY KEY (`id_jabatan`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

INSERT INTO `mst_jabatan` (`id_jabatan`, `nama_jabatan`, `gaji`, `status_jabatan`) VALUES (5, 'Kepala HRD', '2500000', 1);
INSERT INTO `mst_jabatan` (`id_jabatan`, `nama_jabatan`, `gaji`, `status_jabatan`) VALUES (6, 'Staff OB', '1000000', 1);


#
# TABLE STRUCTURE FOR: mst_user
#

DROP TABLE IF EXISTS `mst_user`;

CREATE TABLE `mst_user` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `nama` text NOT NULL,
  `nik` text NOT NULL,
  `bagian_id` int(11) NOT NULL,
  `email` text NOT NULL,
  `password` text NOT NULL,
  `level` text NOT NULL,
  `date_created` date NOT NULL,
  `image` text NOT NULL,
  `is_active` int(2) NOT NULL,
  PRIMARY KEY (`id_user`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=latin1;

INSERT INTO `mst_user` (`id_user`, `nama`, `nik`, `bagian_id`, `email`, `password`, `level`, `date_created`, `image`, `is_active`) VALUES (15, 'sutanto', '0111844', 5, 'admin@gmail.com', '$2y$10$VUEKgUN6JfiE.Ka7.yKXI.Arwpcvy2IP1JV.csFCQFMGYPfCbkayO', 'Admin', '2019-10-02', 'Hijau_dan_Emas_Bola_Basket_Logo.png', 1);
INSERT INTO `mst_user` (`id_user`, `nama`, `nik`, `bagian_id`, `email`, `password`, `level`, `date_created`, `image`, `is_active`) VALUES (35, 'sumingkem', '5555', 6, 'ngimcil@gmail.com', '$2y$10$6TW2Fg9tekd8xmA0nMaeE.T.3Z062lX/e91rxx.dbIBm5wwOqOzFC', 'User', '2022-03-08', 'default1.png', 1);
INSERT INTO `mst_user` (`id_user`, `nama`, `nik`, `bagian_id`, `email`, `password`, `level`, `date_created`, `image`, `is_active`) VALUES (36, 'sutanto', 'pimpinan', 6, 'pimpinan@gmail.com', '$2y$10$hSBo5QjWfg5a6C8yViRHkOTo5BajPXjLwazAxlqVVSI8WzEF2YQTO', 'Kepsek', '2022-03-08', 'Strawberry_(3).png', 1);


#
# TABLE STRUCTURE FOR: tb_absen
#

DROP TABLE IF EXISTS `tb_absen`;

CREATE TABLE `tb_absen` (
  `id_absen` int(11) NOT NULL AUTO_INCREMENT,
  `struktur_kode_absen` text NOT NULL,
  `tgl_absen` date NOT NULL,
  `masuk` int(11) DEFAULT NULL,
  `izin` int(11) DEFAULT NULL,
  `sakit` int(11) DEFAULT NULL,
  `alpha` int(11) DEFAULT NULL,
  `telat` int(11) DEFAULT NULL,
  `pot_absensi` int(11) DEFAULT NULL,
  `pot_terlambat` int(11) NOT NULL,
  PRIMARY KEY (`id_absen`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;

#
# TABLE STRUCTURE FOR: tb_gaji
#

DROP TABLE IF EXISTS `tb_gaji`;

CREATE TABLE `tb_gaji` (
  `id_gaji` int(11) NOT NULL AUTO_INCREMENT,
  `tgl_gaji` date NOT NULL,
  `struktur_kode_gaji` text NOT NULL,
  `gapok` int(11) NOT NULL,
  `bonus` int(11) NOT NULL,
  `bpjs` int(11) NOT NULL,
  `tunjangan` int(11) NOT NULL,
  `transportasi` int(11) NOT NULL,
  `kehadiran` int(11) NOT NULL,
  PRIMARY KEY (`id_gaji`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# TABLE STRUCTURE FOR: tb_struktur
#

DROP TABLE IF EXISTS `tb_struktur`;

CREATE TABLE `tb_struktur` (
  `id_struktur` int(11) NOT NULL AUTO_INCREMENT,
  `kode_karyawan` text NOT NULL,
  `user_id` int(11) NOT NULL,
  `bagian_id_struktur` int(11) NOT NULL,
  `jabatan_id` int(11) NOT NULL,
  PRIMARY KEY (`id_struktur`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

INSERT INTO `tb_struktur` (`id_struktur`, `kode_karyawan`, `user_id`, `bagian_id_struktur`, `jabatan_id`) VALUES (11, '2022030001', 15, 5, 5);
INSERT INTO `tb_struktur` (`id_struktur`, `kode_karyawan`, `user_id`, `bagian_id_struktur`, `jabatan_id`) VALUES (12, '2022030002', 35, 6, 6);


#
# TABLE STRUCTURE FOR: tbl_absen_harian
#

DROP TABLE IF EXISTS `tbl_absen_harian`;

CREATE TABLE `tbl_absen_harian` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `absen_masuk` time NOT NULL,
  `absen_keluar` time DEFAULT NULL,
  `keterangan` varchar(25) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# TABLE STRUCTURE FOR: tbl_aplikasi
#

DROP TABLE IF EXISTS `tbl_aplikasi`;

CREATE TABLE `tbl_aplikasi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_aplikasi` varchar(250) NOT NULL,
  `alamat` varchar(250) NOT NULL,
  `telp` varchar(250) NOT NULL,
  `nama_developer` varchar(250) NOT NULL,
  `logo` varchar(250) NOT NULL,
  `token_bot` varchar(250) NOT NULL,
  `id_grup` varchar(250) NOT NULL,
  `gcalendar` varchar(250) NOT NULL,
  `link_group` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

INSERT INTO `tbl_aplikasi` (`id`, `nama_aplikasi`, `alamat`, `telp`, `nama_developer`, `logo`, `token_bot`, `id_grup`, `gcalendar`, `link_group`) VALUES (1, 'penggajian', 'sadas', '323', 'dion', 'Hijau_dan_Emas_Bola_Basket_Logo.png', '1921840682:AAFV3ovQckJgOvdnir2JcVgCkp_UruadwMw', '-1001573481867', 'https://calendar.google.com/calendar/embed?src=id.indonesian%23holiday%40group.v.calendar.google.com&ctz=Asia%2FMakassar', 'https://t.me/joinchat/e-DvSE72wKkwMzE1');


#
# TABLE STRUCTURE FOR: tbl_setting_gaji
#

DROP TABLE IF EXISTS `tbl_setting_gaji`;

CREATE TABLE `tbl_setting_gaji` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kehadiran` int(11) NOT NULL,
  `keterlambatan` int(11) NOT NULL,
  `mulai_absen_masuk` time NOT NULL,
  `mulai_absen_keluar` time NOT NULL,
  `tanggal_gajian` int(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

INSERT INTO `tbl_setting_gaji` (`id`, `kehadiran`, `keterlambatan`, `mulai_absen_masuk`, `mulai_absen_keluar`, `tanggal_gajian`) VALUES (1, 31000, 25000, '08:00:00', '18:00:00', 25);


