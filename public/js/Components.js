// Sidebar

class Sidebar extends HTMLElement {
  constructor() {
    super();
  }
  connectedCallback() {
    this.innerHTML = `

<div>
  <!-- Mobile menu -->
  <div class="relative z-50 lg:hidden" role="dialog" aria-modal="true">

    <div x-show="mobilemenuOpen" x-cloak class="fixed inset-0 bg-gray-900/80"></div>

    <div x-show="mobilemenuOpen" x-cloak class="fixed inset-0 flex">

      <div class="relative mr-16 flex w-full max-w-xs flex-1">

        <div x-show="mobilemenuOpen" x-cloak class="absolute left-full top-0 flex w-16 justify-center pt-5">
          <button x-on:click="mobilemenuOpen = false" type="button" class="-m-2.5 p-2.5">
            <span class="sr-only">Close sidebar</span>
            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
              aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div x-transition:enter-start=" transform -translate-x-full duration-200"
          x-transition:enter-end=" transform translate-x-0 duration-200"
          x-transition:leave="transition ease-in duration-200"
          x-transition:leave-end="transform -translate-x-full duration-200" x-show="mobilemenuOpen" x-cloak
          x-on:click.away="mobilemenuOpen=false" class="flex grow flex-col gap-y-5 overflow-y-auto bg-white px-6 pb-4">
          <div class="h-4"></div>
          <div class="flex h-16 shrink-0 items-center">
            <img class="w-44 h-24" src="../../assets/svg/logo.svg" alt="BizWiz logo">
          </div>
          <ul role="list" class="flex flex-1 flex-col gap-y-7 ">
            <li>
              <ul role="list" class="-mx-2 ">
                <li>
                  <!-- Current: "bg-indigo-700 text-white", Default: "text-indigo-200 hover:text-white hover:bg-indigo-700" -->
                  <a href="../"
                    class="text-base font-medium text-sky-700 w-full block text-left hover:bg-sky-50 px-4 py-2 rounded-md">
                    Dashboard
                  </a>

                </li>

                <li>
                  <a href="#"
                    class="text-base font-medium text-slate-700 w-full block text-left hover:bg-sky-50 px-4 py-2 rounded-md">
                    LPOs
                  </a>
                </li>

                <li>
                  <a href="#"
                    class="text-base font-medium text-slate-700 w-full block text-left hover:bg-sky-50 px-4 py-2 rounded-md">
                    Agreements
                  </a>
                </li>
<!--
           <li>
                  <a href="#"
                    class="text-base font-medium text-slate-700 w-full block text-left hover:bg-sky-50 px-4 py-2 rounded-md">
                    Reports
                  </a>
                </li> -->



                <li>
                  <div x-data="{
                                                            dropdownOpen: false
                                                        }" class="relative">

                    <button x-on:click="dropdownOpen=!dropdownOpen"
                      class="bg-white text-base font-semibold leading-normal text-slate-700 rounded-md hover:bg-sky-50 px-4 py-2 flex w-full justify-between ">
                      <div class="">

                        Settings
                      </div>
                      <svg class="transition-all ease-in-out duration-200"
                        x-bind:class="{'rotate-180': dropdownOpen, 'rotate-0': !dropdownOpen }" width="20" height="21"
                        viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5 8.48633L10 13.4863L15 8.48633" stroke="#667085" stroke-width="1.66667"
                          stroke-linecap="round" stroke-linejoin="round">
                        </path>
                      </svg>



                    </button>

                    <div x-show="dropdownOpen" x-transition:enter="ease-out duration-200"
                    x-transition:enter-start="-translate-y-2" x-transition:enter-end="translate-y-0" class="w-full"
                    x-cloak>
                    <div class=" bg-white mt-1  text-neutral-700">
                      <ul>
                        <li>
                          <a href="../../Settings/Staff.html"
                            class="text-base font-medium text-slate-700 w-full block text-left hover:bg-sky-50 px-4 py-2 rounded-md">
                            Staff
                          </a>
                        </li>
                          <li>
                          <a href="../../Settings/Roles.html"
                            class="text-base font-medium text-slate-700 w-full block text-left hover:bg-sky-50 px-4 py-2 rounded-md">
                        Roles
                          </a>
                        </li>

                              <li>
                          <a href="../../Settings/Vehicles.html"
                            class="text-base font-medium text-slate-700 w-full block text-left hover:bg-sky-50 px-4 py-2 rounded-md">
                    Vehicles
                          </a>
                        </li>


                      </ul>
                    </div>
                  </div>
                  </div>
                </li>

              </ul>
            </li>

          </ul>
        </div>
      </div>
    </div>
  </div>

  <!-- Static sidebar for desktop -->
  <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-64 lg:flex-col">
    <!-- Sidebar component, swap this element with another sidebar if you like -->
    <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-white px-6 pb-4 pt-4">
      <div class="flex h-24 shrink-0 items-center">
        <img class="w-44 h-24" src="../../assets/svg/logo.svg" alt="BizWiz logo">
      </div>
      <nav class="flex flex-1 flex-col">
        <ul role="list" class="flex flex-1 flex-col gap-y-7 ">
          <li>
            <ul role="list" class="-mx-2 ">
              <li>
                <!-- Current: "bg-indigo-700 text-white", Default: "text-indigo-200 hover:text-white hover:bg-indigo-700" -->
                <a href="../../Index.html"
                  class="text-base font-medium text-sky-700 w-full block text-left hover:bg-sky-50 px-12 py-2 rounded-md">
                  Dashboard
                </a>

              </li>

              <li>
                <a href="../../LPO/Index.html"
                  class="text-base font-medium text-slate-700 w-full block text-left hover:bg-sky-50 px-12 py-2 rounded-md">
                  LPOs
                </a>
              </li>

              <li>
                <a href="../../Trade/Index.html"
                  class="text-base font-medium text-slate-700 w-full block text-left hover:bg-sky-50 px-12 py-2 rounded-md">
                  Agreements
                </a>
              </li>




              <li>
                <div x-data="{
                                                          dropdownOpen: false
                                                      }" class="relative">

                  <button x-on:click="dropdownOpen=!dropdownOpen"
                    class="bg-white text-base font-semibold leading-normal text-slate-700 rounded-md hover:bg-sky-50 px-12 py-2 flex w-full justify-between ">
                    <div class="">

                      Settings
                    </div>
                    <svg class="transition-all ease-in-out duration-200"
                      x-bind:class="{'rotate-180': dropdownOpen, 'rotate-0': !dropdownOpen }" width="20" height="21"
                      viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M5 8.48633L10 13.4863L15 8.48633" stroke="#667085" stroke-width="1.66667"
                        stroke-linecap="round" stroke-linejoin="round">
                      </path>
                    </svg>



                  </button>

                  <div x-show="dropdownOpen" x-transition:enter="ease-out duration-200"
                    x-transition:enter-start="-translate-y-2" x-transition:enter-end="translate-y-0" class="w-full"
                    x-cloak>
                    <div class=" bg-white mt-1  text-neutral-700">
                      <ul>
                        <li>
                          <a href="../../Settings/Staff.html"
                            class="text-base font-medium text-slate-700 w-full block text-left hover:bg-sky-50 px-12 py-2 rounded-md">
                            Staff
                          </a>
                        </li>
                          <li>
                          <a href="../../Settings/Roles.html"
                            class="text-base font-medium text-slate-700 w-full block text-left hover:bg-sky-50 px-12 py-2 rounded-md">
                        Roles
                          </a>
                        </li>

                              <li>
                          <a href="../../Settings/Vehicles.html"
                            class="text-base font-medium text-slate-700 w-full block text-left hover:bg-sky-50 px-12 py-2 rounded-md">
                    Vehicles
                          </a>
                        </li>


                      </ul>
                    </div>
                  </div>
                </div>
              </li>

            </ul>
          </li>

        </ul>
      </nav>
    </div>
  </div>


</div>

`;
  }
}

// Top bar

class TopBar extends HTMLElement {
  constructor() {
    super();
  }
  connectedCallback() {
    this.innerHTML = `

<div>
  <div class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 b px-4 sm:gap-x-4 sm:px-6 lg:px-8">
    <button x-on:click="mobilemenuOpen = true" type="button" class="-m-2.5 p-2.5 text-gray-700 lg:hidden">
      <span class="sr-only">Open sidebar</span>
      <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
      </svg>
    </button>



    <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">


      <div class="flex items-center justify-end w-full gap-x-4 lg:gap-x-4">
        <button type="button" class=" bg-white hover:bg-gray-50 px-2 border border-gray-200 rounded-md py-2">
          <svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
              d="M10.4154 12.4993C11.7961 12.4993 12.9154 11.3801 12.9154 9.99935C12.9154 8.61864 11.7961 7.49935 10.4154 7.49935C9.03465 7.49935 7.91536 8.61864 7.91536 9.99935C7.91536 11.3801 9.03465 12.4993 10.4154 12.4993Z"
              stroke="#667085" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"></path>
            <path
              d="M16.0214 12.2721C15.9206 12.5006 15.8905 12.754 15.9351 12.9998C15.9796 13.2455 16.0968 13.4723 16.2714 13.6509L16.3169 13.6963C16.4578 13.837 16.5695 14.0041 16.6458 14.1881C16.722 14.372 16.7613 14.5692 16.7613 14.7683C16.7613 14.9674 16.722 15.1646 16.6458 15.3485C16.5695 15.5324 16.4578 15.6995 16.3169 15.8403C16.1762 15.9811 16.0091 16.0929 15.8251 16.1691C15.6412 16.2454 15.444 16.2846 15.2449 16.2846C15.0458 16.2846 14.8486 16.2454 14.6647 16.1691C14.4808 16.0929 14.3137 15.9811 14.1729 15.8403L14.1275 15.7948C13.9489 15.6202 13.7222 15.503 13.4764 15.4584C13.2307 15.4139 12.9772 15.444 12.7487 15.5448C12.5246 15.6408 12.3335 15.8003 12.1989 16.0035C12.0643 16.2068 11.9921 16.445 11.9911 16.6887V16.8175C11.9911 17.2194 11.8315 17.6048 11.5473 17.8889C11.2632 18.1731 10.8778 18.3327 10.476 18.3327C10.0741 18.3327 9.68874 18.1731 9.4046 17.8889C9.12045 17.6048 8.96082 17.2194 8.96082 16.8175V16.7493C8.95495 16.4986 8.87379 16.2554 8.72787 16.0514C8.58196 15.8474 8.37804 15.692 8.14264 15.6054C7.91414 15.5046 7.66067 15.4745 7.41492 15.519C7.16917 15.5636 6.94239 15.6808 6.76385 15.8554L6.7184 15.9009C6.57768 16.0417 6.41057 16.1535 6.22664 16.2297C6.0427 16.306 5.84554 16.3452 5.64643 16.3452C5.44731 16.3452 5.25015 16.306 5.06621 16.2297C4.88228 16.1535 4.71517 16.0417 4.57446 15.9009C4.43358 15.7601 4.32183 15.593 4.24558 15.4091C4.16933 15.2252 4.13008 15.028 4.13008 14.8289C4.13008 14.6298 4.16933 14.4326 4.24558 14.2487C4.32183 14.0647 4.43358 13.8976 4.57446 13.7569L4.61991 13.7115C4.79456 13.5329 4.91172 13.3062 4.95628 13.0604C5.00084 12.8146 4.97075 12.5612 4.86991 12.3327C4.77388 12.1086 4.61442 11.9175 4.41117 11.7829C4.20792 11.6483 3.96975 11.5761 3.72597 11.5751H3.59718C3.19534 11.5751 2.80995 11.4155 2.52581 11.1313C2.24166 10.8472 2.08203 10.4618 2.08203 10.06C2.08203 9.65811 2.24166 9.27273 2.52581 8.98858C2.80995 8.70444 3.19534 8.5448 3.59718 8.5448H3.66536C3.91612 8.53894 4.15931 8.45777 4.36332 8.31186C4.56733 8.16594 4.72273 7.96203 4.8093 7.72662C4.91015 7.49813 4.94023 7.24466 4.89567 6.9989C4.85111 6.75315 4.73395 6.52638 4.5593 6.34783L4.51385 6.30238C4.37298 6.16166 4.26122 5.99456 4.18497 5.81062C4.10872 5.62669 4.06948 5.42952 4.06948 5.23041C4.06948 5.0313 4.10872 4.83413 4.18497 4.6502C4.26122 4.46626 4.37298 4.29916 4.51385 4.15844C4.65457 4.01757 4.82167 3.90581 5.00561 3.82956C5.18954 3.75331 5.3867 3.71407 5.58582 3.71407C5.78493 3.71407 5.9821 3.75331 6.16603 3.82956C6.34997 3.90581 6.51707 4.01757 6.65779 4.15844L6.70324 4.20389C6.88179 4.37854 7.10856 4.4957 7.35431 4.54026C7.60007 4.58482 7.85353 4.55474 8.08203 4.45389H8.14264C8.36671 4.35786 8.5578 4.19841 8.69241 3.99516C8.82701 3.79191 8.89924 3.55373 8.90021 3.30996V3.18117C8.90021 2.77932 9.05985 2.39394 9.34399 2.10979C9.62814 1.82565 10.0135 1.66602 10.4154 1.66602C10.8172 1.66602 11.2026 1.82565 11.4867 2.10979C11.7709 2.39394 11.9305 2.77932 11.9305 3.18117V3.24935C11.9315 3.49313 12.0037 3.7313 12.1383 3.93455C12.2729 4.1378 12.464 4.29726 12.6881 4.39329C12.9166 4.49413 13.1701 4.52422 13.4158 4.47966C13.6616 4.4351 13.8883 4.31794 14.0669 4.14329L14.1123 4.09783C14.2531 3.95696 14.4202 3.8452 14.6041 3.76896C14.788 3.69271 14.9852 3.65346 15.1843 3.65346C15.3834 3.65346 15.5806 3.69271 15.7645 3.76896C15.9485 3.8452 16.1156 3.95696 16.2563 4.09783C16.3971 4.23855 16.5089 4.40565 16.5852 4.58959C16.6614 4.77353 16.7006 4.97069 16.7006 5.1698C16.7006 5.36892 16.6614 5.56608 16.5852 5.75002C16.5089 5.93395 16.3971 6.10106 16.2563 6.24177L16.2108 6.28723C16.0362 6.46577 15.919 6.69254 15.8745 6.9383C15.8299 7.18405 15.86 7.43752 15.9608 7.66602V7.72662C16.0569 7.95069 16.2163 8.14179 16.4196 8.27639C16.6228 8.41099 16.861 8.48323 17.1048 8.4842H17.2335C17.6354 8.4842 18.0208 8.64383 18.3049 8.92798C18.5891 9.21212 18.7487 9.59751 18.7487 9.99935C18.7487 10.4012 18.5891 10.7866 18.3049 11.0707C18.0208 11.3549 17.6354 11.5145 17.2335 11.5145H17.1654C16.9216 11.5155 16.6834 11.5877 16.4802 11.7223C16.2769 11.8569 16.1175 12.048 16.0214 12.2721Z"
              stroke="#667085" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"></path>
          </svg>
        </button>


        <!-- Profile dropdown -->
        <div class="relative" x-data="{
                dropdownOpen: false
            }">
          <button type="button" class="-m-1.5 flex items-center p-1.5" id="user-menu-button" aria-expanded="false"
            x-on:click="dropdownOpen=!dropdownOpen" aria-haspopup="true">
            <span class="sr-only">Open user menu</span>
            <img class="h-8 w-8 rounded-full bg-gray-50" src="https://i.pravatar.cc/32?img=51 alt="">
                  <span class=" hidden lg:flex lg:items-center">
            <span class="ml-4 text-sm font-semibold leading-6 text-gray-900" aria-hidden="true">Wilson Mwangi</span>
            <svg class="ml-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
              <path fill-rule="evenodd"
                d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                clip-rule="evenodd" />
            </svg>
            </span>
          </button>


          <div x-transition:enter="ease-out duration-200" x-transition:enter-start="-translate-y-2" x-cloak
            x-show="dropdownOpen" x-on:click.away="dropdownOpen=false" x-transition:enter-end="translate-y-0"
            class="absolute right-0 z-10 mt-2.5 w-32 origin-top-right rounded-md bg-white  shadow-lg ring-1 ring-gray-900/5 focus:outline-none"
            role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
            <!-- Active: "bg-gray-50", Not Active: "" -->
            <a href="#" class="block px-3 py-1 text-sm leading-6 hover:bg-sky-50 text-gray-900" role="menuitem"
              tabindex="-1" id="user-menu-item-0">Your profile</a>
            <a href="../../Index.html" class="block px-3 py-1 text-sm leading-6 text-gray-900 hover:bg-sky-50" role="menuitem"
              tabindex="-1" id="user-menu-item-1">Sign out</a>
          </div>
        </div>
      </div>
    </div>
  </div>


</div>

`;
  }
}

customElements.define("main-sidebar", Sidebar);
customElements.define("main-topbar", TopBar);
