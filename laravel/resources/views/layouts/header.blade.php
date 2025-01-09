<nav class="navbar navbar-dark bg-dark fixed-top" style="background-color: #7cfc00;">
    <div class="container-fluid">
        <a class="navbar-brand" href="/">MPL</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasDarkNavbar" aria-controls="offcanvasDarkNavbar" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
        </button>
        <div class="offcanvas offcanvas-end text-bg-dark" tabindex="-1" id="offcanvasDarkNavbar" aria-labelledby="offcanvasDarkNavbarLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="offcanvasDarkNavbarLabel">MossPoint</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/race_schedule">スケジュール</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/moisture_cushion">クッション、含水率入力</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/race_search">レース検索</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/race_card">出馬表</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/race_history">レース情報</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/chokyo">調教</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/horse">馬情報</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/stallion">種牡馬情報</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Dropdown
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark">
                        <li><a class="dropdown-item" href="#">Action</a></li>
                        <li><a class="dropdown-item" href="#">Another action</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="#">Something else here</a></li>
                        </ul>
                    </li>
                </ul>
                <form class="d-flex mt-3" role="search" method="GET" action="/horse_search">
                <input class="form-control me-2" type="search" name="horse_name" placeholder="Search" aria-label="Search">
                <button class="btn btn-success" type="submit">Search</button>
                </form>
            </div>
        </div>
    </div>
</nav>  
