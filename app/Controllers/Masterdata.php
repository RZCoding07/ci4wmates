<?php

namespace App\Controllers;

use App\Controllers\BaseController;

use App\Models\MasterdataModel;

class Masterdata extends BaseController
{

	protected $masterdataModel;
	protected $validation;

	public function __construct()
	{
		$this->masterdataModel = new MasterdataModel();
		$this->validation =  \Config\Services::validation();
	}

	public function index()
	{

		$data = [
			'controller'    	=> ucwords('masterdata'),
			'title'     		=> ucwords('master data')
		];

		return view('masterdata', $data);
	}

	public function getAll()
	{
		$response = $data['data'] = array();

		$result = $this->masterdataModel->select()->findAll();
		$no = 1;
		foreach ($result as $key => $value) {
			$ops = '<div class="btn-group text-white">';
			$ops .= '<a class="btn btn-dark" onClick="save(' . $value->id . ')"><i class="fas fa-pencil-alt"></i></a>';
			$ops .= '<a class="btn btn-secondary text-dark" onClick="remove(' . $value->id . ')"><i class="fas fa-trash-alt"></i></a>';
			$ops .= '</div>';
			$data['data'][$key] = array(
				$no,
				$value->tahun,
				$value->quarter,
				$value->penjualan,
				$value->jenis,

				$ops
			);
			$no++;
		}

		return $this->response->setJSON($data);
	}

	public function getOne()
	{
		$response = array();

		$id = $this->request->getPost('id');

		if ($this->validation->check($id, 'required|numeric')) {

			$data = $this->masterdataModel->where('id', $id)->first();

			return $this->response->setJSON($data);
		} else {
			throw new \CodeIgniter\Exceptions\PageNotFoundException();
		}
	}

	public function add()
	{
		$response = array();

		$fields['id'] = $this->request->getPost('id');
		$fields['tahun'] = $this->request->getPost('tahun');
		$fields['quarter'] = $this->request->getPost('quarter');
		$fields['penjualan'] = $this->request->getPost('penjualan');
		$fields['jenis'] = $this->request->getPost('jenis');


		$this->validation->setRules([
			'tahun' => ['label' => 'Tahun', 'rules' => 'required|numeric|min_length[0]'],
			'quarter' => ['label' => 'Quarter', 'rules' => 'required|numeric|min_length[0]'],
			'penjualan' => ['label' => 'Penjualan', 'rules' => 'required|min_length[0]'],
			'jenis' => ['label' => 'Jenis', 'rules' => 'required|min_length[0]|max_length[200]'],

		]);

		if ($this->validation->run($fields) == FALSE) {

			$response['success'] = false;
			$response['messages'] = $this->validation->getErrors(); //Show Error in Input Form

		} else {

			if ($this->masterdataModel->insert($fields)) {

				$response['success'] = true;
				$response['messages'] = lang("App.insert-success");
			} else {

				$response['success'] = false;
				$response['messages'] = lang("App.insert-error");
			}
		}

		return $this->response->setJSON($response);
	}

	public function edit()
	{
		$response = array();

		$fields['id'] = $this->request->getPost('id');
		$fields['tahun'] = $this->request->getPost('tahun');
		$fields['quarter'] = $this->request->getPost('quarter');
		$fields['penjualan'] = $this->request->getPost('penjualan');
		$fields['jenis'] = $this->request->getPost('jenis');


		$this->validation->setRules([
			'tahun' => ['label' => 'Tahun', 'rules' => 'required|numeric|min_length[0]'],
			'quarter' => ['label' => 'Quarter', 'rules' => 'required|numeric|min_length[0]'],
			'penjualan' => ['label' => 'Penjualan', 'rules' => 'required|min_length[0]'],
			'jenis' => ['label' => 'Jenis', 'rules' => 'required|min_length[0]|max_length[200]'],

		]);

		if ($this->validation->run($fields) == FALSE) {

			$response['success'] = false;
			$response['messages'] = $this->validation->getErrors(); //Show Error in Input Form

		} else {

			if ($this->masterdataModel->update($fields['id'], $fields)) {

				$response['success'] = true;
				$response['messages'] = lang("App.update-success");
			} else {

				$response['success'] = false;
				$response['messages'] = lang("App.update-error");
			}
		}

		return $this->response->setJSON($response);
	}

	public function remove()
	{
		$response = array();

		$id = $this->request->getPost('id');

		if (!$this->validation->check($id, 'required|numeric')) {

			throw new \CodeIgniter\Exceptions\PageNotFoundException();
		} else {

			if ($this->masterdataModel->where('id', $id)->delete()) {

				$response['success'] = true;
				$response['messages'] = lang("App.delete-success");
			} else {

				$response['success'] = false;
				$response['messages'] = lang("App.delete-error");
			}
		}

		return $this->response->setJSON($response);
	}

	public function importData()
	{
		$validation = \Config\Services::validation();
		$valid = $this->validate([
			'fileimport' => [
				'label' => 'Inputan File',
				'rules' => 'uploaded[fileimport]|ext_in[fileimport,xls,xlsx]',
				'errors' => [
					'uploaded' => '{field} wajib diisi',
					'ext_in' => '{field} harus ekstensi xls & xlsx'
				]
			]
		]);


		if (!$valid) {
			session()->setFlashdata('error', $validation->getError('fileimport'));
			return redirect()->to('/vyc');
		}

		$ext = $this->request->getFile('fileimport')->getClientExtension();
		if ($ext == 'xls') {
			$render = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
		} else {
			$render = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
		}

		$xls = $render->load($this->request->getFile('fileimport'));
		$data = $xls->getActiveSheet()->toArray();

		// Make batch insert to table users per 100 rows
		$batch = array_chunk($data, 100);

		ini_set('max_execution_time', 0);

		foreach ($batch as $rows) {
			$insertData = []; // Reset $insertData for each batch
			for ($i = 2; $i < count($rows); $i++) {
				$insertData[] = [
					'tahun' => $rows[$i][1],
					'quarter' => $rows[$i][2],
					'penjualan' => $rows[$i][3],
					'jenis' => $rows[$i][4],
				];
			}
			$this->masterdataModel->insertBatch($insertData);
		}


		session()->setFlashdata('success', 'Data berhasil diimport');
		return redirect()->to('/masterdata');
	}
}
