  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
      <a href="{{route('home')}}" class="logo d-flex align-items-center">
        <img src="{{asset('img/fix_it_logo.png')}}" alt="logo">
        <!-- <span class="d-none d-lg-block text-white">{{ trans('lang.labeey') }}{{Session::get('branch_id')}}</span> -->
      </a>
      <i class="bi bi-list toggle-sidebar-btn text-white"></i>
    </div><!-- End Logo -->
<?php

use Illuminate\Support\Facades\DB;

$new_users = DB::select("
    SELECT chats.id, users.name, chats.customer_id, chats.text, chats.created_at 
    FROM chats 
    JOIN users ON chats.customer_id = users.id
    WHERE chats.is_read = 0 
    AND chats.is_admin = 0 
    ORDER BY chats.created_at DESC
"); 
// echo "<pre>"; print_r($new_users); exit;
?>
    <div class="search-bar">
      <form class="search-form d-flex align-items-center mb-0" method="POST" action="#">
        <input type="text" name="query" id="search-input" placeholder="Search" title="Enter search keyword">
        <button type="submit" title="Search"><i class="bi bi-search"></i></button>
      </form>
    </div><!-- End Search Bar -->

    <div class="btn-container">
      <label class="switch btn-color-mode-switch">
          <input 
              value="1" 
              id="color_mode" 
              name="color_mode" 
              type="checkbox"
              onclick="changeLanguage()"
              {{ app()->getLocale() == 'ar' ? 'checked' : '' }}>
          <label 
              class="btn-color-mode-switch-inner" 
              data-off="EN" 
              data-on="العربية" 
              for="color_mode">
          </label>
      </label>
    </div>


    <!-- <a href="{{ url('lang/en') }}" class="mx-30px {{ app()->getLocale() == 'en' ? 'text-white' : 'text-dark' }}">English</a>
    <a href="{{ url('lang/ar') }}" class="{{ app()->getLocale() == 'ar' ? 'text-white' : 'text-dark' }}">العربية</a> -->

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">

        <li class="nav-item d-block d-lg-none">
          <a class="nav-link nav-icon search-bar-toggle " href="#">
            <i class="bi bi-search"></i>
          </a>
        </li><!-- End Search Icon-->

        <li class="nav-item dropdown d-none d-md-block">
          <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
            <i class="bi bi-bell text-white"></i>
            <span class="badge bg-primary badge-number">{{count($new_users)}}</span>
          </a><!-- End Notification Icon -->

          <ul class="dropdown-menu {{ app()->isLocale('ar') ? 'dropdown-menu-start' : 'dropdown-menu-end' }} dropdown-menu-arrow notifications">
            <li class="dropdown-header">
            {{trans('lang.you_have')}} {{count($new_users)}} {{trans('lang.new_notifications')}}
              <!-- <a href="{{ route('notifications.edit',['all', "choice" => "is_read"]) }}"><span class="badge rounded-pill bg-primary p-2 ms-2">{{trans('lang.read_all')}}</span></a> -->
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>
          <?php foreach($new_users as $key => $row) {
              
                echo '
                <li class="notification-item">
                  <i class="bi bi-exclamation-circle text-warning"></i>
                  <a href="'.route('chats.show', $row->customer_id) .'">
                  <div>
                    <h4>'.$row->name.' <a href="'.route('chats.show', $row->customer_id) .'" style="'.(app()->isLocale('ar') ? "margin-right:50px;" : "margin-left:100px;").'" class="text-sm" href="#"><small>'.trans('lang.read').'</small></a></h4>
                    <p>'.$row->text.'</p>
                    <p>'.formatCreatedAt($row->created_at).'</p>
                  </div>
                  </a>
                </li>
                <li>
                  <hr class="dropdown-divider">
                </li>';
            }
            ?>
            

          </ul><!-- End Notification Dropdown Items -->

        </li><!-- End Notification Nav -->

        <!-- <li class="nav-item dropdown">

          <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
            <i class="bi bi-chat-left-text"></i>
            <span class="badge bg-success badge-number">3</span>
          </a> End Messages Icon 

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow messages">
            <li class="dropdown-header">
            {{trans('lang.you_have')}} 3 {{trans('lang.new_messages')}}
              <a href="#"><span class="badge rounded-pill bg-primary p-2 ms-2">{{trans('lang.view_all')}}</span></a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="message-item">
              <a href="#">
                <img src="assets/img/messages-1.jpg" alt="" class="rounded-circle">
                <div>
                  <h4>Maria Hudson</h4>
                  <p>Velit asperiores et ducimus soluta repudiandae labore officia est ut...</p>
                  <p>4 hrs. ago</p>
                </div>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="message-item">
              <a href="#">
                <img src="assets/img/messages-2.jpg" alt="" class="rounded-circle">
                <div>
                  <h4>Anna Nelson</h4>
                  <p>Velit asperiores et ducimus soluta repudiandae labore officia est ut...</p>
                  <p>6 hrs. ago</p>
                </div>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="message-item">
              <a href="#">
                <img src="assets/img/messages-3.jpg" alt="" class="rounded-circle">
                <div>
                  <h4>David Muldon</h4>
                  <p>Velit asperiores et ducimus soluta repudiandae labore officia est ut...</p>
                  <p>8 hrs. ago</p>
                </div>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="dropdown-footer">
              <a href="#">{{trans('lang.show_all_messages')}}</a>
            </li>

          </ul> End Messages Dropdown Items 

        </li> -->
        <!-- End Messages Nav -->

        <li class="nav-item dropdown pe-3">
          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="{{route('profile')}}" data-bs-toggle="dropdown">
            <!-- <img src="{{asset('images/'.Auth::user()->image)}}" alt="" class="rounded-circle"> -->
            <img src="{{asset('img/user-circle-img.png')}}" alt="">
            <!-- <span class="d-none d-md-block dropdown-toggle ps-2 text-white">{{Auth::user()->name}}</span> -->
          </a><!-- End Profile Iamge Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6>{{Auth::user()->name}}</h6>
              {{-- <span>Web Designer</span> --}}
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="{{route('profile')}}">
                <i class="bi bi-person"></i>
                <span>{{trans('lang.my_profile')}}</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <!-- <li>
              <a class="dropdown-item d-flex align-items-center" href="users-profile.html">
                <i class="bi bi-gear"></i>
                <span>{{trans('lang.account_settings')}}</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="pages-faq.html">
                <i class="bi bi-question-circle"></i>
                <span>{{trans('lang.need_help?')}}</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li> -->

            <li>
              <a class="dropdown-item d-flex align-items-center"    onclick="event.preventDefault();
              document.getElementById('logout-form').submit();" href="{{ route('logout') }}">
                <i class="bi bi-box-arrow-right"></i>
                <span>{{trans('lang.sign_out')}}</span>
              </a>
            </li>

          </ul><!-- End Profile Dropdown Items -->
        </li><!-- End Profile Nav -->

      </ul>
      <div class="dropdown-menu" aria-labelledby="navbarDropdown">
        <a class="dropdown-item" href="{{ route('logout') }}"
           onclick="event.preventDefault();
                         document.getElementById('logout-form').submit();">
            {{ trans('lang.logout') }}
        </a>


        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>
    </nav><!-- End Icons Navigation -->

  </header><!-- End Header -->