<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MasterdataModel;

class WMA extends BaseController
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
            'title' => 'WMA',
            'controller' => 'WMA',
            'distinct_tahun' => $db->table('distinct_jenis')->distinct()->select('jenis')->get()->getResult(),
        ];

        return view('wma', $data);
    }

    public function getAll()
    {
        $response = $data['data'] = array();

        $bobot = $this->request->getPost('bobot'); // bobot adalah array
        $periode_moving = $this->request->getPost('periode_moving');
        $tahun = $this->request->getPost('tahun');

        $result = $this->masterdataModel->select()->orderBy('tahun', 'ASC')->orderBy('quarter', 'ASC')->findAll();

        if ($tahun !== null) {
            $result = $this->masterdataModel->select()->where('tahun >=', $tahun)->orderBy('tahun', 'ASC')->orderBy('quarter', 'ASC')->findAll();
        }
        $no = 1;

        $total_mad = 0;
        $total_mse = 0;
        $total_mape = 0;
        $count = 0;

        $last_wma = 0;

        foreach ($result as $key => $value) {
            if ($key <  count(($result)) - $periode_moving - 1) {

                if ($key == $periode_moving - 1) {
                }
                if ($key < $periode_moving) {
                    $wma = '';
                    $mad = '';
                    $mse = '';
                    $mape = '';
                } else {
                    if ($bobot !== null) {
                        $wma = $this->hitungWMA($result, $key - 1, $periode_moving, $bobot);

                        $mad = abs($wma - $value->penjualan);
                        $total_mad += $mad;

                        $mse = $mad * $mad;
                        $total_mse += $mse;

                        $mape = ($mad / $value->penjualan) * 100;
                        $total_mape += $mape;

                        $count++;
                    } else {
                        $wma = '';
                        $mad = '';
                        $mse = '';
                        $mape = '';
                    }
                }

                $data['data'][$key] = array(
                    $no,
                    $value->tahun,
                    $value->quarter,
                    $value->penjualan,
                    $wma,
                    $mad,
                    $mse,
                    $mape ? number_format($mape, 2) . '%' : '',
                );
                $no++;
            }


        }

        // Calculate the averages after the loop
        if ($count > 0) {
            $avg_mad = $total_mad / $count;
            $avg_mse = $total_mse / $count;
            $avg_mape = $total_mape / $count;
        } else {
            $avg_mad = 0;
            $avg_mse = 0;
            $avg_mape = 0;
        }

        foreach ($result as $key => $value) {
            if($bobot != null) {
                if ($key ==  count(($result)) - $periode_moving - 1) {
                    $last_wma = $this->hitungWMA($result, $key - 1, $periode_moving, $bobot);
                }
            }

        }

        $data['avg'] = array(
            'avg_mad' => number_format($avg_mad, 2),
            'avg_mse' => number_format($avg_mse, 2),
            'avg_mape' => number_format($avg_mape, 2) . '%',
            'value_detail' => $last_wma !== 0 ? $last_wma : ''
        );

        return $this->response->setJSON($data);
    }



    public function hitungWMA($data, $key, $periode_moving, $bobot)
    {
        $wma = 0;
        $bobot = array_reverse($bobot);
        for ($i = 0; $i < $periode_moving; $i++) {
            $wma += $data[$key - $i]->penjualan * $bobot[$i];
        }

        return $wma;
    }
}
