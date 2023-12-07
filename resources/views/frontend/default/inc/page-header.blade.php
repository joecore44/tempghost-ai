<!--page header section start-->
<section class="tt-page-header {{ isset($ptb) ? $ptb : 'ptb-100' }} bg-image-header bg-dark position-relative">
    <div class="container">
        <div class="row justify-content-center">
            <div class="{{ isset($col) ? $col : 'col-lg-8' }}">
                <div class="tt-section-heading text-center my-5">
                    <h1 class="text-light">@yield('page-header-title') <br>
                        <span class="tt-text-gradient-primary">@yield('page-header-subtitle')</span>
                    </h1>
                    @yield('page-header-form')
                </div>
            </div>
        </div>
    </div>
</section>
<!--page header section end-->
