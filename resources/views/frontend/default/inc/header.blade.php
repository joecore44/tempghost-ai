<!--header start-->
<header class="main-header position-absolute w-100">
    <nav class="navbar navbar-expand-xl navbar-dark sticky-header z-10">
        <div class="container d-flex align-items-center justify-content-lg-between position-relative">
            <a href="{{ route('home') }}" class="navbar-brand d-flex align-items-center mb-md-0 text-decoration-none">
                <img src="{{ uploadedAsset(getSetting('navbar_logo_white')) }}" alt="Ghost AI logo"
                    class="img-fluid logo-white" />
                <img src="{{ uploadedAsset(getSetting('navbar_logo_dark')) }}" alt="Ghost AI logo"
                    class="img-fluid logo-color" />
            </a>
            <a class="navbar-toggler position-absolute right-0 border-0" href="javascript::void(0)">
                <i data-feather="menu" class="flaticon-menu" data-bs-target="#offcanvasWithBackdrop"
                    aria-controls="offcanvasWithBackdrop" data-bs-toggle="offcanvas" role="button"></i>
            </a>

            <div class="clearfix"></div>

            <div class="collapse navbar-collapse justify-content-center">
                <ul class="nav col-12 col-md-auto justify-content-center main-menu">
                    <li><a href="{{ route('home') }}" class="nav-link">{{ localize('Home') }}</a></li>
                    <!--<li><a href="{{ route('home.pages.aboutUs') }}" class="nav-link">{{ localize('About Us') }}</a>-->
                    </li>

                    @if (getSetting('enable_built_in_templates') != '0')
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                                aria-expanded="false">{{ localize('Templates') }}</a>
                            <div class="dropdown-menu border-0 rounded-custom shadow py-0 bg-white">
                                <div class="rounded-custom rounded-5 width-full-3 p-4">
                                    <div class="row gx-3">
                                        <div class="col-lg-4">
                                            <h6 class="drop-heading">{{ localize('Content Templates for Anything') }}</h6>
                                            <ul class="nav nav-tabs d-flex flex-column tt-template-tab" id="myTab"
                                                role="tablist">
                                                @php
                                                    $groups = [];
                                                    if (getSetting('navbar_template_groups') != null) {
                                                        $groups = \App\Models\TemplateGroup::whereIn('id', json_decode(getSetting('navbar_template_groups')))->get();
                                                    }
                                                @endphp

                                                @foreach ($groups as $group)
                                                    <li class="nav-item" role="presentation">
                                                        <a href="pane{{ $group->id }}"
                                                            class="nav-link {{ $loop->iteration == 1 ? 'active' : '' }}"
                                                            id="pane-{{ $group->id }}" data-bs-toggle="tab"
                                                            data-bs-target="#pane{{ $group->id }}" role="tab"
                                                            aria-controls="pane{{ $group->id }}"
                                                            aria-selected="true">
                                                            {{ localize($group->name) }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <div class="col-lg-8">
                                            <div class="tab-content d-flex h-100" id="myTabContent">
                                                @php
                                                    $templates = [];
                                                    if (getSetting('navbar_template_groups') != null) {
                                                        $templates = \App\Models\Template::whereIn('template_group_id', json_decode(getSetting('navbar_template_groups')))->get();
                                                    }
                                                @endphp

                                                @foreach ($groups as $group)
                                                    <div class="bg-secondary-subtle p-4 rounded-3 tab-pane fade {{ $loop->iteration == 1 ? 'show active' : '' }}"
                                                        id="pane{{ $group->id }}" role="tabpanel"
                                                        aria-labelledby="pane-{{ $group->id }}">
                                                        <ul
                                                            class="tt-template-list list-unstyled mb-0 d-flex flex-wrap">
                                                            @foreach ($templates as $template)
                                                                @if ($template->template_group_id == $group->id)
                                                                    <li><a
                                                                            href="{{ route('templates.show', $template->code) }}"><img
                                                                                src="{{ staticAsset('backend/assets/img/templates/' . $template->code . '.png') }}"
                                                                                width="24" alt=""
                                                                                class="me-2 rounded-circle">{{ $template->collectLocalization('name') }}</a>
                                                                    </li>
                                                                @endif
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endforeach

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endif

                    <li><a href="{{ route('home.pricing') }}" class="nav-link">{{ localize('Pricing') }}</a></li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">{{ localize('Company') }}</a>
                        <div class="dropdown-menu border-0 rounded-custom shadow py-0 bg-white">
                            <div class="dropdown-grid rounded-custom width-half rounded-4 overflow-hidden">
                                <div class="dropdown-grid-item bg-white">
                                    <h6 class="drop-heading">{{ localize('Useful Links') }}</h6>
                                    <a href="{{ route('home.pages.contactUs') }}" class="dropdown-link px-0">
                                        <span class="me-2">
                                            <i data-feather="mail" class="icon-14"></i>
                                        </span>
                                        <div class="drop-title">{{ localize('Contact Us') }}</div>
                                    </a>
                                    <a href="{{ route('home.blogs') }}" class="dropdown-link">
                                        <span class="me-2">
                                            <i data-feather="file-text" class="icon-14"></i>
                                        </span>
                                        <div class="drop-title">{{ localize('Our Latest News') }}</div>
                                    </a>
                                    <a href="{{ route('home.testimonials') }}" class="dropdown-link">
                                        <span class="me-2">
                                            <i data-feather="book" class="icon-14"></i>
                                        </span>
                                        <div class="drop-title">{{ localize('Customer Review') }}</div>
                                    </a>

                                    @php
                                        $pages = [];
                                        if (getSetting('navbar_pages') != null) {
                                            $pages = \App\Models\Page::whereIn('id', json_decode(getSetting('navbar_pages')))->get();
                                        }
                                    @endphp

                                    @foreach ($pages as $navbarPage)
                                        <a href="{{ route('home.pages.show', $navbarPage->slug) }}"
                                            class="dropdown-link">
                                            <span class="me-2">
                                                <i data-feather="disc" class="icon-14"></i>
                                            </span>
                                            <div class="drop-title">
                                                <span>{{ $navbarPage->collectLocalization('title') }}</span>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="action-btns text-end me-4 me-md-5 me-lg-5 d-flex align-items-center">
                <div class="d-flex align-items-center">
                    <a href="javascript:void(0)" class="btn btn-link p-1 tt-theme-toggle">
                        <div class="tt-theme-light"><i data-feather="moon" class="fs-lg"></i></div>
                        <div class="tt-theme-dark"><i data-feather="sun" class="fs-lg"></i></div>
                    </a>
                   
                </div>
                <div class="me-xl-0 d-none d-md-block d-lg-block">
                    @guest
                        <a href="{{ route('login') }}"
                            class="btn btn-link text-decoration-none ps-2">{{ localize('Sign In') }}</a>
                    @endguest
                    @php
                        $starterActive = false;
                        $starter = \App\Models\SubscriptionPackage::isActive()
                            ->where('id', 1)
                            ->first();
                        if (!is_null($starter)) {
                            $starterActive = true;
                        }
                    @endphp

                    @auth
                        <a href="{{ route('writebot.dashboard') }}"
                            class="btn btn-primary ms-2 px-4">{{ localize('Dashboard') }}
                        </a>
                    @endauth
                    @guest
                        <a href="{{ route('register') }}" class="btn btn-primary">{{ localize('Get Started') }}
                            @if ($starterActive)
                                - {{ localize('It\'s Free') }}
                            @endif
                        </a>
                    @endguest
                </div>
            </div>
        </div>
    </nav>


    <!--offcanvas menu start-->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasWithBackdrop">
        <div class="offcanvas-header d-flex align-items-center border-bottom">
            <a href="{{ route('writebot.dashboard') }}"
                class="navbar-brand d-flex align-items-center mb-md-0 text-decoration-none">

                <img src="{{ uploadedAsset(getSetting('navbar_logo_white')) }}" alt="logo"
                    class="img-fluid logo-white" />
                <img src="{{ uploadedAsset(getSetting('navbar_logo_dark')) }}" alt="logo"
                    class="img-fluid logo-color" />
            </a>
            <button type="button" class="close-btn text-danger" data-bs-dismiss="offcanvas" aria-label="Close">
                <i data-feather="x"></i>
            </button>
        </div>
        <div class="offcanvas-body z-10">
            <ul class="nav col-12 col-md-auto justify-content-center main-menu">
                <li><a href="{{ route('home') }}" class="nav-link">{{ localize('Home') }}</a></li>
                <li><a href="{{ route('home.pages.aboutUs') }}" class="nav-link">{{ localize('About Us') }}</a></li>

                @if (getSetting('enable_built_in_templates') != '0')
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">{{ localize('Templates') }}</a>
                        <div class="dropdown-menu border-0 rounded-custom shadow py-0 bg-white">
                            <div class="rounded-custom rounded-5 width-full-3 p-4">
                                <div class="row gx-3">
                                    <div class="col-lg-12">
                                        <h6 class="drop-heading">{{ localize('Everything That You Need') }}</h6>
                                        <ul class="tt-template-list list-unstyled mb-0 d-flex flex-wrap">
                                            @foreach ($templates as $template)
                                                <li><a href="{{ route('templates.show', $template->code) }}"><img
                                                            src="{{ staticAsset('backend/assets/img/templates/' . $template->code . '.png') }}"
                                                            width="24" alt=""
                                                            class="me-1 rounded-circle">
                                                        {{ $template->collectLocalization('name') }}</a></li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                @endif

                <li><a href="{{ route('home.pricing') }}" class="nav-link">{{ localize('Pricing') }}</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                        aria-expanded="false">{{ localize('Company') }}</a>
                    <div class="dropdown-menu border-0 rounded-custom shadow py-0 bg-white">
                        <div class="dropdown-grid rounded-custom width-half rounded-4 overflow-hidden">
                            <div class="dropdown-grid-item bg-white">
                                <h6 class="drop-heading">{{ localize('Useful Links') }}</h6>
                                <a href="{{ route('home.pages.contactUs') }}" class="dropdown-link px-0">
                                    <span class="me-2">
                                        <i data-feather="mail" class="icon-14"></i>
                                    </span>
                                    <div class="drop-title">{{ localize('Contact Us') }}</div>
                                </a>
                                <a href="{{ route('home.blogs') }}" class="dropdown-link">
                                    <span class="me-2">
                                        <i data-feather="file-text" class="icon-14"></i>
                                    </span>
                                    <div class="drop-title">{{ localize('Our Latest News') }}</div>
                                </a>
                                <a href="{{ route('home.testimonials') }}" class="dropdown-link">
                                    <span class="me-2">
                                        <i data-feather="book" class="icon-14"></i>
                                    </span>
                                    <div class="drop-title">{{ localize('Customer Review') }}</div>
                                </a>

                                @foreach ($pages as $navbarPage)
                                    <a href="{{ route('home.pages.show', $navbarPage->slug) }}"
                                        class="dropdown-link">
                                        <span class="me-2">
                                            <i data-feather="disc" class="icon-14"></i>
                                        </span>
                                        <div class="drop-title">{{ $navbarPage->collectLocalization('title') }}</div>
                                    </a>
                                @endforeach

                            </div>
                        </div>
                    </div>
                </li>
            </ul>
            <div class="action-btns mt-4">

                @auth
                    <a href="{{ route('writebot.dashboard') }}" class="btn btn-primary px-4">{{ localize('Dashboard') }}
                    </a>
                @endauth

                @guest
                    <a href="{{ route('login') }}"
                        class="btn btn-link text-decoration-none me-2">{{ localize('Sign In') }}</a>

                    <a href="{{ route('register') }}" class="btn btn-primary">{{ localize('Get Started') }}
                        @if ($starterActive)
                            - {{ localize('It\'s Free') }}
                        @endif
                    </a>
                @endguest
            </div>
        </div>
    </div>
    <!--offcanvas menu end-->
</header>
<!--header end-->
