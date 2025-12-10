<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admin_model extends CI_model
{

	
	public function countUserAktif()
	{

		$query = $this->db->query(
			"SELECT COUNT(id_user) as jml_user
                               FROM mst_user
                               WHERE is_active = 1"
		);
		if ($query->num_rows() > 0) {
			return $query->row()->jml_user;
		} else {
			return 0;
		}
	}

	public function terlambat_bulan_ini($bulan_ini, $id_user)
	{
		$query = $this->db->query("SELECT COUNT(keterangan) as jumlah_terlambat FROM `tbl_absen_harian` WHERE month(tanggal)='$bulan_ini' AND keterangan='Terlambat' AND id_user='$id_user'");
		if ($query->num_rows() >0 ) {
			return $query->row()->jumlah_terlambat;
		} else {
			return 0;
		}
	}

	public function absen_terlambat_bulan_ini($bulan_ini, $id_struktur)
	{
		$query = $this->db->query("SELECT tb_struktur.kode_karyawan, COUNT(tbl_absen_harian.keterangan) as jumlah_terlambat 
			FROM `tbl_absen_harian` 
			JOIN tb_struktur ON tb_struktur.user_id = tbl_absen_harian.id_user 
			WHERE month(tanggal)='$bulan_ini' AND keterangan='Terlambat' AND kode_karyawan='$id_struktur'");
		if ($query->num_rows() >0 ) {
			return $query->row()->jumlah_terlambat;
		} else {
			return 0;
		}
	}

	public function tepat_waktu_bulan_ini($bulan_ini, $id_user)
	{
		$query = $this->db->query("SELECT COUNT(keterangan) as jumlah_ontime FROM `tbl_absen_harian` WHERE month(tanggal)='$bulan_ini' AND keterangan='Tepat Waktu' AND id_user='$id_user'");
		if ($query->num_rows() >0 ) {
			return $query->row()->jumlah_ontime;
		} else {
			return 0;
		}
	}

	public function total_absen_bulan_ini($bulan_ini, $id_user)
	{
		$query = $this->db->query("SELECT COUNT(keterangan) as jumlah_ontime FROM `tbl_absen_harian` WHERE month(tanggal)='$bulan_ini' AND keterangan='Tepat Waktu' AND id_user='$id_user'");
		if ($query->num_rows() >0 ) {
			return $query->row()->jumlah_ontime;
		} else {
			return 0;
		}
	}

	public function total_hadir($bulan_ini, $id_struktur)
	{
		$query = $this->db->query("SELECT COUNT(keterangan) as jumlah_hadir, tb_struktur.kode_karyawan 
			FROM `tbl_absen_harian` 
			JOIN tb_struktur ON tb_struktur.user_id = tbl_absen_harian.id_user
			WHERE month(tanggal)='$bulan_ini' AND kode_karyawan='$id_struktur'");
		if ($query->num_rows() >0 ) {
			return $query->row()->jumlah_hadir;
		} else {
			return 0;
		}
	}

	public function countUserTidakAktif()
	{

		$query = $this->db->query(
			"SELECT COUNT(id_user) as jml_user
                               FROM mst_user
                               WHERE is_active = 0"
		);
		if ($query->num_rows() > 0) {
			return $query->row()->jml_user;
		} else {
			return 0;
		}
	}

	public function countUserBulan()
	{

		$query = $this->db->query(
			"SELECT CONCAT(YEAR(date_created),'/',MONTH(date_created)) AS tahun_bulan, COUNT(*) AS count_bulan
                FROM mst_user
                WHERE CONCAT(YEAR(date_created),'/',MONTH(date_created))=CONCAT(YEAR(NOW()),'/',MONTH(NOW()))
                GROUP BY YEAR(date_created),MONTH(date_created);"
		);
		if ($query->num_rows() > 0) {
			return $query->row()->count_bulan;
		} else {
			return 0;
		}
	}

	public function countAllUser()
	{
		$query = $this->db->query(
			"SELECT COUNT(id_user) as count_all
                               FROM mst_user"
		);
		if ($query->num_rows() > 0) {
			return $query->row()->count_all;
		} else {
			return 0;
		}
	}

	public function getAllUser()
	{
		$query = "SELECT * 
                  FROM mst_user
				 ";
		return $this->db->query($query)->result_array();
	}

	public function getKaryawan()
	{
		$query = "SELECT * 
				  FROM tb_struktur JOIN mst_user
				  ON mst_user.id_user = tb_struktur.user_id
				  JOIN mst_bagian
				  ON mst_bagian.id_bagian = tb_struktur.bagian_id_struktur
				  JOIN mst_jabatan
				  ON mst_jabatan.id_jabatan = tb_struktur.jabatan_id 
				  WHERE mst_user.is_active=1";
		return $this->db->query($query)->result_array();
	}

	function getKodeKaryawan()
	{
		$this->db->select('RIGHT(kode_karyawan,4) as kode', FALSE);
		$this->db->order_by('id_struktur', 'DESC');
		$this->db->limit(1);
		$query = $this->db->get('tb_struktur');
		if ($query->num_rows() <> 0) {
			$data = $query->row();
			$kode = intval($data->kode) + 1;
		} else {
			$kode = 1;
		}
		$kodemax = str_pad($kode, 4, "0", STR_PAD_LEFT);
		$kodejadi = date('Ym') . $kodemax;
		return $kodejadi;
	}

	public function getStruktur($id_struktur)
	{
		$query = "SELECT *
				  FROM tb_struktur JOIN mst_user
				  ON mst_user.id_user = tb_struktur.user_id
				  JOIN mst_bagian
				  ON mst_bagian.id_bagian = tb_struktur.bagian_id_struktur
				  JOIN mst_jabatan
				  ON mst_jabatan.id_jabatan = tb_struktur.jabatan_id
				  WHERE tb_struktur.id_struktur = '$id_struktur'";
		
		return $this->db->query($query)->row_array();
	}

	public function get_isi_absen($id_struktur)
	{
		$query = "SELECT *
				  FROM tb_struktur JOIN mst_user
				  ON mst_user.id_user = tb_struktur.user_id
				  JOIN mst_bagian
				  ON mst_bagian.id_bagian = tb_struktur.bagian_id_struktur
				  JOIN mst_jabatan
				  ON mst_jabatan.id_jabatan = tb_struktur.jabatan_id
				  WHERE tb_struktur.kode_karyawan = '$id_struktur'
				  ";
		return $this->db->query($query)->result();
	}

	public function getKaryawanAbsen()
	{
		$query = "SELECT * 
                  FROM mst_user JOIN mst_bagian
				  ON mst_user.bagian_id = mst_bagian.id_bagian
				  JOIN tb_struktur
				  ON tb_struktur.bagian_id_struktur = mst_bagian.id_bagian
				  JOIN mst_jabatan
				  ON mst_jabatan.id_jabatan = tb_struktur.jabatan_id
				  JOIN tb_absen
				  ON tb_absen.struktur_kode_absen = tb_struktur.kode_karyawan";
		return $this->db->query($query)->result_array();
	}

	public function getKaryawanGaji()
	{
		$query = "SELECT * 
                  FROM mst_user JOIN mst_bagian
				  ON mst_user.bagian_id = mst_bagian.id_bagian
				  JOIN tb_struktur
				  ON tb_struktur.bagian_id_struktur = mst_bagian.id_bagian
				  JOIN mst_jabatan
				  ON mst_jabatan.id_jabatan = tb_struktur.jabatan_id
				  JOIN tb_gaji
				  ON tb_gaji.struktur_kode_gaji = tb_struktur.kode_karyawan";
		return $this->db->query($query)->result_array();
	}

	public function getKaryawanCari($cari)
	{
		$this->db->select('*');
		$this->db->from('mst_user');
		$this->db->join('mst_bagian', 'mst_user.bagian_id = mst_bagian.id_bagian', 'left');
		$this->db->join('tb_struktur', 'tb_struktur.bagian_id_struktur = mst_bagian.id_bagian', 'left');
		$this->db->join('mst_jabatan', 'mst_jabatan.id_jabatan = tb_struktur.jabatan_id', 'left');
		$this->db->join('tb_absen', 'tb_absen.struktur_kode_absen = tb_struktur.kode_karyawan', 'left');
		$this->db->join('tb_gaji', 'tb_gaji.struktur_kode_gaji = tb_struktur.kode_karyawan', 'left');
		$this->db->like('nama', $cari);
		$this->db->or_like('nama_jabatan', $cari);
		$this->db->or_like('nama_bagian', $cari);
		$this->db->or_like('nik', $cari);
		return $this->db->get()->result_array();
	}

	public function getKaryawanGajiCetak($bulan, $tahun, $kode_struktur)
	{
		$query = "SELECT * 
                  FROM mst_user JOIN mst_bagian
				  ON mst_user.bagian_id = mst_bagian.id_bagian
				  JOIN tb_struktur
				  ON tb_struktur.bagian_id_struktur = mst_bagian.id_bagian
				  JOIN mst_jabatan
				  ON mst_jabatan.id_jabatan = tb_struktur.jabatan_id
				  JOIN tb_gaji
				  ON tb_gaji.struktur_kode_gaji = tb_struktur.kode_karyawan
				  JOIN tb_absen
				   ON tb_absen.struktur_kode_absen = tb_struktur.kode_karyawan
				  WHERE month(tgl_gaji)='$bulan' and year(tgl_gaji) = '$tahun' AND kode_karyawan = '$kode_struktur'";
		return $this->db->query($query)->row_array();
	}

	public function getAbsen($kode, $tanggal)
	{
		$query = "SELECT COUNT(tb_struktur.kode_karyawan) AS absen
                  FROM tb_struktur JOIN tb_absen
				  ON tb_struktur.kode_karyawan = tb_absen.struktur_kode_absen
				  WHERE tb_struktur.kode_karyawan = '$kode' AND DATE_FORMAT(tb_absen.tgl_absen, '%Y-%m') = '" . date('Y-m', strtotime($tanggal)) . "'";
		return $this->db->query($query)->row_array();
	}

	public function getGaji($kode, $tanggal)
	{
		$query = "SELECT COUNT(tb_struktur.kode_karyawan) AS gaji
                  FROM tb_struktur JOIN tb_gaji
				  ON tb_struktur.kode_karyawan = tb_gaji.struktur_kode_gaji
				  WHERE tb_struktur.kode_karyawan = '$kode' AND DATE_FORMAT(tb_gaji.tgl_gaji, '%Y-%m') = '" . date('Y-m', strtotime($tanggal)) . "'";
		return $this->db->query($query)->row_array();
	}
}
