<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MasterdataModel;

class TES extends BaseController
{
    protected $masterdataModel;

    public function __construct()
    {
        $this->masterdataModel = new MasterdataModel();
    }

    public function index()
    {
        $db  = db_connect();
        $data = [
            'title' => 'TES',
            'controller' => 'TES',
            'distinct_tahun' => $db->table('distinct_jenis')->distinct()->select('jenis')->get()->getResult(),
        ];

        return view('tes', $data);
    }

    public function getAll()
    {
        $response = $data['data'] = array();

        $alpha = $this->request->getPost('alpha');
        $beta = $this->request->getPost('beta');
        $gamma = $this->request->getPost('gamma');

        $quarterYear = $this->request->getPost('tahun');

        // $alpha = 0.7;
        // $beta = 0.2;
        // $gamma = 0.1;

        // $quarterYear = 2020;

        $result = $this->masterdataModel->select()->orderBy('tahun', 'ASC')->orderBy('quarter', 'ASC')->findAll();

        if ($alpha == null || $beta == null || $gamma == null || $quarterYear == null) {
            // Di dalam loop foreach terakhir
            foreach ($result as $key => $value) {
                $data['data'][$key] = array(
                    $key + 1,
                    $value->tahun,
                    $value->quarter,
                    "",
                    "",
                    "",
                    "",
                    "",
                    "",
                    "",
                    "",
                    "",
                );
            }
        } else {

            $alpha = $this->request->getPost('alpha');
            $beta = $this->request->getPost('beta');
            $gamma = $this->request->getPost('gamma');

            $quarterYear = $this->request->getPost('tahun');

            // $alpha = 0.7;
            // $beta = 0.2;
            // $gamma = 0.1;
    
            // $quarterYear = 2020;
    

            $result = $this->masterdataModel->select()->where('tahun >=', $quarterYear - 1)->orderBy('tahun', 'ASC')->orderBy('quarter', 'ASC')->findAll();

            $rumus = [];
            $penjualan = [];
            $quarter = [];

            foreach ($result as $key => $value) {
                $quarter[] = $value->quarter;
                if ($key < 4) {
                    $rumus[] = $this->quarterYear($value->quarter, $quarterYear) - $this->quarterYear($value->quarter, $quarterYear - 1);
                    $penjualan[] = $value->penjualan;
                }
            }

            // Initializations
            $level = array_sum($penjualan) / 4;
            $trend = array_sum($rumus) / 16;

            $v_level = [];
            $v_trend = [];
            $seasonal = [];
            $new_seasonal = [];
            $tes = [];

            // Calculate initial seasonal indices
            foreach ($result as $key => $value) {
                if ($key < 4) {
                    $seasonal[] = $value->penjualan / $level;
                }
            }

            $new_seasonal = $seasonal;

            foreach ($result as $key => $value) {
                if ($key >= 4) {
                    $penjualan[] = $value->penjualan;
                    $rumus[] = "";
                    if ($key == 4) {
                        $new_v_level = $alpha * ($value->penjualan / $seasonal[$key % 4]) + (1 - $alpha) * ($level + $trend);
                        $new_v_trend = $beta * ($new_v_level - $level) + (1 - $beta) * $trend;

                        $v_level[] = $new_v_level;
                        $v_trend[] = $new_v_trend;
                    } else {
                        $last_v_level = $v_level[$key - 5];
                        $last_v_trend = $v_trend[$key - 5];
                        $season_index = $seasonal[$key % 4];

                        if ($season_index != 0) {
                            $new_v_level = $alpha * ($value->penjualan / $season_index) + (1 - $alpha) * ($last_v_level + $last_v_trend);
                        } else {
                            $new_v_level = (1 - $alpha) * ($last_v_level + $last_v_trend);
                        }

                        $new_v_trend = $beta * ($new_v_level - $last_v_level) + (1 - $beta) * $last_v_trend;


                        $v_level[] = $new_v_level;
                        $v_trend[] = $new_v_trend;
                    }

                    // Update seasonal indices dynamically
                    if ($new_v_level != 0) {
                        $new_seasonal[] = $seasonal[$key % 4] = $gamma * ($value->penjualan / $new_v_level) + (1 - $gamma) * $seasonal[$key % 4];
                    }
                }
            }

            $v_level = array_merge(array_fill(0, 4, ''), $v_level);
            $v_level[3] = $level;

            $v_trend = array_merge(array_fill(0, 4, ''), $v_trend);
            $v_trend[3] = $trend;


            $peramalanTes = [];
            $mad = [];

            $avg_mad = 0;
            $avg_mse = 0;
            $avg_mape = 0;
            $hasil_peramalan = 0;

            foreach ($result as $key => $value) {
                if ($key > 2 && $key < count($result) - 1) {
                    $peramalanTes[] = ($v_level[$key] + $v_trend[$key]) * $new_seasonal[$key - 3];
                }

                if($key == count($result) - 1){
                    $hasil_peramalan = ($v_level[$key] + $v_trend[$key]) * $new_seasonal[$key - 3];
                }
            }
            $mape = [];

            for ($i = 0; $i < count($peramalanTes); $i++) {
                $mad[] = abs($peramalanTes[$i] - $result[$i + 4]->penjualan);
                $avg_mad += $mad[$i];

                $mape[] = ($mad[$i] / $result[$i + 4]->penjualan) * 100;
                $avg_mape += $mape[$i];
            }

            $avg_mad = $avg_mad / count($peramalanTes);
            $avg_mape = $avg_mape / count($peramalanTes);

            $mse = [];

            foreach ($mad as $value) {
                $mse[] = $value * $value;
                $avg_mse += $mse[count($mse) - 1];
            }

            $avg_mse = $avg_mse / count($mse);

            $peramalanTes = array_merge(array_fill(0, 4, ''), $peramalanTes);
            $mad = array_merge(array_fill(0, 4, ''), $mad);
            $mse = array_merge(array_fill(0, 4, ''), $mse);
            $mape = array_merge(array_fill(0, 4, ''), $mape);


            $data['avg'] = [
               

                'avg_mad' => is_numeric($avg_mad) ? number_format($avg_mad, 2) : $avg_mad,
                'avg_mse' => is_numeric($avg_mse) ? number_format($avg_mse, 2) : $avg_mse,
                'avg_mape' => is_numeric($avg_mape) ? number_format($avg_mape, 2) : $avg_mape,
                'value_detail' => is_numeric($hasil_peramalan) ? number_format($hasil_peramalan, 2) : $hasil_peramalan
            ];

            // hasil peramalab periode kedepan

            // Di dalam loop foreach terakhir
            foreach ($result as $key => $value) {
                $data['data'][$key] = array(
                    $key + 1,
                    $value->tahun,
                    $value->quarter,
                    is_numeric($value->penjualan) ? number_format($value->penjualan, 2) : $value->penjualan,
                    is_numeric($rumus[$key]) ? number_format($rumus[$key], 2) : $rumus[$key],
                    is_numeric($v_level[$key]) ? number_format($v_level[$key], 2) : $v_level[$key],
                    is_numeric($v_trend[$key]) ? number_format($v_trend[$key], 2) : $v_trend[$key],
                    is_numeric($new_seasonal[$key]) ? number_format($new_seasonal[$key], 2) : $new_seasonal[$key],
                    is_numeric($peramalanTes[$key]) ? number_format($peramalanTes[$key], 2) : $peramalanTes[$key],
                    is_numeric($mad[$key]) ? number_format($mad[$key], 2) : $mad[$key],
                    is_numeric($mse[$key]) ? number_format($mse[$key], 2) : $mse[$key],
                    is_numeric($mape[$key]) ? number_format($mape[$key], 2) : $mape[$key]
                );
            }
        }


        return $this->response->setJSON($data);
    }



    public function quarterYear($quarter, $year)
    {
        $db = db_connect();
        $data = $db->table('master_data')->select('penjualan')->where('quarter', $quarter)->where('tahun', $year)->get()->getResultArray();
        return $data[0]['penjualan'];
    }
}
