<?= $this->extend("layouts/master_app") ?>

<?= $this->section("content") ?>
<!-- Main content -->

<section class="content mb-0">
    <div class="box">
        <div class="box-header with-border">
            <h4 class="box-title text-capitalize float-start">Input Data</h4>
        </div>

        <div class="box-body">
            <form id="ramal">
                <div class="row">

                    <div class="col-md-2">
                        <div class="mb-3">
                            <label for="" class="form-label">Jenis Pupuk</label>
                            <select name="jenis_pupuk" id="jenis_pupuk" class="form-select">
                                <?php foreach ($distinct_tahun as $key => $value) : ?>
                                    <option value="<?= $value->jenis ?>"><?= $value->jenis ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="mb-3">
                            <label for="" class="form-label">Alpha</label>
                            <input type="text" name="alpha" id="alpha" class="form-control">
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="mb-3">
                            <label for="" class="form-label">Beta</label>
                            <input type="text" name="beta" id="beta" class="form-control">
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="mb-3">
                            <label for="" class="form-label">Gamma</label>
                            <input type="text" name="gamma" id="gamma" class="form-control">
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="mb-3">
                            <label for="" class="form-label">Tahun</label>
                            <input type="number" name="tahun" id="tahun" class="form-control">

                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="mb-3">
                            <button class="btn btn-primary" id="btn_proses" type="button">Proses</button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</section>

<section class="content" id="tabelHasil">
    <div class="box">
        <div class="box-header with-border">
            <h4 class="box-title text-capitalize">Data Peramalan TES</h4>
        </div>
        <div class="box-body">
            <table id="data_table" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tahun</th>
                        <th>Quarter</th>
                        <th>Penjualan/Ton</th>
                        <th>𝒚(𝒕+𝑳)−𝒚𝑳</th>
                        <th>Level</th>
                        <th>Trend</th>
                        <th>Seasonal</th>
                        <th>Peramalan TES</th>
                        <th>MAD e=x-y</th>
                        <th>MSE = MAD^2</th>
                        <th>MAPE = (MAD/Xt)*100</th>
                    </tr>
                </thead>
                <tbody></tbody> <!-- Data akan dimasukkan di sini -->
            </table>
        </div>
    </div>
</section>


<section class="content" id="tabelHasil">
    <div class="box">
        <div class="box-header with-border">
            <h4 class="box-title text-capitalize">Hasil Keseluruhan</h4>
        </div>
        <div class="box-body">
            <table id="data_detail" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Hasil Akurasi Matriks</th>
                        <th>Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>MAD</td>
                        <td id="avgMad">0.00</td>
                    </tr>
                    <tr>
                        <td>MSE</td>
                        <td id="avgMse">0.00</td>
                    </tr>
                    <tr>
                        <td>MAPE</td>
                        <td id="avgMape">0.00</td>
                    </tr>
                    <tr>
                        <th>Hasil Peramalan Periode Kedepan</th>
                        <th>Nilai</th>
                    </tr>
                    <tr>
                        <td id="periode">Q1-2024</td>
                        <td id="valDetail">0.00</td>
                    </tr>
                </tbody> <!-- Data akan dimasukkan di sini -->
            </table>


        </div>
    </div>
</section>
<!-- /Main content -->

<!-- ADD modal content -->

<!-- /ADD modal content -->


<?= $this->endSection() ?>
<!-- page script -->
<?= $this->section("script") ?>
<script>
    let csrfHash = '<?= csrf_hash(); ?>'
    let csrfToken = '<?= csrf_token(); ?>'
    // dataTables

    function dataTableTes() {
        var table = $('#data_table').removeAttr('width').DataTable({
            "paging": true,
            "lengthChange": true,
            //  full length
            "lengthMenu": [
                [-1],
                ["All"]
            ],
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "scrollY": '45vh',
            "scrollX": true,
            "scrollCollapse": false,
            "responsive": false,
            "destroy": true,
            "ajax": {
                "url": '<?php echo base_url($controller . "/getAll") ?>',
                "type": "POST",
                "dataType": "json",
                "data": {
                    [csrfToken]: csrfHash,
                    alpha: $('#alpha').val() ?? null,
                    beta: $('#beta').val() ?? null,
                    gamma: $('#gamma').val() ?? null,
                    tahun: $('#tahun').val() ?? null

                },
                async: "true"
            }
        });
    }

    function getAvg() {
        $.ajax({
            url: '<?php echo base_url($controller . "/getAll") ?>',
            type: 'POST',
            dataType: 'json',
            data: {
                [csrfToken]: csrfHash,
                alpha: $('#alpha').val() ?? null,
                beta: $('#beta').val() ?? null,
                gamma: $('#gamma').val() ?? null,
                tahun: $('#tahun').val() ?? null
            },
            success: function(response) {
                $('#avgMad').text(response.avg.avg_mad)
                $('#avgMse').text(response.avg.avg_mse)
                $('#avgMape').text(response.avg.avg_mape)
                $('#valDetail').text(response.avg.value_detail)
            }
        })
    }

    dataTableTes()


    $('#btn_proses').on('click', function() {
        dataTableTes()
        getAvg()
    })
</script>
<?= $this->endSection() ?>