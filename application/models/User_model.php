<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User_model extends CI_model
{
	public function getKaryawan($id_user)
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
				  WHERE mst_user.id_user = '$id_user'
				  ORDER BY tb_gaji.tgl_gaji DESC , tb_absen.tgl_absen DESC
				  LIMIT 1";
		return $this->db->query($query)->row_array();
	}


	public function getGaji($id_user)
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
                  WHERE mst_user.id_user = '$id_user'";
		return $this->db->query($query)->result_array();
	}

	public function getAbsen($id_user)
	{
		$query = "SELECT * 
                  FROM mst_user JOIN mst_bagian
				  ON mst_user.bagian_id = mst_bagian.id_bagian
				  JOIN tb_struktur
				  ON tb_struktur.bagian_id_struktur = mst_bagian.id_bagian
				  JOIN mst_jabatan
				  ON mst_jabatan.id_jabatan = tb_struktur.jabatan_id
				  JOIN tb_absen
                  ON tb_absen.struktur_kode_absen = tb_struktur.kode_karyawan
                  WHERE mst_user.id_user = '$id_user'";
		return $this->db->query($query)->result_array();
	}
}
