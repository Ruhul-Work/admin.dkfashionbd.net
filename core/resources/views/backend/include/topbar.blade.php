<div class="navbar-header ">
    <div class="row align-items-center justify-content-between g-2 flex-wrap flex-sm-nowrap">
        <div class="col-12 col-sm-auto">
            <div class="d-flex align-items-center justify-content-between justify-content-sm-start gap-2 gap-md-4">
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="sidebar-toggle">
                        <iconify-icon icon="heroicons:bars-3-solid" class="icon text-2xl non-active"></iconify-icon>
                        <iconify-icon icon="iconoir:arrow-right" class="icon text-2xl active"></iconify-icon>
                    </button>
                    <button type="button" class="sidebar-mobile-toggle">
                        <iconify-icon icon="heroicons:bars-3-solid" class="icon"></iconify-icon>
                    </button>
                </div>

                <form class="navbar-search d-none d-lg-inline-block">
                    <input type="text" name="search" placeholder="Search">
                    <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
                </form>

                <!-- Branch switcher start -->
                @php
                    use App\Support\BranchScope;
                    $auth = auth()->user();
                    $isSuper = $auth?->isSuper();
                    $isAll = BranchScope::isAll();
                    $curId = BranchScope::currentId();

                    // সুপার হলে: list cache করে দেখাবো
                    $branchList = collect();
                    if ($isSuper) {
                        $branchList = Cache::remember(
                            'ui:branches:list',
                            60,
                            fn() => \App\Models\backend\Branch::where('is_active', 1)
                                ->orderBy('name')
                                ->get(['id', 'name']),
                        );
                    }

                    // বর্তমান branch নাম বের করা (সুপার হলে scope থেকে, normal হলে user->branch)
                    $currentBranchName = 'Not assigned';
                    if ($isSuper) {
                        $currentBranchName = $isAll
                            ? 'All Branches'
                            : 'Branch: ' . optional($branchList->firstWhere('id', $curId))->name ?? '#' . $curId;
                    } else {
                        $currentBranchName = optional($auth?->branch)->name ?? 'Not assigned';
                    }
                @endphp

                @if ($isSuper)
                    {{-- SUPER: dropdown with switch --}}
                    <div class="dropdown">
                        <button type="button" class="topbar-action-btn topbar-branch-btn dropdown-toggle"
                            data-bs-toggle="dropdown" aria-label="Switch branch">
                            <iconify-icon icon="mdi:source-branch" class="topbar-action-icon"></iconify-icon>
                            <span class="text-truncate">{{ $currentBranchName }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a href="#" class="dropdown-item js-branch-switch" data-mode="all">All
                                    Branches</a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            @foreach ($branchList as $b)
                                <li>
                                    <a href="#" class="dropdown-item js-branch-switch" data-mode="one"
                                        data-branch="{{ $b->id }}">
                                        {{ $b->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    {{-- NORMAL: just a label (no dropdown, no switch) --}}
                    <span class="topbar-action-btn topbar-branch-btn" title="{{ $currentBranchName }}">
                        <iconify-icon icon="mdi:source-branch" class="topbar-action-icon"></iconify-icon>
                        <span class="text-truncate">{{ $currentBranchName }}</span>
                    </span>
                @endif

                <!-- Branch switcher end -->

            </div>

        </div>
        <div class="col-12 col-sm-auto">
            <div class="d-flex align-items-center justify-content-center justify-content-sm-end gap-2 gap-md-3">
                <!-- POS link -->
                <a href="{{ route('pos.index') }}"
                    class="topbar-action-btn topbar-pos-btn pos-top-btn" aria-label="Open POS">
                    <iconify-icon icon="mdi:point-of-sale" class="topbar-action-icon"></iconify-icon>
                    <span>POS</span>
                </a>
                <!-- POS link end -->
                <button type="button" data-theme-toggle
                    class="w-32-px h-32-px w-md-40-px h-md-40-px bg-neutral-200 rounded-circle d-flex justify-content-center align-items-center"></button>
                <div class="dropdown">
                    <button
                        class="has-indicator w-32-px h-32-px w-md-40-px h-md-40-px bg-neutral-200 rounded-circle d-flex justify-content-center align-items-center"
                        type="button" data-bs-toggle="dropdown">
                        <img src="{{ asset('theme/admin/assets/images/lang-flag-2.png') }}" alt="image"
                            class="w-20-px h-20-px w-md-24-px h-md-24-px object-fit-cover rounded-circle">
                    </button>

                    <div class="dropdown-menu to-top dropdown-menu-sm">
                        <div
                            class="py-12 px-16 radius-8 bg-primary-50 mb-16 d-flex align-items-center justify-content-between gap-2">
                            <div>
                                <h6 class="text-lg text-primary-light fw-semibold mb-0">Choose Your Language</h6>
                            </div>
                        </div>

                        <div class="max-h-400-px overflow-y-auto scroll-sm pe-8">
                            <div class="form-check style-check d-flex align-items-center justify-content-between mb-16">
                                <label class="form-check-label line-height-1 fw-medium text-secondary-light"
                                    for="english">
                                    <span
                                        class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                                        <img src={{ asset('theme/admin/assets/images/lang-flag.png') }} alt=""
                                            class="w-36-px h-36-px bg-success-subtle text-success-main rounded-circle flex-shrink-0">
                                        <span class="text-md fw-semibold mb-0">English</span>
                                    </span>
                                </label>
                                <input class="form-check-input" type="radio" name="crypto" id="english">
                            </div>

                            <div class="form-check style-check d-flex align-items-center justify-content-between mb-16">
                                <label class="form-check-label line-height-1 fw-medium text-secondary-light"
                                    for="japan">
                                    <span
                                        class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                                        <img src="assets/images/flags/flag2.png" alt=""
                                            class="w-36-px h-36-px bg-success-subtle text-success-main rounded-circle flex-shrink-0">
                                        <span class="text-md fw-semibold mb-0">Japan</span>
                                    </span>
                                </label>
                                <input class="form-check-input" type="radio" name="crypto" id="japan">
                            </div>

                            <div class="form-check style-check d-flex align-items-center justify-content-between mb-16">
                                <label class="form-check-label line-height-1 fw-medium text-secondary-light"
                                    for="france">
                                    <span
                                        class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                                        <img src="assets/images/flags/flag3.png" alt=""
                                            class="w-36-px h-36-px bg-success-subtle text-success-main rounded-circle flex-shrink-0">
                                        <span class="text-md fw-semibold mb-0">France</span>
                                    </span>
                                </label>
                                <input class="form-check-input" type="radio" name="crypto" id="france">
                            </div>

                            <div class="form-check style-check d-flex align-items-center justify-content-between mb-16">
                                <label class="form-check-label line-height-1 fw-medium text-secondary-light"
                                    for="germany">
                                    <span
                                        class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                                        <img src="assets/images/flags/flag4.png" alt=""
                                            class="w-36-px h-36-px bg-success-subtle text-success-main rounded-circle flex-shrink-0">
                                        <span class="text-md fw-semibold mb-0">Germany</span>
                                    </span>
                                </label>
                                <input class="form-check-input" type="radio" name="crypto" id="germany">
                            </div>

                            <div
                                class="form-check style-check d-flex align-items-center justify-content-between mb-16">
                                <label class="form-check-label line-height-1 fw-medium text-secondary-light"
                                    for="korea">
                                    <span
                                        class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                                        <img src="assets/images/flags/flag5.png" alt=""
                                            class="w-36-px h-36-px bg-success-subtle text-success-main rounded-circle flex-shrink-0">
                                        <span class="text-md fw-semibold mb-0">South Korea</span>
                                    </span>
                                </label>
                                <input class="form-check-input" type="radio" name="crypto" id="korea">
                            </div>

                            <div
                                class="form-check style-check d-flex align-items-center justify-content-between mb-16">
                                <label class="form-check-label line-height-1 fw-medium text-secondary-light"
                                    for="bangladesh">
                                    <span
                                        class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                                        <img src={{ asset('theme/admin/assets/images/lang-flag-2.png') }}
                                            alt=""
                                            class="w-36-px h-36-px bg-success-subtle text-success-main rounded-circle flex-shrink-0">
                                        <span class="text-md fw-semibold mb-0">Bangladesh</span>
                                    </span>
                                </label>
                                <input class="form-check-input" type="radio" name="crypto" id="bangladesh">
                            </div>

                            <div
                                class="form-check style-check d-flex align-items-center justify-content-between mb-16">
                                <label class="form-check-label line-height-1 fw-medium text-secondary-light"
                                    for="india">
                                    <span
                                        class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                                        <img src="assets/images/flags/flag7.png" alt=""
                                            class="w-36-px h-36-px bg-success-subtle text-success-main rounded-circle flex-shrink-0">
                                        <span class="text-md fw-semibold mb-0">India</span>
                                    </span>
                                </label>
                                <input class="form-check-input" type="radio" name="crypto" id="india">
                            </div>
                            <div class="form-check style-check d-flex align-items-center justify-content-between">
                                <label class="form-check-label line-height-1 fw-medium text-secondary-light"
                                    for="canada">
                                    <span
                                        class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                                        <img src="assets/images/flags/flag8.png" alt=""
                                            class="w-36-px h-36-px bg-success-subtle text-success-main rounded-circle flex-shrink-0">
                                        <span class="text-md fw-semibold mb-0">Canada</span>
                                    </span>
                                </label>
                                <input class="form-check-input" type="radio" name="crypto" id="canada">
                            </div>
                        </div>
                    </div>
                </div><!-- Language dropdown end -->

                <div class="dropdown">
                    <button
                        class="has-indicator w-40-px h-40-px bg-neutral-200 rounded-circle d-flex justify-content-center align-items-center"
                        type="button" data-bs-toggle="dropdown">
                        <iconify-icon icon="mage:email" class="text-primary-light text-xl"></iconify-icon>
                    </button>
                    <div class="dropdown-menu to-top dropdown-menu-lg p-0">
                        <div
                            class="m-16 py-12 px-16 radius-8 bg-primary-50 mb-16 d-flex align-items-center justify-content-between gap-2">
                            <div>
                                <h6 class="text-lg text-primary-light fw-semibold mb-0">Message</h6>
                            </div>
                            <span
                                class="text-primary-600 fw-semibold text-lg w-40-px h-40-px rounded-circle bg-base d-flex justify-content-center align-items-center">05</span>
                        </div>

                        <div class="max-h-400-px overflow-y-auto scroll-sm pe-4">

                            <a href="javascript:void(0)"
                                class="px-24 py-12 d-flex align-items-start gap-3 mb-2 justify-content-between">
                                <div
                                    class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                                    <span class="w-40-px h-40-px rounded-circle flex-shrink-0 position-relative">
                                        <img src="assets/images/notification/profile-3.png" alt="">
                                        <span
                                            class="w-8-px h-8-px bg-success-main rounded-circle position-absolute end-0 bottom-0"></span>
                                    </span>
                                    <div>
                                        <h6 class="text-md fw-semibold mb-4">Kathryn Murphy</h6>
                                        <p class="mb-0 text-sm text-secondary-light text-w-100-px">hey! there i’m...
                                        </p>
                                    </div>
                                </div>
                                <div class="d-flex flex-column align-items-end">
                                    <span class="text-sm text-secondary-light flex-shrink-0">12:30 PM</span>
                                    <span
                                        class="mt-4 text-xs text-base w-16-px h-16-px d-flex justify-content-center align-items-center bg-warning-main rounded-circle">8</span>
                                </div>
                            </a>

                            <a href="javascript:void(0)"
                                class="px-24 py-12 d-flex align-items-start gap-3 mb-2 justify-content-between">
                                <div
                                    class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                                    <span class="w-40-px h-40-px rounded-circle flex-shrink-0 position-relative">
                                        <img src="assets/images/notification/profile-4.png" alt="">
                                        <span
                                            class="w-8-px h-8-px  bg-neutral-300 rounded-circle position-absolute end-0 bottom-0"></span>
                                    </span>
                                    <div>
                                        <h6 class="text-md fw-semibold mb-4">Robiul Hasan</h6>
                                        <p class="mb-0 text-sm text-secondary-light text-w-100-px">hey! there i’m...
                                        </p>
                                    </div>
                                </div>
                                <div class="d-flex flex-column align-items-end">
                                    <span class="text-sm text-secondary-light flex-shrink-0">12:30 PM</span>
                                    <span
                                        class="mt-4 text-xs text-base w-16-px h-16-px d-flex justify-content-center align-items-center bg-warning-main rounded-circle">2</span>
                                </div>
                            </a>

                            <a href="javascript:void(0)"
                                class="px-24 py-12 d-flex align-items-start gap-3 mb-2 justify-content-between bg-neutral-50">
                                <div
                                    class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                                    <span class="w-40-px h-40-px rounded-circle flex-shrink-0 position-relative">
                                        <img src="assets/images/notification/profile-5.png" alt="">
                                        <span
                                            class="w-8-px h-8-px bg-success-main rounded-circle position-absolute end-0 bottom-0"></span>
                                    </span>
                                    <div>
                                        <h6 class="text-md fw-semibold mb-4">Kathryn Murphy</h6>
                                        <p class="mb-0 text-sm text-secondary-light text-w-100-px">hey! there i’m...
                                        </p>
                                    </div>
                                </div>
                                <div class="d-flex flex-column align-items-end">
                                    <span class="text-sm text-secondary-light flex-shrink-0">12:30 PM</span>
                                    <span
                                        class="mt-4 text-xs text-base w-16-px h-16-px d-flex justify-content-center align-items-center bg-neutral-400 rounded-circle">0</span>
                                </div>
                            </a>

                            <a href="javascript:void(0)"
                                class="px-24 py-12 d-flex align-items-start gap-3 mb-2 justify-content-between bg-neutral-50">
                                <div
                                    class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                                    <span class="w-40-px h-40-px rounded-circle flex-shrink-0 position-relative">
                                        <img src="assets/images/notification/profile-6.png" alt="">
                                        <span
                                            class="w-8-px h-8-px bg-neutral-300 rounded-circle position-absolute end-0 bottom-0"></span>
                                    </span>
                                    <div>
                                        <h6 class="text-md fw-semibold mb-4">Kathryn Murphy</h6>
                                        <p class="mb-0 text-sm text-secondary-light text-w-100-px">hey! there i’m...
                                        </p>
                                    </div>
                                </div>
                                <div class="d-flex flex-column align-items-end">
                                    <span class="text-sm text-secondary-light flex-shrink-0">12:30 PM</span>
                                    <span
                                        class="mt-4 text-xs text-base w-16-px h-16-px d-flex justify-content-center align-items-center bg-neutral-400 rounded-circle">0</span>
                                </div>
                            </a>

                            <a href="javascript:void(0)"
                                class="px-24 py-12 d-flex align-items-start gap-3 mb-2 justify-content-between">
                                <div
                                    class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                                    <span class="w-40-px h-40-px rounded-circle flex-shrink-0 position-relative">
                                        <img src="assets/images/notification/profile-7.png" alt="">
                                        <span
                                            class="w-8-px h-8-px bg-success-main rounded-circle position-absolute end-0 bottom-0"></span>
                                    </span>
                                    <div>
                                        <h6 class="text-md fw-semibold mb-4">Kathryn Murphy</h6>
                                        <p class="mb-0 text-sm text-secondary-light text-w-100-px">hey! there i’m...
                                        </p>
                                    </div>
                                </div>
                                <div class="d-flex flex-column align-items-end">
                                    <span class="text-sm text-secondary-light flex-shrink-0">12:30 PM</span>
                                    <span
                                        class="mt-4 text-xs text-base w-16-px h-16-px d-flex justify-content-center align-items-center bg-warning-main rounded-circle">8</span>
                                </div>
                            </a>

                        </div>
                        <div class="text-center py-12 px-16">
                            <a href="javascript:void(0)" class="text-primary-600 fw-semibold text-md">See All
                                Message</a>
                        </div>
                    </div>
                </div><!-- Message dropdown end -->

                <div class="dropdown">
                    <button
                        class="has-indicator w-40-px h-40-px bg-neutral-200 rounded-circle d-flex justify-content-center align-items-center"
                        type="button" data-bs-toggle="dropdown">
                        <iconify-icon icon="iconoir:bell" class="text-primary-light text-xl"></iconify-icon>
                    </button>
                    <div class="dropdown-menu to-top dropdown-menu-lg p-0">
                        <div
                            class="m-16 py-12 px-16 radius-8 bg-primary-50 mb-16 d-flex align-items-center justify-content-between gap-2">
                            <div>
                                <h6 class="text-lg text-primary-light fw-semibold mb-0">Notifications</h6>
                            </div>
                            <span
                                class="text-primary-600 fw-semibold text-lg w-40-px h-40-px rounded-circle bg-base d-flex justify-content-center align-items-center">05</span>
                        </div>

                        <div class="max-h-400-px overflow-y-auto scroll-sm pe-4">
                            <a href="javascript:void(0)"
                                class="px-24 py-12 d-flex align-items-start gap-3 mb-2 justify-content-between">
                                <div
                                    class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                                    <span
                                        class="w-44-px h-44-px bg-success-subtle text-success-main rounded-circle d-flex justify-content-center align-items-center flex-shrink-0">
                                        <iconify-icon icon="bitcoin-icons:verify-outline"
                                            class="icon text-xxl"></iconify-icon>
                                    </span>
                                    <div>
                                        <h6 class="text-md fw-semibold mb-4">Congratulations</h6>
                                        <p class="mb-0 text-sm text-secondary-light text-w-200-px">Your profile has
                                            been Verified. Your profile has been Verified</p>
                                    </div>
                                </div>
                                <span class="text-sm text-secondary-light flex-shrink-0">23 Mins ago</span>
                            </a>

                            <a href="javascript:void(0)"
                                class="px-24 py-12 d-flex align-items-start gap-3 mb-2 justify-content-between bg-neutral-50">
                                <div
                                    class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                                    <span
                                        class="w-44-px h-44-px bg-success-subtle text-success-main rounded-circle d-flex justify-content-center align-items-center flex-shrink-0">
                                        <img src="assets/images/notification/profile-1.png" alt="">
                                    </span>
                                    <div>
                                        <h6 class="text-md fw-semibold mb-4">Ronald Richards</h6>
                                        <p class="mb-0 text-sm text-secondary-light text-w-200-px">You can stitch
                                            between artboards</p>
                                    </div>
                                </div>
                                <span class="text-sm text-secondary-light flex-shrink-0">23 Mins ago</span>
                            </a>

                            <a href="javascript:void(0)"
                                class="px-24 py-12 d-flex align-items-start gap-3 mb-2 justify-content-between">
                                <div
                                    class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                                    <span
                                        class="w-44-px h-44-px bg-info-subtle text-info-main rounded-circle d-flex justify-content-center align-items-center flex-shrink-0">
                                        AM
                                    </span>
                                    <div>
                                        <h6 class="text-md fw-semibold mb-4">Arlene McCoy</h6>
                                        <p class="mb-0 text-sm text-secondary-light text-w-200-px">Invite you to
                                            prototyping</p>
                                    </div>
                                </div>
                                <span class="text-sm text-secondary-light flex-shrink-0">23 Mins ago</span>
                            </a>

                            <a href="javascript:void(0)"
                                class="px-24 py-12 d-flex align-items-start gap-3 mb-2 justify-content-between bg-neutral-50">
                                <div
                                    class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                                    <span
                                        class="w-44-px h-44-px bg-success-subtle text-success-main rounded-circle d-flex justify-content-center align-items-center flex-shrink-0">
                                        <img src="assets/images/notification/profile-2.png" alt="">
                                    </span>
                                    <div>
                                        <h6 class="text-md fw-semibold mb-4">Robiul Hasan</h6>
                                        <p class="mb-0 text-sm text-secondary-light text-w-200-px">Invite you to
                                            prototyping</p>
                                    </div>
                                </div>
                                <span class="text-sm text-secondary-light flex-shrink-0">23 Mins ago</span>
                            </a>

                            <a href="javascript:void(0)"
                                class="px-24 py-12 d-flex align-items-start gap-3 mb-2 justify-content-between">
                                <div
                                    class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                                    <span
                                        class="w-44-px h-44-px bg-info-subtle text-info-main rounded-circle d-flex justify-content-center align-items-center flex-shrink-0">
                                        DR
                                    </span>
                                    <div>
                                        <h6 class="text-md fw-semibold mb-4">Darlene Robertson</h6>
                                        <p class="mb-0 text-sm text-secondary-light text-w-200-px">Invite you to
                                            prototyping</p>
                                    </div>
                                </div>
                                <span class="text-sm text-secondary-light flex-shrink-0">23 Mins ago</span>
                            </a>
                        </div>

                        <div class="text-center py-12 px-16">
                            <a href="javascript:void(0)" class="text-primary-600 fw-semibold text-md">See All
                                Notification</a>
                        </div>

                    </div>
                </div><!-- Notification dropdown end -->

                <div class="dropdown">
          
                    <button class="d-flex justify-content-center align-items-center rounded-circle" type="button"
                        data-bs-toggle="dropdown">
                        <img src="{{ Auth::check() && !empty(Auth::user()->image)
                            ? image(Auth::user()->image)
                            : asset('theme/admin/assets/images/avatar/avatar.png') }}"
                            alt="image" class="w-40-px h-40-px object-fit-cover rounded-circle">
                    </button>
                    <div class="dropdown-menu to-top dropdown-menu-sm">
                        <div
                            class="py-12 px-16 radius-8 bg-primary-50 mb-16 d-flex align-items-center justify-content-between gap-2">
                            <div>
                                <h6 class="text-lg text-primary-light fw-semibold mb-2">
                                    {{ ucwords(Auth::user()?->name) }}</h6>
                                <span
                                    class="text-secondary-light fw-medium text-sm">{{ ucwords(Auth::user()?->role->name ?? 'Guest user') }}</span>
                            </div>
                            <button type="button" class="hover-text-danger">
                                <iconify-icon icon="radix-icons:cross-1" class="icon text-xl"></iconify-icon>
                            </button>
                        </div>
                        <ul class="to-top-list">
                            <li>
                                <a class="dropdown-item text-black px-0 py-8 hover-bg-transparent hover-text-primary d-flex align-items-center gap-3"
                                    href=" {{ route('usermanage.users.profile', Crypt::encryptString(Auth::user()?->id)) }}">
                                    <iconify-icon icon="solar:user-linear" class="icon text-xl"></iconify-icon> My
                                    Profile</a>
                            </li>
                            <li>
                                <a class="dropdown-item text-black px-0 py-8 hover-bg-transparent hover-text-primary d-flex align-items-center gap-3"
                                    href="email.html">
                                    <iconify-icon icon="tabler:message-check" class="icon text-xl"></iconify-icon>
                                    Inbox</a>
                            </li>
                            <li>
                                <a class="dropdown-item text-black px-0 py-8 hover-bg-transparent hover-text-primary d-flex align-items-center gap-3"
                                    href="company.html">
                                    <iconify-icon icon="icon-park-outline:setting-two"
                                        class="icon text-xl"></iconify-icon> Setting</a>
                            </li>
                            <li>
                                <a class="dropdown-item text-black px-0 py-8 hover-bg-transparent hover-text-danger d-flex align-items-center gap-3"
                                    href="#"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <iconify-icon icon="lucide:power" class="icon text-xl"></iconify-icon>
                                    Log Out
                                </a>
                            </li>

                            <form id="logout-form" action="{{ route('backend.logout') }}" method="POST"
                                class="d-none">
                                @csrf
                            </form>

                        </ul>
                    </div>
                </div><!-- Profile dropdown end -->
            </div>
        </div>
    </div>
</div>

<script>
    // Branch switch js (Topbar)
    // ===================================================================================

    document.addEventListener('click', function(e) {
        const el = e.target.closest('.js-branch-switch');
        if (!el) return;
        e.preventDefault();

        const mode = el.dataset.mode; // "all" | "one"
        const branchId = el.dataset.branch || '';


        // UI: disable item (ঐচ্ছিক)
        el.classList.add('disabled');

        const body = new URLSearchParams();
        body.append('mode', mode);
        if (mode === 'one') body.append('branch_id', branchId);

        fetch("{{ route('org.branch.switch') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body
            })
            .then(r => r.ok ? r.json() : r.json().catch(() => ({})).then(j => Promise.reject({
                status: r.status,
                ...j
            })))
            .then(data => {
                if (data.ok) {
                    if (window.Swal) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Branch switched',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        location.reload();
                    }
                    // DataTable থাকলে শুধু টেবিল রিলোড, নাহলে পুরো পেজ
                    if ($.fn.dataTable && $('.AjaxDataTable').length) {
                        $('.AjaxDataTable').DataTable().ajax.reload(null, false);
                    } else {
                        location.reload();
                    }
                } else {
                    window.Swal ?
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed to switch branch',
                            text: (data.message || 'Try again')
                        }) :
                        alert('Failed to switch branch.');
                }
            })
            .catch(err => {
                const msg = err?.message || (err?.errors ? Object.values(err.errors)[0]?.[0] : '') ||
                    'Request failed';
                window.Swal ?
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: msg
                    }) :
                    alert('Failed to switch branch: ' + msg);
            })
            .finally(() => {
                el.classList.remove('disabled');
            });
    });
</script>
{{-- topbar end --}}
