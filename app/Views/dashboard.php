<?= $this->extend('layouts/master_app') ?>

<?= $this->section('content') ?>
<style>
    [data-overlay-light]::before {
    position: absolute;
    content: '';
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: none;
    z-index: -1;
    border-radius: inherit;
    display: block;
}
</style>
<section class="content">
    <div class="row">
        <div class="col-lg-23 col-12">
            <div class="box pull-up">
                <div class="box-body bg-img" style="background-image: url('./assets/images/wallpaper_dark.jpg'); opacity:1;" data-overlay-light="9">
                    <div class="d-lg-flex align-items-center justify-content-between">
                        <div class="d-md-flex align-items-center mb-30 mb-lg-0 w-p100">
                            <img src="/assets/images/custom-14.svg" class="img-fluid max-w-150" alt="">
                            <div class="ms-30">
                                <h4 class="mb-10b text-white">Selamat Datang 👋 </h4>
                                <p class="mb-0 text-fade">WMA TES<?= date('Y') ?> </p>
                            </div>
                        </div>
                        <div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- <div class="col-12">
            <div class="box no-shadow mb-0 bg-transparent">
                <div class="box-header no-border px-0">
                    <h4 class="box-title">Cours Classes</h4>
                    <ul class="box-controls pull-right d-md-flex d-none">
                        <li>
                            <button class="btn btn-primary-light px-10">View All</button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-12">
            <div class="box pull-up">
                <div class="box-body d-flex align-items-center">
                    <img src="../images/front-end-img/courses/cor-logo-6.png" alt="" class="align-self-end h-80 w-80">
                    <div class="d-flex flex-column flex-grow-1">
                        <h5 class="box-title fs-16 mb-2">Angular Class</h5>
                        <a href="#">Learn more</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-12">
            <div class="box pull-up">
                <div class="box-body d-flex align-items-center">
                    <img src="../images/front-end-img/courses/cor-logo-5.png" alt="" class="align-self-end h-80 w-80">
                    <div class="d-flex flex-column flex-grow-1">
                        <h5 class="box-title fs-16 mb-2">Android Class</h5>
                        <a href="#">Learn more</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-12">
            <div class="box pull-up">
                <div class="box-body d-flex align-items-center">
                    <img src="../images/front-end-img/courses/cor-logo-4.png" alt="" class="align-self-end h-80 w-80">
                    <div class="d-flex flex-column flex-grow-1">
                        <h5 class="box-title fs-16 mb-2">Python Class</h5>
                        <a href="#">Learn more</a>
                    </div>
                </div>
            </div>
        </div> -->
        <!-- <div class="col-lg-3 col-md-6 col-12">
            <div class="box pull-up">
                <div class="box-body d-flex align-items-center">
                    <img src="../images/front-end-img/courses/cor-logo-3.png" alt="" class="align-self-end h-80 w-80">
                    <div class="d-flex flex-column flex-grow-1">
                        <h5 class="box-title fs-16 mb-2">laravel </h5>
                        <a href="#">Learn more</a>
                    </div>
                </div>
            </div>
        </div> -->
    </div>
   
</section>
<?= $this->endSection() ?>