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

                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="" class="form-label">Jenis Pupuk</label>
                            <select name="jenis_pupuk" id="jenis_pupuk" class="form-select">
                                <?php foreach ($distinct_tahun as $key => $value) : ?>
                                    <option value="<?= $value->jenis ?>"><?= $value->jenis ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="" class="form-label">Periode Moving</label>
                            <input type="number" name="periode_moving" id="periode_moving" class="form-control">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="" class="form-label">Jumlah Periode diramal</label>
                            <input type="number" name="jumlah_periode" id="jumlah_periode" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="" class="form-label">Tahun</label>
                            <input type="number" name="tahun" id="tahun" class="form-control">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="mb-3">
                            <button class="btn btn-primary" id="btn_proses" type="button">Set</button>
                        </div>
                    </div>

                </div>
                <div class="row" id="banyak-bobot">

                </div>
                <div class="row d-none" id="tombolSubmit">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <button class="btn btn-primary" id="btn_submit" type="submit">Proses</button>
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
            <h4 class="box-title text-capitalize">Data Peramalan WMA</h4>
        </div>
        <div class="box-body">
            <table id="data_table" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tahun</th>
                        <th>Quarter</th>
                        <th>Penjualan/Ton</th>
                        <th>Peramalan WMA</th>
                        <th>MAD e=x-y</th>
                        <th>MSE = MAD^2</th>
                        <th>MAPE = (MAD/Xt)*100</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</section>

<!-- /Main content -->

<!-- ADD modal content -->

<!-- /ADD modal content -->

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


<?= $this->endSection() ?>
<!-- page script -->
<?= $this->section("script") ?>
<script>
    let csrfHash = '<?= csrf_hash(); ?>'
    let csrfToken = '<?= csrf_token(); ?>'
    // dataTables

    function dataTableWMA(periode_moving = 0, bobot = [], tahun = null) {
        var table = $('#data_table').removeAttr('width').DataTable({
            "paging": true,
            "lengthChange": true,
            //  full length
            "lengthMenu": [
                [ -1],
                [ "All"]
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
                    bobot: bobot,
                    periode_moving: periode_moving,
                    tahun: tahun    
                },
                async: "true"
            }
        });
    }

    dataTableWMA()


    $('#btn_proses').on('click', function() {
        let jenis_pupuk = $('#jenis_pupuk').val()
        let periode_moving = $('#periode_moving').val()
        let jumlah_periode = $('#jumlah_periode').val()
        let banyak_bobot = $('#banyak_bobot').html('')
        let bobot = []
        let html = ''
        for (let i = 1; i <= periode_moving; i++) {
            html += `
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="" class="form-label">Bobot ${i}</label>
                        <input type="text" name="bobot[]" id="bobot${i}" class="form-control">
                    </div>
                </div>
            `
        }

        $('#banyak-bobot').html(html)
        $('#tombolSubmit').removeClass('d-none')
    })

    $('#ramal').on('submit', function(e) {
        e.preventDefault()
        let jenis_pupuk = $('#jenis_pupuk').val()
        let periode_moving = $('#periode_moving').val()
        let jumlah_periode = $('#jumlah_periode').val()
        let tahun = $('#tahun').val()
        let bobot = []
        for (let i = 1; i <= periode_moving; i++) {
            bobot.push($('#bobot' + i).val())
        }

        dataTableWMA(periode_moving, bobot, tahun)
        getAvg()
    })

    function  getAvg() {
        let bobot = []
        let periode_moving = $('#periode_moving').val()
        let tahun = $('#tahun').val()
        for (let i = 1; i <= periode_moving; i++) {
            bobot.push($('#bobot' + i).val())
        }
        
        $.ajax({
            url: '<?php echo base_url($controller . "/getAll") ?>',
            type: 'POST',
            dataType: 'json',
            data: {
                [csrfToken]: csrfHash,
                bobot: bobot,
                periode_moving: periode_moving,
                tahun: tahun
            },
            success: function(response) {
                $('#avgMad').text(response.avg.avg_mad)
                $('#avgMse').text(response.avg.avg_mse)
                $('#avgMape').text(response.avg.avg_mape)
                $('#valDetail').text(response.avg.value_detail)
            }
        })
    }

</script>


<?= $this->endSection() ?>