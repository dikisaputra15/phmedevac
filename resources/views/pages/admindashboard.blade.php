@extends('layouts.master-admin')

@section('title', 'Dashboard')

@section('page-title', 'Philippine Crisis Management Tools')

@push('styles')

    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.fullscreen/1.6.0/Control.FullScreen.css" />

    <style>
        #map {
            height: 700px;
        }
        .filter-container {
            margin-bottom: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
        }
        .form-check-scrollable {
            max-height: 150px;
            overflow-y: auto;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
        }
        .total-info {
            background: white;
            padding: 8px 12px;
            border-radius: 8px;
            box-shadow: 0 0 6px rgba(0,0,0,0.2);
            font-weight: bold;
            margin-left: 10px;
        }

        .select2-container .select2-selection--single {
            height: 45px;
            padding: 6px 12px;
            border: 1px solid #ced4da;
            border-radius: 10px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 30px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 45px;
            right: 10px;
        }

        .p-modal{
            text-align:justify;
        }
        .hospital-legend-item {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 0 5px;
        }
        .hospital-legend-item img {
            width: 30px;
            height: 30px;
        }

        p{
        margin-bottom: 8px;
            line-height: 18px;
        }

        .btn-danger{
            background-color:#395272;
            border-color: transparent;
        }

        .btn-danger:hover{
            background-color:#5686c3;
            border-color: transparent;
        }

        .btn.active {
            background-color: #5686c3 !important;
            border-color: transparent !important;
            color: #fff !important;
        }

        .p-3{
            padding: 10px !important;
            margin: 0 3px;
        }

        .btn-outline-danger{
            color: #FFFFFF;
            background-color:#395272;
            border-color: transparent;
        }

        .btn-outline-danger:hover{
            background-color:#5686c3;
            border-color: transparent;
        }

        .fa,
        .fab,
        .fad,
        .fal,
        .far,
        .fas {
            color: #346abb;
        }

        .card-header{
            padding: 0.25rem 1.25rem;
            color: #3c66b5;
            font-weight: bold;
        }

        .mb-4{
            margin-bottom: 0.5rem !important;
        }

    /* Classification section */
    .classification {
      display: flex;
      width: 100%;
    }

    .class-column {
      flex: 1;
      text-align: center;

    }
    .class-column:last-child {
      border-right: none;
    }

    .class-header {
      font-weight: 600;
      padding: 0.1rem 0;
    }

    /* Color bars */
    .class-medical-classification {border: none; text-align: center;}
    .class-airport-category {border: none;}
    .class-advanced { border-bottom: 3px solid #0070c0; }
    .class-intermediate { border-bottom: 3px solid #00b050; padding-inline-start: 20px; padding-inline-end: 20px; }
    .class-basic { border-bottom: 3px solid #ffc000; }

    /* Hospital layout */
    .hospital-list {
      display: flex;
      flex-direction: column;
      align-items: center;

    }

    /* For side-by-side classes */
    .hospital-row {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 0;
    }

    .hospital-item {
      display: flex;
      align-items: center;
      gap: 0;
      font-size: 0.9rem;
      white-space: nowrap;
    }

    .hospital-icon {
      width: 18px;
      height: 18px;
      border-radius: 3px;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
    }

    /* Image inside icon box */
    .hospital-icon img {
      width: 100%;
      height: 100%;
      object-fit: contain;
    }

    /* Airfield icons */
    .category-item img {
      width: 16px;
      height: 16px;
      object-fit: contain;
    }

     .select-input {
        border: 1px solid #ccc;
        border-radius: 6px;
        padding: 8px 10px;
        background: #fff;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .select-input input {
        border: none;
        width: 100%;
        cursor: pointer;
        background: transparent;
        outline: none;
    }

    .select-dropdown {
        display: none;
        position: absolute;
        width: 100%;
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 6px;
        margin-top: 3px;
        z-index: 9999;
        max-height: 250px;
        overflow: hidden;
    }

    .select-dropdown.show {
        display: block;
    }

    .dropdown-search {
        width: 100%;
        border: none;
        border-bottom: 1px solid #ddd;
        padding: 8px;
        outline: none;
    }

    #provinceList {
        list-style: none;
        padding: 0;
        margin: 0;
        max-height: 180px;
        overflow-y: auto;
    }

    #provinceList li {
        padding: 5px 10px;
    }

    #provinceList li:hover {
        background: #f5f5f5;
    }

    #provinceList label {
        width: 100%;
        margin: 0;
        cursor: pointer;
    }
</style>

@endpush

@section('conten')

<div class="card">
    <div class="row" style="background-color: #dfeaf1;">
        <div class="col-md-9">
            <div class="d-flex p-3" style="justify-content: center;">
                <div class="d-flex gap-2">

                <!-- Airport -->
                      <div class="class-column" style="margin-right: 100px;">
                        <div class="class-header class-airport-category">AIRFIELD CLASSIFICATION</div>
                        <div class="airport-list">
                          <div class="hospital-row" style="flex-direction: column;">
                            <!-- Airport row 1 -->
                            <div class="hospital-item">
                              <button class="btn p-1" data-bs-toggle="modal" data-bs-target="#level6Modal">
                                  <img src="https://pg.concordreview.com/wp-content/uploads/2024/10/International-Airport.png" style="width:18px; height:18px;">
                                  <small>International</small>
                              </button>

                              <button class="btn p-1" data-bs-toggle="modal" data-bs-target="#level5Modal">
                                  <img src="https://pg.concordreview.com/wp-content/uploads/2025/01/regional-airport.png" style="width:18px; height:18px;">
                                  <small>Domestic</small>
                              </button>

                              <button class="btn p-1" data-bs-toggle="modal" data-bs-target="#level4Modal">
                                  <img src="https://pg.concordreview.com/wp-content/uploads/2025/01/regional-domestic-airport.png" style="width:18px; height:18px;">
                                  <small>Regional</small>
                              </button>
                            </div>
                            <!-- Airport row 2 -->
                            <div class="hospital-item">
                              <button class="btn p-1" data-bs-toggle="modal" data-bs-target="#level2Modal">
                                  <img src="https://pg.concordreview.com/wp-content/uploads/2024/10/civil-military-airport.png" style="width:18px; height:18px;">
                                  <small>Civil-Military</small>
                              </button>

                              <button class="btn p-1" data-bs-toggle="modal" data-bs-target="#level3Modal">
                                  <img src="https://pg.concordreview.com/wp-content/uploads/2024/10/military-airport-red.png" style="width:18px; height:18px;">
                                  <small>Military</small>
                              </button>

                              <button class="btn p-1" data-bs-toggle="modal" data-bs-target="#level1Modal">
                                  <img src="https://pg.concordreview.com/wp-content/uploads/2025/01/private-airport.png" style="width:18px; height:18px;">
                                  <small>Private</small>
                              </button>
                            </div>
                          </div>

                        </div>
                      </div>

                      <!-- Medical Facility Legend -->
                      <div style="flex-direction: column;">
                        <!-- Title -->
                        <div>
                            <div class="class-header class-medical-classification">MEDICAL FACILITY CLASSIFICATION</div>
                        </div>
                        <div style="display: flex; flex-direction: row;">
                            <!-- Advanced -->
                            <div class="class-column">
                              <div class="class-header class-advanced">Advanced</div>
                              <div class="hospital-list">
                                <div class="hospital-item">
                                  <button class="btn p-1" data-bs-toggle="modal" data-bs-target="#level66Modal">
                                    <img src="https://pg.concordreview.com/wp-content/uploads/2025/01/hospital-pin-red.png" style="width:24px; height:24px;">
                                    <small>Level 3</small>
                                  </button>
                                </div>
                              </div>
                            </div>

                            <!-- Intermediate -->
                            <div class="class-column">
                              <div class="class-header class-intermediate">Intermediate</div>
                              <div class="hospital-list">
                                <div class="hospital-row">
                                  <div class="hospital-item">
                                    <button class="btn p-1" data-bs-toggle="modal" data-bs-target="#level55Modal">
                                      <img src="https://pg.concordreview.com/wp-content/uploads/2025/01/hospital_pin-blue.png" style="width:24px; height:24px;">
                                      <small>Level 2</small>
                                    </button>
                                  </div>
                                </div>
                              </div>
                            </div>

                            <!-- Basic -->
                            <div class="class-column">
                              <div class="class-header class-basic">Basic</div>
                              <div class="hospital-list">
                                <div class="hospital-row">
                                    <div class="hospital-item">
                                    <button class="btn p-1" data-bs-toggle="modal" data-bs-target="#level44Modal">
                                      <img src="https://pg.concordreview.com/wp-content/uploads/2025/01/hospital_pin-purple.png" style="width:24px; height:24px;">
                                      <small>Level 1</small>
                                    </button>
                                  </div>
                                  <div class="hospital-item">
                                    <button class="btn p-1" data-bs-toggle="modal" data-bs-target="#level11Modal">
                                        <img src="https://pg.concordreview.com/wp-content/uploads/2025/01/hospital_pin-tosca.png" style="width:24px; height:24px;">
                                        <small>Primary Care Facility (Level A)</small>
                                    </button>
                                  </div>
                                </div>
                              </div>
                            </div>
                        </div>
                      </div>

                       <div class="class-column" style="margin-left: 50px;">
                        <div class="class-header class-airport-category">POLICE CLASSIFICATION</div>

                        <div class="airport-list">
                            <div class="hospital-row" style="flex-direction: column;">

                                <!-- Baris Atas (3) -->
                                <div class="hospital-item">
                                    <button class="btn p-1">
                                        <img src="{{ asset('images/dot-blue-ring-royal-papua.png') }}" style="width:12px; height:12px;">
                                        <small>National Police (HQ)</small>
                                    </button>

                                    <button class="btn p-1">
                                        <img src="{{ asset('images/dot-red.png') }}" style="width:12px; height:12px;">
                                        <small>Police Regional Office (PRO)</small>
                                    </button>
                                </div>

                                <!-- Baris Bawah (2) -->
                                <div class="hospital-item">
                                    <button class="btn p-1">
                                         <img src="{{ asset('images/dot-orange-ppc.png') }}" style="width:12px; height:12px;">
                                        <small>Provincial Police Office (PPO)</small>
                                    </button>

                                    <button class="btn p-1">
                                        <img src="{{ asset('images/dot-green.png') }}" style="width:12px; height:12px;">
                                        <small>City Police Office (CPO)</small>
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
         <div class="col-md-3">
            <div class="d-flex justify-content-end p-3">
                <div class="d-flex gap-2 mt-2">

                    <a href="{{ url('airports') }}" class="btn btn-danger d-flex flex-column align-items-center p-3 {{ request()->is('airports') ? 'active' : '' }}">
                        <i class="bi bi-airplane fs-3"></i>
                        <small>Aviation</small>
                    </a>

                    <a href="{{ url('aircharter') }}" class="btn btn-danger d-flex flex-column align-items-center p-3 {{ request()->is('aircharter') ? 'active' : '' }}">
                        <img src="{{ asset('images/icon-air-charter.png') }}" style="width: 48px; height: 24px;">
                        <small>Air Charter</small>
                    </a>

                    <a href="{{ url('hospital') }}" class="btn btn-danger d-flex flex-column align-items-center p-3 {{ request()->is('hospital') ? 'active' : '' }}">
                    <img src="{{ asset('images/icon-medical.png') }}" style="width: 24px; height: 24px;">
                        <small>Medical</small>
                    </a>

                    <a href="{{ url('police') }}" class="btn btn-danger d-flex flex-column align-items-center p-3 {{ request()->is('police') ? 'active' : '' }}">
                    <i class="bi bi-person-badge" style="width: 24px; height: 24px;"></i>
                        <small>Police</small>
                    </a>

                    <a href="{{ url('embassiees') }}" class="btn btn-danger d-flex flex-column align-items-center p-3 {{ request()->is('embassiees') ? 'active' : '' }}">
                    <img src="{{ asset('images/icon-embassy.png') }}" style="width: 24px; height: 24px;">
                        <small>Embassies</small>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <button class="btn btn-link p-0 fw-bold text-decoration-underline text-dark" data-bs-toggle="modal" data-bs-target="#disclaimerModal">
            <i class="bi bi-info-circle text-primary fs-5"></i>
            <small>Disclaimer</small>
        </button>
    </div>

</div>

<div id="map"></div>

<div class="modal fade" id="disclaimerModal" tabindex="-1" aria-labelledby="disclaimerLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="disclaimerLabel">Disclaimer</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
       <p class="p-modal text-justify">Every attempt has been made to ensure the completeness and accuracy of the most updated information and data available. Clients are advised, however, that provided information, and data is subject to change.</p>
       <h5 class="modal-title" id="disclaimerLabel">Google Maps Link</h5>
       <p class="p-modal text-justify">Google Maps may automatically display or translate content based on the user’s current region, browser settings, or Google account preferences. This issue may occur when opening google maps link from TCMT platform using Microsoft Edge. For the best experience, we recommend opening the Google Chrome link while logged into your Google account. You can also use your browser’s translation feature to view Google Maps in your preferred language.</p>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="level1Modal" tabindex="-1" aria-labelledby="disclaimerLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <div class="d-flex align-items-center">
             <img src="https://pg.concordreview.com/wp-content/uploads/2025/01/private-airport.png" style="width:30px; height:30px;">
            <h5 class="modal-title" id="disclaimerLabel">Private Airfield</h5>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="p-modal">Also known as private airfields or airstrips are primarily used for general and private aviation are owned by private individuals, groups, corporations, or organizations operated for their exclusive use that may include limited access for authorized personnel by the owner or manager. Owners are responsible to ensure safe operation, maintenance, repair, and control of who can use the facilities. Typically, they are not open to the public or provide scheduled commercial airline services and cater to private pilots, business aviation, and sometimes small charter operations. Services may be provided if authorized by the appropriate regulatory authority.</p>

        <p class="p-modal">A large majority of private airports are grass or dirt strip fields without services or facilities, they may feature amenities such as hangars, fueling facilities, maintenance services, and ground transportation options tailored to the needs of their owners or users. Private airports are not subject to the same level of regulatory oversight as public airports, but must still comply with applicable aviation regulations, safety standards, and environmental requirements. In the event of an emergency, landing at a private airport is authorized without any prior approval and should be done if landing anywhere else compromises the safety of the aircraft, crew, passengers, or cargo.</p>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="level2Modal" tabindex="-1" aria-labelledby="disclaimerLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <div class="d-flex align-items-center">
            <img src="https://pg.concordreview.com/wp-content/uploads/2024/10/civil-military-airport.png" style="width:30px; height:30px;">
            <h5 class="modal-title" id="disclaimerLabel">Combined Airfield</h5>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="p-modal">Also called "joint-use airport," are used by both civilian and military aircraft, where a formal agreement exists between the military and a local government agency allowing shared access to infrastructure and facilities, typically with separate passenger terminals and designated operating areas, airspace allocation, and aircraft scheduling. Features can include aircraft maintenance, air traffic control, communications, emergency response, and fuel storage.</p>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="level3Modal" tabindex="-1" aria-labelledby="disclaimerLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <div class="d-flex align-items-center">
             <img src="https://pg.concordreview.com/wp-content/uploads/2024/10/military-airport-red.png" style="width:30px; height:30px;">
            <h5 class="modal-title" id="disclaimerLabel">Military Airfield</h5>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="p-modal">Facilities where military aircraft operate, also known as a military airport, airbase, or air station. Features include aircraft maintenance, air traffic control, communications, emergency response, fuel and weapon storage, defensive systems, aircraft shelters, and personnel facilities.</p>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="level4Modal" tabindex="-1" aria-labelledby="disclaimerLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <div class="d-flex align-items-center">
            <img src="https://pg.concordreview.com/wp-content/uploads/2025/01/regional-domestic-airport.png" style="width:30px; height:30px;">
            <h5 class="modal-title" id="disclaimerLabel">Regional Domestic Airfield</h5>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="p-modal">A small or remote regional domestic airfield usually located in a geographically isolated area, far from major population centers, often with difficult terrain or vast distances from other airports with limited passenger traffic. May have shorter runways, basic facilities, and limited amenities, and basic infrastructure, serving primarily local communities providing access to essential services like medical transport or regional travel, rather than large-scale commercial flights.</p>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="level5Modal" tabindex="-1" aria-labelledby="disclaimerLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <div class="d-flex align-items-center">
            <img src="https://pg.concordreview.com/wp-content/uploads/2025/01/regional-airport.png" style="width:30px; height:30px;">
            <h5 class="modal-title" id="disclaimerLabel">Domestic Airfield</h5>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="p-modal">Exclusively manages flights that originate and end within the same country, does not have international customs or border control facilities. Airport often has smaller and shorter runways, suitable for smaller regional aircraft used on domestic routes, and cannot support larger haul aircraft having less developed support services. Features can include aircraft maintenance, air traffic control, communications, emergency response, and fuel storage.</p>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="level6Modal" tabindex="-1" aria-labelledby="disclaimerLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <div class="d-flex align-items-center">
            <img src="https://pg.concordreview.com/wp-content/uploads/2024/10/International-Airport.png" style="width:30px; height:30px;">
            <h5 class="modal-title" id="disclaimerLabel">International Airfield</h5>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="p-modal">Meet standards set by the International Air Transport Association (IATA) and the International Civil Aviation Organization (ICAO), facilitate transnational travel managing flights between countries, have customs and border control facilities to manage passengers and cargo, and may have dedicated terminals for domestic and international flights. International airports have longer runways to accommodate larger, heavier aircraft, are often a main hub for air traffic, and can serve as a base for larger airlines. Features can include aircraft maintenance, air traffic control, communications, emergency response, and fuel storage</p>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="level7Modal" tabindex="-1" aria-labelledby="disclaimerLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <div class="d-flex align-items-center">
            <img src="https://pg.concordreview.com/wp-content/uploads/2024/10/military-airport-red.png" style="width:30px; height:30px;">
            <h5 class="modal-title" id="disclaimerLabel">Military Airfield</h5>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="p-modal">Facilities where military aircraft operate, also known as a military airport, airbase, or air station. Features include aircraft maintenance, air traffic control, communications, emergency response, fuel and weapon storage, defensive systems, aircraft shelters, and personnel facilities.</p>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="level11Modal" tabindex="-1" aria-labelledby="disclaimerLabel" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered" style="max-width:800px;">
    <div class="modal-content">
      <div class="modal-header">
         <div class="d-flex align-items-center">
            <img src="https://pg.concordreview.com/wp-content/uploads/2025/01/hospital_pin-tosca.png" style="width:30px; height:30px;">
            <h5 class="modal-title" id="disclaimerLabel">Primary Care Medical Facilities (Public and Private)</h5>
         </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <h6 class="fw-bold">
            <b>Overview</b>
        </h6>
        <p class="text-justify">
            <b>Primary Care Facility</b> – a first-contact healthcare facility that offers basic services including emergency services and provision for normal deliveries, subdivided into:
        </p>

        <ol class="text-justify" type="a">
            <li>
                <b>With in-patient beds</b> – a short stay facility where patients can be admitted for a short period of 1 to 3 days, this includes infirmary, dispensary, and birthing home.
            </li>

            <li>
                <b>Without beds</b> – a facility where medical and/or dental examination and treatment and minor surgical procedures are rendered without confining the patient. This includes the following:

                <ol type="1">
                    <li>Medical Outpatient Clinic, Overseas Filipino Workers (OFW) Clinics, and Dental Clinics.</li>

                    <li>
                        <b>Infirmary</b> – a healthcare facility with in-patient beds capable of providing diagnosis and treatment of medical conditions and simple surgical procedures but lacks one or several components required of a hospital, including operating room and/or intensive care unit.
                    </li>

                    <li>
                        <b>Dispensary</b> – a healthcare facility where medicine or medical treatment is dispensed. Under the new DOH classification (AO 2012-0012), they are considered a primary care facility with in-patient beds.
                    </li>

                    <li>
                        <b>Birthing Home</b> – a facility with in-patient beds that provides maternity services (pre-natal, normal spontaneous delivery, post-natal care) and newborn care. Also called a maternity clinic.
                    </li>

                    <li>
                        <b>Medical Outpatient Clinic</b> – an institution or facility providing medical outpatient health services such as diagnostic examination, treatment, and health counseling.
                    </li>

                    <li>
                        <b>Specialty Hospital</b> – a hospital that specializes in a particular disease or condition or in one type of patient, licensed as such with no corresponding level of classification. This includes children’s hospitals and orthopedic hospitals.
                    </li>

                    <li>
                        <b>Geographically Isolated and Disadvantaged Areas (GIDA)</b> – communities with marginalized populations that are physically and socio-economically separated from mainstream society and characterized by:
                        <ol type="a">
                            <li>
                                <b>Physical Factors</b> – isolated due to distance, weather conditions, and transportation difficulties (island, upland, lowland, landlocked, hard-to-reach and underserved communities).
                            </li>
                            <li>
                                <b>Socio-economic Factors</b> – high poverty incidence, presence of vulnerable sectors, communities in or recovering from crisis or armed conflict.
                            </li>
                        </ol>
                        <i>Note: These areas are identified by the DOH.</i>
                    </li>

                    <li>
                        <b>Health Human Resource Shortage (HHRS) areas</b> – areas with inadequate health human resources, determined based on accessibility, terrain, distance, availability of public transport, socio-economic factors, peace and order, and evaluation reports from PhilHealth Regional Offices, DOH offices, and the National Statistics Coordination Board.
                    </li>
                </ol>
            </li>
        </ol>
        <h6 class="fw-bold">
            <b>Role</b>
        </h6>
        <p class="text-justify">
            <ul>
                <li>First-contact point for basic healthcare needs</li>
                <li>Provide basic medical, dental, emergency, maternal, newborn, and outpatient services</li>
                <li>Manage simple and uncomplicated cases</li>
                <li>Provide short-stay care when licensed with inpatient beds</li>
                <li>Refer patients to hospitals or specialized facilities when higher-level care is required</li>
            </ul>
        </p>
        <h6 class="fw-bold">
            <b>Clinical Services</b>
        </h6>
        <p class="text-justify">
            <ul>
                <li>
                    <strong>Bed Capacity</strong>
                    <ul>
                        <li>With inpatient beds: short-stay admission, usually around 1-3 days</li>
                        <li>Without beds: no patient confinement</li>
                        <li>Actual capacity depends on the facility's DOH license</li>
                    </ul>
                </li>
                <li class="mt-2">
                    <strong>Core Specialties</strong>
                    <ul>
                        <li>General medical care</li>
                        <li>Basic emergency care</li>
                        <li>Maternal and newborn care, where applicable</li>
                        <li>Dental care, where applicable</li>
                        <li>Health counseling and preventive care</li>
                    </ul>
                </li>
                <li class="mt-2">
                    <strong>Intermediate Services</strong>
                    <ul>
                        <li>With inpatient beds</li>
                        <li>Infirmary</li>
                        <li>Dispensary</li>
                        <li>Birthing home</li>
                        <li>Without beds</li>
                        <li>Medical outpatient clinic</li>
                        <li>OFW or seafarer clinic</li>
                        <li>Dental clinic</li>
                    </ul>
                </li>
                <li class="mt-2">
                    <strong>Surgical & Procedural Capacity</strong>
                    <ul>
                        <li>Minor procedures only, depending on license and staff capability</li>
                        <li>Simple surgical procedures may be available in infirmaries</li>
                        <li>Normal spontaneous delivery may be available in birth homes</li>
                        <li>Complex surgery, ICU care, high-risk pregnancy care, and advanced diagnostics require referral</li>
                    </ul>
                </li>
                <li class="mt-2">
                    <strong>Diagnostic & Support Infrastructure</strong>
                    <ul>
                        <li>Basic consultation and treatment areas</li>
                        <li>Emergency equipment appropriate to facility type</li>
                        <li>Medicine dispensing or pharmacy-related services, where authorized</li>
                        <li>Birthing room and newborn care equipment for birthing homes</li>
                        <li>Dental equipment for dental clinics</li>
                        <li>Referral system to hospitals and diagnostic facilities</li>
                    </ul>
                </li>
            </ul>
        </p>
        <p class="p-modal text-justify">
            <b>Note:</b> This classification and service scope is aligned with the Philippine DOH hospital licensing and classification framework pursuant to the Hospital Licensure Act. <a href="{{ asset('files/ao-2012-0012.pdf') }}" target="_blank">Philippines Hospital and other medical facilities Classification (AO 2012-0012)</a>
        </p>

        <h5 class="modal-header fw-bold" style="padding:10px 0;">
            Philippines Government Health Insurance
        </h5>
            <p class="text-justify">
                Government public health insurance system in the Philippines is administered by the Philippine Health Insurance Corporation, commonly known as PhilHealth. PhilHealth manages the National Health Insurance Program, the country’s national social health insurance scheme.
            </p>
            <p class="text-justify">
                PhilHealth is designed to reduce the financial burden of medical care by paying part of eligible healthcare costs through accredited public and private health facilities. It does not directly operate hospitals or clinics. Instead, it finances approved benefit packages for qualified members and their dependents.
            </p>
            <p class="text-justify">
                Under the Universal Health Care framework, all Philippine citizens are covered under the National Health Insurance Program. Coverage is organized through two broad membership groups: Direct Contributors and Indirect Contributors.
            </p>
            <h6 class="fw-bold">
                <b>Key Facts</b>
            </h6>
            <p class="text-justify">
                <ul>
                    <li>Official program name: National Health Insurance Program</li>
                    <li>Implementing agency: Philippine Health Insurance Corporation</li>
                    <li>Common name: PhilHealth</li>
                    <li>Coverage model: National social health insurance</li>
                    <li>Provider network: Accredited public and private health facilities</li>
                    <li>Main payment method: Case rates and approved benefit packages</li>
                    <li>Main member categories: Direct Contributors and Indirect Contributors</li>
                    <li>Direct Contributors: Members who pay premiums through salary deduction, employer remittance, or direct payment</li>
                    <li>Indirect Contributors: Members whose premiums are subsidized by the government</li>
                    <li>Employee payment model: Contributions are generally deducted from salary and remitted through the employer</li>
                    <li>Self-paying payment model: Contributions may be paid directly through authorized PhilHealth payment channels</li>
                    <li>Free registration: Registration itself is free</li>
                    <li>Free coverage: Available only for qualified government-subsidized groups</li>
                    <li>Foreign eligibility: Eligible foreign residents, workers, and retirees may enroll if they meet PhilHealth requirements</li>
                    <li>Tourist coverage: Foreign tourists are generally not automatically covered</li>
                    <li>Patient billing: PhilHealth reduces eligible bills but may not cover the full cost of treatment</li>
                </ul>
            </p>
            <h6 class="fw-bold">
                <b>Legal and Institutional Background</b>
            </h6>
            <p class="text-justify">
                PhilHealth operates under the National Health Insurance Program established by the National Health Insurance Act. The program was created to provide health insurance coverage and improve access to affordable healthcare services in the Philippines.
            </p>
            <p class="text-justify">
                The Universal Health Care Act further expanded the national health insurance framework by placing all Philippine citizens under PhilHealth coverage. The law reorganized membership into Direct Contributors and Indirect Contributors.
            </p>
            <p class="text-justify">
                Direct Contributors are members with the capacity to pay premiums. This group includes employed individuals, self-employed workers, professionals, migrant workers, overseas Filipino workers, household workers, and other paying members.
            </p>
            <p class="text-justify">
                Indirect Contributors are members whose premiums are paid through government subsidy. This group includes qualified indigent members, senior citizens, persons with disabilities, sponsored members, and other government-identified subsidized groups.
            </p>
            <p class="text-justify">
                PhilHealth is a government-owned and controlled corporation attached to the national health system. Its main responsibilities include member registration, premium collection, provider accreditation, benefit administration, claims processing, and payment to accredited healthcare facilities.
            </p>
            <h6 class="fw-bold">
                <b>Coverage and Benefits</b>
            </h6>
            <p class="text-justify">
                PhilHealth coverage applies only when the patient receives care from a PhilHealth-accredited or contracted healthcare provider and meets the applicable eligibility requirements.
            </p>
            <p class="text-justify">
            Coverage may include the following benefit areas:
            </p>
            <p class="text-justify">
                <ul>
                    <li>Inpatient hospital care</li>
                    <li>Selected outpatient care</li>
                    <li>Primary care services</li>
                    <li>Emergency care covered under approved packages</li>
                    <li>Maternity care</li>
                    <li>Newborn care</li>
                    <li>Dialysis</li>
                    <li>Ambulatory surgery</li>
                    <li>TB-DOTS services</li>
                    <li>Animal bite treatment packages</li>
                    <li>Mental health services under approved arrangements</li>
                    <li>Cancer and other specialty benefit packages</li>
                    <li>Selected high-cost or catastrophic care packages</li>
                </ul>
            </p>
            <p class="text-justify">
            PhilHealth commonly pays benefits through fixed case rates. The case-rate amount is deducted from the member’s total bill before discharge or final billing. The amount may cover hospital charges, professional fees, or other approved components depending on the applicable benefit package.
            </p>
            <p class="text-justify">
            PhilHealth does not always pay the full medical bill. Patients may still have remaining out-of-pocket expenses, depending on the diagnosis, procedure, hospital type, accommodation level, medicines, supplies, professional fees, and package limits.
            </p>
            <p class="text-justify">
                For qualified members in eligible settings, the No Balance Billing policy may remove additional charges beyond the PhilHealth benefit package. This usually applies to specific subsidized member groups and eligible services in government health facilities.
            </p>
            <h6 class="fw-bold">
                <b>Implementation and Challenges</b>
            </h6>
            <p class="text-justify">
                PhilHealth improves access to healthcare financing, but the system still faces operational and access-related challenges.
            </p>
            <p class="text-justify">
                <ul>
                    <li>PhilHealth benefit payments are often based on fixed case rates. When the actual cost of care is higher than the case-rate amount, patients may still pay the remaining balance.</li>
                    <li>Access depends on provider's accreditation. A member may only use PhilHealth benefits in a health facility that is accredited or contracted for the relevant service.</li>
                    <li>Service availability differs across regions. Large urban hospitals may offer more specialized PhilHealth-covered services, while remote or underserved areas may have fewer accredited providers and limited specialist capacity.</li>
                    <li>Members must maintain accurate membership records and meet documentation requirements. Delays or errors in eligibility verification may affect benefit processing.</li>
                    <li>Direct Contributors must remain compliant with premium payment rules. Employees depend on proper employer remittance, while self-paying members must pay through approved channels.</li>
                    <li>Foreign nationals must meet specific enrollment requirements. Legal residence, work status, retirement visa status, and immigration documents affect eligibility.</li>
                </ul>
            </p>
            <h6 class="fw-bold">
                <b>Current Role</b>
            </h6>
            <p class="text-justify">
                PhilHealth remains the central public health insurance mechanism in the Philippines. It is the main government instrument for reducing the cost of hospital care, primary care, maternity care, dialysis, selected outpatient services, and approved specialty packages.
            </p>
            <p class="text-justify">
                PhilHealth supports the wider Universal Health Care agenda by expanding financial protection, organizing members into contributor categories, accrediting healthcare providers, and paying approved benefits through public and private facilities.
            </p>
            <p class="text-justify">
                The system is not a fully free healthcare model. It combines government subsidy for qualified groups with premium-based contributions from workers, employers, self-employed individuals, professionals, voluntary members, and eligible foreign residents.
            </p>
            <p class="text-justify">
            For practical use, a member should confirm active PhilHealth membership, seek care from an accredited provider, present the required PhilHealth information, complete the facility's benefit process, and verify the amount deducted from the final bill.
            </p>
        </div>
        </div>
    </div>
    </div>

    <div class="modal fade" id="level44Modal" tabindex="-1" aria-labelledby="disclaimerLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:800px;">
        <div class="modal-content">
        <div class="modal-header">
            <div class="d-flex align-items-center">
                <img src="https://pg.concordreview.com/wp-content/uploads/2025/01/hospital_pin-purple.png" style="width:30px; height:30px;">
                <h5 class="modal-title" id="disclaimerLabel">Level 1 Medical Facilities (Public and Private)</h5>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <h6 class="fw-bold">
                <b>Overview</b>
            </h6>
            <p class="text-justify">
               Level 1 hospitals are basic general hospitals licensed under the Philippine Department of Health (DOH) hospital classification framework. They provide essential hospital services for common and uncomplicated medical, surgical, maternity, pediatric, and emergency cases. They are the first hospital-level referral point above primary care facilities.
            </p>
            <p class="text-justify">
                <b>Note:</b> The Philippine DOH medical facility classification applies to both public and private hospitals and health facilities. Classification is determined by licensed service capability, infrastructure, staffing, diagnostics, and authorized bed capacity, not by whether the facility is government-owned or privately owned.
            </p>
            <h6 class="fw-bold">
                <b>Role</b>
            </h6>
            <p class="text-justify">
                <ul>
                    <li>Provide first-line hospital care for common and uncomplicated cases</li>
                    <li>Manage basic medical, surgical, pediatric, obstetric, and emergency conditions</li>
                    <li>Serve as referral hospitals for primary care facilities, infirmaries, birthing homes, and outpatient clinics</li>
                    <li>Stabilize patients before referral to Level 2 or Level 3 hospitals when advanced care is required</li>
                    <li>Support basic inpatient care, infection control, patient records, and facility reporting</li>
                </ul>
            </p>
            <h6 class="fw-bold">
                <b>Clinical Services</b>
            </h6>
            <p class="text-justify">
                <ul>
                    <li>
                        <strong>Bed Capacity</strong>
                        <ul>
                            <li>Approximately 25-75 beds</li>
                            <li>Actual capacity depends on the hospital's DOH-authorized bed capacity under its License to Operate</li>
                        </ul>
                    </li>
                    <li class="mt-2">
                        <strong>Core Specialties</strong>
                        <ul>
                            <li>Internal Medicine / General Medicine</li>
                            <li>Pediatrics</li>
                            <li>Obstetrics & Gynecology</li>
                            <li>Surgery</li>
                            <li>Family Medicine / General Practice, where applicable</li>
                        </ul>
                    </li>
                    <li class="mt-2">
                        <strong>Intermediate Services</strong>
                        <ul>
                            <li>Emergency services</li>
                            <li>Outpatient services</li>
                            <li>Inpatient nursing care</li>
                            <li>Maternity facilities</li>
                            <li>Isolation facilities for infectious and communicable diseases</li>
                            <li>Dental section or dental clinic</li>
                            <li>Pharmacy services</li>
                            <li>Blood station provision or access</li>
                        </ul>
                    </li>
                    <li class="mt-2">
                        <strong>Surgical & Procedural Capacity</strong>
                        <ul>
                            <li>Operating room with standard equipment</li>
                            <li>Sterilization capacity for equipment and supplies</li>
                            <li>Post-operative recovery room</li>
                            <li>Basic surgical and maternity procedures within licensed capability</li>
                            <li>Referral of complex surgery, ICU-level care, high-risk pregnancy, and subspecialty cases to higher-level hospitals</li>
                        </ul>
                    </li>
                    <li class="mt-2">
                        <strong>Diagnostic & Support Infrastructure</strong>
                        <ul>
                            <li>DOH-licensed secondary clinical laboratory</li>
                            <li>Consulting pathologist support</li>
                            <li>DOH-licensed Level 1 imaging facility</li>
                            <li>X-ray services with consulting radiologist support</li>
                            <li>Pharmacy</li>
                            <li>Dental clinic</li>
                            <li>Blood station provision</li>
                            <li>Infection-control and isolation facilities</li>
                        </ul>
                    </li>
                </ul>
            </p>
            <p class="p-modal text-justify">
                <b>Note:</b> This classification and service scope is aligned with the Philippine DOH hospital licensing and classification framework pursuant to the Hospital Licensure Act. <a href="{{ asset('files/ao-2012-0012.pdf') }}" target="__blank">Philippines Hospital and other medical facilities Classification (AO 2012-0012)</a>
            </p>

            <h5 class="modal-header fw-bold" style="padding:10px 0;">
            Philippines Government Health Insurance
        </h5>
            <p class="text-justify">
                Government public health insurance system in the Philippines is administered by the Philippine Health Insurance Corporation, commonly known as PhilHealth. PhilHealth manages the National Health Insurance Program, the country’s national social health insurance scheme.
            </p>
            <p class="text-justify">
                PhilHealth is designed to reduce the financial burden of medical care by paying part of eligible healthcare costs through accredited public and private health facilities. It does not directly operate hospitals or clinics. Instead, it finances approved benefit packages for qualified members and their dependents.
            </p>
            <p class="text-justify">
                Under the Universal Health Care framework, all Philippine citizens are covered under the National Health Insurance Program. Coverage is organized through two broad membership groups: Direct Contributors and Indirect Contributors.
            </p>
            <h6 class="fw-bold">
                <b>Key Facts</b>
            </h6>
            <p class="text-justify">
                <ul>
                    <li>Official program name: National Health Insurance Program</li>
                    <li>Implementing agency: Philippine Health Insurance Corporation</li>
                    <li>Common name: PhilHealth</li>
                    <li>Coverage model: National social health insurance</li>
                    <li>Provider network: Accredited public and private health facilities</li>
                    <li>Main payment method: Case rates and approved benefit packages</li>
                    <li>Main member categories: Direct Contributors and Indirect Contributors</li>
                    <li>Direct Contributors: Members who pay premiums through salary deduction, employer remittance, or direct payment</li>
                    <li>Indirect Contributors: Members whose premiums are subsidized by the government</li>
                    <li>Employee payment model: Contributions are generally deducted from salary and remitted through the employer</li>
                    <li>Self-paying payment model: Contributions may be paid directly through authorized PhilHealth payment channels</li>
                    <li>Free registration: Registration itself is free</li>
                    <li>Free coverage: Available only for qualified government-subsidized groups</li>
                    <li>Foreign eligibility: Eligible foreign residents, workers, and retirees may enroll if they meet PhilHealth requirements</li>
                    <li>Tourist coverage: Foreign tourists are generally not automatically covered</li>
                    <li>Patient billing: PhilHealth reduces eligible bills but may not cover the full cost of treatment</li>
                </ul>
            </p>
            <h6 class="fw-bold">
                <b>Legal and Institutional Background</b>
            </h6>
            <p class="text-justify">
                PhilHealth operates under the National Health Insurance Program established by the National Health Insurance Act. The program was created to provide health insurance coverage and improve access to affordable healthcare services in the Philippines.
            </p>
            <p class="text-justify">
                The Universal Health Care Act further expanded the national health insurance framework by placing all Philippine citizens under PhilHealth coverage. The law reorganized membership into Direct Contributors and Indirect Contributors.
            </p>
            <p class="text-justify">
                Direct Contributors are members with the capacity to pay premiums. This group includes employed individuals, self-employed workers, professionals, migrant workers, overseas Filipino workers, household workers, and other paying members.
            </p>
            <p class="text-justify">
                Indirect Contributors are members whose premiums are paid through government subsidy. This group includes qualified indigent members, senior citizens, persons with disabilities, sponsored members, and other government-identified subsidized groups.
            </p>
            <p class="text-justify">
                PhilHealth is a government-owned and controlled corporation attached to the national health system. Its main responsibilities include member registration, premium collection, provider accreditation, benefit administration, claims processing, and payment to accredited healthcare facilities.
            </p>
            <h6 class="fw-bold">
                <b>Coverage and Benefits</b>
            </h6>
            <p class="text-justify">
                PhilHealth coverage applies only when the patient receives care from a PhilHealth-accredited or contracted healthcare provider and meets the applicable eligibility requirements.
            </p>
            <p class="text-justify">
            Coverage may include the following benefit areas:
            </p>
            <p class="text-justify">
                <ul>
                    <li>Inpatient hospital care</li>
                    <li>Selected outpatient care</li>
                    <li>Primary care services</li>
                    <li>Emergency care covered under approved packages</li>
                    <li>Maternity care</li>
                    <li>Newborn care</li>
                    <li>Dialysis</li>
                    <li>Ambulatory surgery</li>
                    <li>TB-DOTS services</li>
                    <li>Animal bite treatment packages</li>
                    <li>Mental health services under approved arrangements</li>
                    <li>Cancer and other specialty benefit packages</li>
                    <li>Selected high-cost or catastrophic care packages</li>
                </ul>
            </p>
            <p class="text-justify">
            PhilHealth commonly pays benefits through fixed case rates. The case-rate amount is deducted from the member’s total bill before discharge or final billing. The amount may cover hospital charges, professional fees, or other approved components depending on the applicable benefit package.
            </p>
            <p class="text-justify">
            PhilHealth does not always pay the full medical bill. Patients may still have remaining out-of-pocket expenses, depending on the diagnosis, procedure, hospital type, accommodation level, medicines, supplies, professional fees, and package limits.
            </p>
            <p class="text-justify">
                For qualified members in eligible settings, the No Balance Billing policy may remove additional charges beyond the PhilHealth benefit package. This usually applies to specific subsidized member groups and eligible services in government health facilities.
            </p>
            <h6 class="fw-bold">
                <b>Implementation and Challenges</b>
            </h6>
            <p class="text-justify">
                PhilHealth improves access to healthcare financing, but the system still faces operational and access-related challenges.
            </p>
            <p class="text-justify">
                <ul>
                    <li>PhilHealth benefit payments are often based on fixed case rates. When the actual cost of care is higher than the case-rate amount, patients may still pay the remaining balance.</li>
                    <li>Access depends on provider's accreditation. A member may only use PhilHealth benefits in a health facility that is accredited or contracted for the relevant service.</li>
                    <li>Service availability differs across regions. Large urban hospitals may offer more specialized PhilHealth-covered services, while remote or underserved areas may have fewer accredited providers and limited specialist capacity.</li>
                    <li>Members must maintain accurate membership records and meet documentation requirements. Delays or errors in eligibility verification may affect benefit processing.</li>
                    <li>Direct Contributors must remain compliant with premium payment rules. Employees depend on proper employer remittance, while self-paying members must pay through approved channels.</li>
                    <li>Foreign nationals must meet specific enrollment requirements. Legal residence, work status, retirement visa status, and immigration documents affect eligibility.</li>
                </ul>
            </p>
            <h6 class="fw-bold">
                <b>Current Role</b>
            </h6>
            <p class="text-justify">
                PhilHealth remains the central public health insurance mechanism in the Philippines. It is the main government instrument for reducing the cost of hospital care, primary care, maternity care, dialysis, selected outpatient services, and approved specialty packages.
            </p>
            <p class="text-justify">
                PhilHealth supports the wider Universal Health Care agenda by expanding financial protection, organizing members into contributor categories, accrediting healthcare providers, and paying approved benefits through public and private facilities.
            </p>
            <p class="text-justify">
                The system is not a fully free healthcare model. It combines government subsidy for qualified groups with premium-based contributions from workers, employers, self-employed individuals, professionals, voluntary members, and eligible foreign residents.
            </p>
            <p class="text-justify">
            For practical use, a member should confirm active PhilHealth membership, seek care from an accredited provider, present the required PhilHealth information, complete the facility's benefit process, and verify the amount deducted from the final bill.
            </p>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="level55Modal" tabindex="-1" aria-labelledby="disclaimerLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width:800px;">
    <div class="modal-content">
      <div class="modal-header">
        <div class="d-flex align-items-center">
            <img src="https://pg.concordreview.com/wp-content/uploads/2025/01/hospital_pin-blue.png" style="width:30px; height:30px;">
            <h5 class="modal-title" id="disclaimerLabel">Level 2 Medical Facilities (Public and Private)</h5>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <h6 class="fw-bold">
            <b>Overview</b>
        </h6>
        <p class="text-justify">
            Level 2 hospitals provide all Level 1 services plus departmentalized clinical services, broader specialist capacity, intensive care, neonatal intensive care, high-risk pregnancy care, respiratory therapy, and more advanced diagnostic services. They function as secondary referral hospitals for moderately complex cases.
        </p>
        <p class="text-justify">
            <b>Note:</b> The Philippine DOH medical facility classification applies to both public and private hospitals and health facilities. Classification is determined by licensed service capability, infrastructure, staffing, diagnostics, and authorized bed capacity, not by whether the facility is government-owned or privately owned.
        </p>
        <h6 class="fw-bold">
            <b>Role</b>
        </h6>
        <p class="text-justify">
            <ul>
                <li>Secondary referral hospitals for Level 1 hospitals and primary care facilities</li>
                <li>Manage moderate to complex medical, surgical, pediatric, obstetric, and emergency cases</li>
                <li>Provide departmentalized clinical services led by qualified clinical department heads</li>
                <li>Provide ICU, NICU, high-risk pregnancy, and respiratory therapy services</li>
                <li>Stabilize and refer highly complex or tertiary-level cases to Level 3 hospitals</li>
            </ul>
        </p>
        <h6 class="fw-bold">
            <b>Clinical Services</b>
        </h6>
        <p class="text-justify">
            <ul>
                <li>
                    <strong>Bed Capacity</strong>
                    <ul>
                        <li>Approximately 100-200 beds</li>
                        <li>Actual capacity depends on the hospital's DOH-authorized bed capacity under its License to Operate</li>
                    </ul>
                </li>
                <li class="mt-2">
                    <strong>Core Specialties</strong>
                    <ul>
                        <li>Internal Medicine</li>
                        <li>Pediatrics</li>
                        <li>Obstetrics & Gynecology</li>
                        <li>Surgery</li>
                        <li>Related specialties and ancillary services</li>
                        <li>Departmentalized clinical services</li>
                    </ul>
                </li>
                <li class="mt-2">
                    <strong>Intermediate Services</strong>
                    <ul>
                        <li>All Level 1 services</li>
                        <li>General Intensive Care Unit</li>
                        <li>Neonatal Intensive Care Unit</li>
                        <li>High-Risk Pregnancy Unit</li>
                        <li>Respiratory Therapy Unit</li>
                        <li>Expanded emergency care</li>
                        <li>Expanded inpatient and outpatient services</li>
                    </ul>
                </li>
                <li class="mt-2">
                    <strong>Surgical & Procedural Capacity</strong>
                    <ul>
                        <li>Broader surgical services than Level 1 hospitals</li>
                        <li>Operating room and post-operative recovery capacity</li>
                        <li>Surgical care supported by ICU, NICU, HRPU, respiratory therapy, and tertiary laboratory services</li>
                        <li>Referral of highly specialized, interventional, or tertiary surgical cases to Level 3 hospitals</li>
                    </ul>
                </li>
                <li class="mt-2">
                    <strong>Diagnostic & Support Infrastructure</strong>
                    <ul>
                        <li>DOH-licensed tertiary clinical laboratory</li>
                        <li>DOH-licensed Level 2 imaging facility</li>
                        <li>Mobile X-ray capability inside the institution</li>
                        <li>Capability for contrast examinations</li>
                        <li>Pharmacy</li>
                        <li>Blood station provision</li>
                        <li>Respiratory therapy support</li>
                        <li>ICU, NICU, and HRPU infrastructure</li>
                    </ul>
                </li>
            </ul>
        </p>
        <p class="p-modal text-justify">
            <b>Note:</b> This classification and service scope is aligned with the Philippine DOH hospital licensing and classification framework pursuant to the Hospital Licensure Act. <a href="{{ asset('files/ao-2012-0012.pdf') }}" target="__blank">Philippines Hospital and other medical facilities Classification (AO 2012-0012)</a>
        </p>

        <h5 class="modal-header fw-bold" style="padding:10px 0;">
            Philippines Government Health Insurance
        </h5>
        <p class="text-justify">
            Government public health insurance system in the Philippines is administered by the Philippine Health Insurance Corporation, commonly known as PhilHealth. PhilHealth manages the National Health Insurance Program, the country’s national social health insurance scheme.
        </p>
        <p class="text-justify">
            PhilHealth is designed to reduce the financial burden of medical care by paying part of eligible healthcare costs through accredited public and private health facilities. It does not directly operate hospitals or clinics. Instead, it finances approved benefit packages for qualified members and their dependents.
        </p>
        <p class="text-justify">
            Under the Universal Health Care framework, all Philippine citizens are covered under the National Health Insurance Program. Coverage is organized through two broad membership groups: Direct Contributors and Indirect Contributors.
        </p>
        <h6 class="fw-bold">
            <b>Key Facts</b>
        </h6>
        <p class="text-justify">
            <ul>
                <li>Official program name: National Health Insurance Program</li>
                <li>Implementing agency: Philippine Health Insurance Corporation</li>
                <li>Common name: PhilHealth</li>
                <li>Coverage model: National social health insurance</li>
                <li>Provider network: Accredited public and private health facilities</li>
                <li>Main payment method: Case rates and approved benefit packages</li>
                <li>Main member categories: Direct Contributors and Indirect Contributors</li>
                <li>Direct Contributors: Members who pay premiums through salary deduction, employer remittance, or direct payment</li>
                <li>Indirect Contributors: Members whose premiums are subsidized by the government</li>
                <li>Employee payment model: Contributions are generally deducted from salary and remitted through the employer</li>
                <li>Self-paying payment model: Contributions may be paid directly through authorized PhilHealth payment channels</li>
                <li>Free registration: Registration itself is free</li>
                <li>Free coverage: Available only for qualified government-subsidized groups</li>
                <li>Foreign eligibility: Eligible foreign residents, workers, and retirees may enroll if they meet PhilHealth requirements</li>
                <li>Tourist coverage: Foreign tourists are generally not automatically covered</li>
                <li>Patient billing: PhilHealth reduces eligible bills but may not cover the full cost of treatment</li>
            </ul>
        </p>
        <h6 class="fw-bold">
            <b>Legal and Institutional Background</b>
        </h6>
        <p class="text-justify">
            PhilHealth operates under the National Health Insurance Program established by the National Health Insurance Act. The program was created to provide health insurance coverage and improve access to affordable healthcare services in the Philippines.
        </p>
        <p class="text-justify">
            The Universal Health Care Act further expanded the national health insurance framework by placing all Philippine citizens under PhilHealth coverage. The law reorganized membership into Direct Contributors and Indirect Contributors.
        </p>
        <p class="text-justify">
            Direct Contributors are members with the capacity to pay premiums. This group includes employed individuals, self-employed workers, professionals, migrant workers, overseas Filipino workers, household workers, and other paying members.
        </p>
        <p class="text-justify">
            Indirect Contributors are members whose premiums are paid through government subsidy. This group includes qualified indigent members, senior citizens, persons with disabilities, sponsored members, and other government-identified subsidized groups.
        </p>
        <p class="text-justify">
            PhilHealth is a government-owned and controlled corporation attached to the national health system. Its main responsibilities include member registration, premium collection, provider accreditation, benefit administration, claims processing, and payment to accredited healthcare facilities.
        </p>
        <h6 class="fw-bold">
            <b>Coverage and Benefits</b>
        </h6>
        <p class="text-justify">
            PhilHealth coverage applies only when the patient receives care from a PhilHealth-accredited or contracted healthcare provider and meets the applicable eligibility requirements.
        </p>
        <p class="text-justify">
           Coverage may include the following benefit areas:
        </p>
        <p class="text-justify">
            <ul>
                <li>Inpatient hospital care</li>
                <li>Selected outpatient care</li>
                <li>Primary care services</li>
                <li>Emergency care covered under approved packages</li>
                <li>Maternity care</li>
                <li>Newborn care</li>
                <li>Dialysis</li>
                <li>Ambulatory surgery</li>
                <li>TB-DOTS services</li>
                <li>Animal bite treatment packages</li>
                <li>Mental health services under approved arrangements</li>
                <li>Cancer and other specialty benefit packages</li>
                <li>Selected high-cost or catastrophic care packages</li>
            </ul>
        </p>
        <p class="text-justify">
           PhilHealth commonly pays benefits through fixed case rates. The case-rate amount is deducted from the member’s total bill before discharge or final billing. The amount may cover hospital charges, professional fees, or other approved components depending on the applicable benefit package.
        </p>
        <p class="text-justify">
           PhilHealth does not always pay the full medical bill. Patients may still have remaining out-of-pocket expenses, depending on the diagnosis, procedure, hospital type, accommodation level, medicines, supplies, professional fees, and package limits.
        </p>
        <p class="text-justify">
            For qualified members in eligible settings, the No Balance Billing policy may remove additional charges beyond the PhilHealth benefit package. This usually applies to specific subsidized member groups and eligible services in government health facilities.
        </p>
        <h6 class="fw-bold">
            <b>Implementation and Challenges</b>
        </h6>
        <p class="text-justify">
            PhilHealth improves access to healthcare financing, but the system still faces operational and access-related challenges.
        </p>
        <p class="text-justify">
            <ul>
                <li>PhilHealth benefit payments are often based on fixed case rates. When the actual cost of care is higher than the case-rate amount, patients may still pay the remaining balance.</li>
                <li>Access depends on provider's accreditation. A member may only use PhilHealth benefits in a health facility that is accredited or contracted for the relevant service.</li>
                <li>Service availability differs across regions. Large urban hospitals may offer more specialized PhilHealth-covered services, while remote or underserved areas may have fewer accredited providers and limited specialist capacity.</li>
                <li>Members must maintain accurate membership records and meet documentation requirements. Delays or errors in eligibility verification may affect benefit processing.</li>
                <li>Direct Contributors must remain compliant with premium payment rules. Employees depend on proper employer remittance, while self-paying members must pay through approved channels.</li>
                <li>Foreign nationals must meet specific enrollment requirements. Legal residence, work status, retirement visa status, and immigration documents affect eligibility.</li>
            </ul>
        </p>
        <h6 class="fw-bold">
            <b>Current Role</b>
        </h6>
        <p class="text-justify">
            PhilHealth remains the central public health insurance mechanism in the Philippines. It is the main government instrument for reducing the cost of hospital care, primary care, maternity care, dialysis, selected outpatient services, and approved specialty packages.
        </p>
        <p class="text-justify">
            PhilHealth supports the wider Universal Health Care agenda by expanding financial protection, organizing members into contributor categories, accrediting healthcare providers, and paying approved benefits through public and private facilities.
        </p>
        <p class="text-justify">
            The system is not a fully free healthcare model. It combines government subsidy for qualified groups with premium-based contributions from workers, employers, self-employed individuals, professionals, voluntary members, and eligible foreign residents.
        </p>
        <p class="text-justify">
           For practical use, a member should confirm active PhilHealth membership, seek care from an accredited provider, present the required PhilHealth information, complete the facility's benefit process, and verify the amount deducted from the final bill.
        </p>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="level66Modal" tabindex="-1" aria-labelledby="disclaimerLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width:800px;">
    <div class="modal-content">
      <div class="modal-header">
        <div class="d-flex align-items-center">
            <img src="https://pg.concordreview.com/wp-content/uploads/2025/01/hospital-pin-red.png" style="width:30px; height:30px;">
            <h5 class="modal-title" id="disclaimerLabel">Level 3 Medical Facilities (Public and Private)</h5>
        </div>
         <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <h6 class="fw-bold">
            <b>Overview</b>
        </h6>
        <p class="text-justify">
            Level 3 hospitals are the highest tier medical facility in the Philippines registered under the Department of Health (DOH) licensing standards. Level 3 facilities provide comprehensive specialist and subspecialist services supported by advanced medical technology, tertiary diagnostic capabilities, and full referral-level clinical functions. These hospitals manage complex and severe medical cases and operate with a bed capacity exceeding 500 beds, consistent with requirements for tertiary hospital classification.
        </p>
        <p class="text-justify">
            <b>Note:</b> : The Philippine DOH medical facility classification applies to both public and private hospitals and health facilities. Classification is determined by licensed service capability, infrastructure, staffing, diagnostics, and authorized bed capacity, not by whether the facility is government or privately owned.
        </p>
        <h6 class="fw-bold">
            <b>Role</b>
        </h6>
        <p class="text-justify">
            <ul>
                <li>Tertiary referral hospitals for complex and severe cases</li>
                <li>Manage advanced medical, surgical, obstetric, pediatric, emergency, and subspecialty cases</li>
                <li>Provide training and teaching functions through accredited residency programs</li>
                <li>Support advanced diagnostics, critical care, rehabilitation, dialysis, blood banking, and interventional services</li>
                <li>Receive referrals from Level 1 and Level 2 hospitals, specialty facilities, primary care facilities, and other health providers</li>
            </ul>
        </p>
        <h6 class="fw-bold">
            <b>Clinical Services</b>
        </h6>
        <p class="text-justify">
            <ul>
                <li>
                    <strong>Bed Capacity</strong>
                    <ul>
                        <li>Approximately 200-500+ beds</li>
                        <li>Standard staffing references commonly use 200, 300, 400, and 500 beds</li>
                        <li>Larger Level 3 government hospitals may exceed 500 beds depending on authorized capacity and separate approval or upgrading laws</li>
                    </ul>
                </li>
                <li class="mt-2">
                    <strong>Core Specialties</strong>
                    <ul>
                        <li>Internal Medicine / General Medicine</li>
                        <li>Pediatrics</li>
                        <li>Obstetrics & Gynecology</li>
                        <li>Surgery</li>
                        <li>Family Medicine / General Practice, where applicable</li>
                    </ul>
                </li>
                <li class="mt-2">
                    <strong>Intermediate Services</strong>
                    <ul>
                        <li>Emergency services</li>
                        <li>Outpatient services</li>
                        <li>Inpatient nursing services</li>
                        <li>Maternity services, including delivery room and maternity ward facilities</li>
                        <li>Isolation facilities for infectious and communicable disease control</li>
                        <li>Dental section or dental clinic</li>
                        <li>Pharmacy services</li>
                        <li>Blood station provision or access</li>
                    </ul>
                </li>
                <li class="mt-2">
                    <strong>Surgical & Procedural Capacity</strong>
                    <ul>
                        <li>Operating room with standard equipment</li>
                        <li>Sterilization capacity for equipment and supplies</li>
                        <li>Post-operative recovery room</li>
                        <li>Basic surgical and maternity procedures within the hospital's licensed capability</li>
                        <li>Referral of complex surgical, critical care, high-risk obstetric, and subspecialty cases to higher-level hospitals</li>
                    </ul>
                </li>
                <li class="mt-2">
                    <strong>Diagnostic & Support Infrastructure</strong>
                    <ul>
                        <li>DOH-licensed secondary clinical laboratory</li>
                        <li>Consulting pathologist support</li>
                        <li>DOH-licensed Level 1 imaging facility</li>
                        <li>X-ray services with consulting radiologist support</li>
                        <li>Pharmacy</li>
                        <li>Dental clinic</li>
                        <li>Blood station provision</li>
                        <li>Infection control and isolation facilities</li>
                    </ul>
                </li>
            </ul>
        </p>
        <p class="p-modal text-justify">
            <b>Note:</b> : This classification and service scope is aligned with the Philippine DOH hospital licensing and classification framework pursuant to the Hospital Licensure Act. <a href="{{ asset('files/ao-2012-0012.pdf') }}" target="__blank">Philippines Hospital and other medical facilities Classification (AO 2012-0012)</a>
        </p>

        <h5 class="modal-header fw-bold" style="padding:10px 0;">
            Philippines Government Health Insurance
        </h5>
        <p class="text-justify">
            Government public health insurance system in the Philippines is administered by the Philippine Health Insurance Corporation, commonly known as PhilHealth. PhilHealth manages the National Health Insurance Program, the country’s national social health insurance scheme.
        </p>
        <p class="text-justify">
            PhilHealth is designed to reduce the financial burden of medical care by paying part of eligible healthcare costs through accredited public and private health facilities. It does not directly operate hospitals or clinics. Instead, it finances approved benefit packages for qualified members and their dependents.
        </p>
        <p class="text-justify">
            Under the Universal Health Care framework, all Philippine citizens are covered under the National Health Insurance Program. Coverage is organized through two broad membership groups: Direct Contributors and Indirect Contributors.
        </p>
        <h6 class="fw-bold">
            <b>Key Facts</b>
        </h6>
        <p class="text-justify">
            <ul>
                <li>Official program name: National Health Insurance Program</li>
                <li>Implementing agency: Philippine Health Insurance Corporation</li>
                <li>Common name: PhilHealth</li>
                <li>Coverage model: National social health insurance</li>
                <li>Provider network: Accredited public and private health facilities</li>
                <li>Main payment method: Case rates and approved benefit packages</li>
                <li>Main member categories: Direct Contributors and Indirect Contributors</li>
                <li>Direct Contributors: Members who pay premiums through salary deduction, employer remittance, or direct payment</li>
                <li>Indirect Contributors: Members whose premiums are subsidized by the government</li>
                <li>Employee payment model: Contributions are generally deducted from salary and remitted through the employer</li>
                <li>Self-paying payment model: Contributions may be paid directly through authorized PhilHealth payment channels</li>
                <li>Free registration: Registration itself is free</li>
                <li>Free coverage: Available only for qualified government-subsidized groups</li>
                <li>Foreign eligibility: Eligible foreign residents, workers, and retirees may enroll if they meet PhilHealth requirements</li>
                <li>Tourist coverage: Foreign tourists are generally not automatically covered</li>
                <li>Patient billing: PhilHealth reduces eligible bills but may not cover the full cost of treatment</li>
            </ul>
        </p>
        <h6 class="fw-bold">
            <b>Legal and Institutional Background</b>
        </h6>
        <p class="text-justify">
            PhilHealth operates under the National Health Insurance Program established by the National Health Insurance Act. The program was created to provide health insurance coverage and improve access to affordable healthcare services in the Philippines.
        </p>
        <p class="text-justify">
            The Universal Health Care Act further expanded the national health insurance framework by placing all Philippine citizens under PhilHealth coverage. The law reorganized membership into Direct Contributors and Indirect Contributors.
        </p>
        <p class="text-justify">
            Direct Contributors are members with the capacity to pay premiums. This group includes employed individuals, self-employed workers, professionals, migrant workers, overseas Filipino workers, household workers, and other paying members.
        </p>
        <p class="text-justify">
            Indirect Contributors are members whose premiums are paid through government subsidy. This group includes qualified indigent members, senior citizens, persons with disabilities, sponsored members, and other government-identified subsidized groups.
        </p>
        <p class="text-justify">
            PhilHealth is a government-owned and controlled corporation attached to the national health system. Its main responsibilities include member registration, premium collection, provider accreditation, benefit administration, claims processing, and payment to accredited healthcare facilities.
        </p>
        <h6 class="fw-bold">
            <b>Coverage and Benefits</b>
        </h6>
        <p class="text-justify">
            PhilHealth coverage applies only when the patient receives care from a PhilHealth-accredited or contracted healthcare provider and meets the applicable eligibility requirements.
        </p>
        <p class="text-justify">
           Coverage may include the following benefit areas:
        </p>
        <p class="text-justify">
            <ul>
                <li>Inpatient hospital care</li>
                <li>Selected outpatient care</li>
                <li>Primary care services</li>
                <li>Emergency care covered under approved packages</li>
                <li>Maternity care</li>
                <li>Newborn care</li>
                <li>Dialysis</li>
                <li>Ambulatory surgery</li>
                <li>TB-DOTS services</li>
                <li>Animal bite treatment packages</li>
                <li>Mental health services under approved arrangements</li>
                <li>Cancer and other specialty benefit packages</li>
                <li>Selected high-cost or catastrophic care packages</li>
            </ul>
        </p>
        <p class="text-justify">
           PhilHealth commonly pays benefits through fixed case rates. The case-rate amount is deducted from the member’s total bill before discharge or final billing. The amount may cover hospital charges, professional fees, or other approved components depending on the applicable benefit package.
        </p>
        <p class="text-justify">
           PhilHealth does not always pay the full medical bill. Patients may still have remaining out-of-pocket expenses, depending on the diagnosis, procedure, hospital type, accommodation level, medicines, supplies, professional fees, and package limits.
        </p>
        <p class="text-justify">
            For qualified members in eligible settings, the No Balance Billing policy may remove additional charges beyond the PhilHealth benefit package. This usually applies to specific subsidized member groups and eligible services in government health facilities.
        </p>
        <h6 class="fw-bold">
            <b>Implementation and Challenges</b>
        </h6>
        <p class="text-justify">
            PhilHealth improves access to healthcare financing, but the system still faces operational and access-related challenges.
        </p>
        <p class="text-justify">
            <ul>
                <li>PhilHealth benefit payments are often based on fixed case rates. When the actual cost of care is higher than the case-rate amount, patients may still pay the remaining balance.</li>
                <li>Access depends on provider's accreditation. A member may only use PhilHealth benefits in a health facility that is accredited or contracted for the relevant service.</li>
                <li>Service availability differs across regions. Large urban hospitals may offer more specialized PhilHealth-covered services, while remote or underserved areas may have fewer accredited providers and limited specialist capacity.</li>
                <li>Members must maintain accurate membership records and meet documentation requirements. Delays or errors in eligibility verification may affect benefit processing.</li>
                <li>Direct Contributors must remain compliant with premium payment rules. Employees depend on proper employer remittance, while self-paying members must pay through approved channels.</li>
                <li>Foreign nationals must meet specific enrollment requirements. Legal residence, work status, retirement visa status, and immigration documents affect eligibility.</li>
            </ul>
        </p>
        <h6 class="fw-bold">
            <b>Current Role</b>
        </h6>
        <p class="text-justify">
            PhilHealth remains the central public health insurance mechanism in the Philippines. It is the main government instrument for reducing the cost of hospital care, primary care, maternity care, dialysis, selected outpatient services, and approved specialty packages.
        </p>
        <p class="text-justify">
            PhilHealth supports the wider Universal Health Care agenda by expanding financial protection, organizing members into contributor categories, accrediting healthcare providers, and paying approved benefits through public and private facilities.
        </p>
        <p class="text-justify">
            The system is not a fully free healthcare model. It combines government subsidy for qualified groups with premium-based contributions from workers, employers, self-employed individuals, professionals, voluntary members, and eligible foreign residents.
        </p>
        <p class="text-justify">
           For practical use, a member should confirm active PhilHealth membership, seek care from an accredited provider, present the required PhilHealth information, complete the facility's benefit process, and verify the amount deducted from the final bill.
        </p>
      </div>
    </div>
  </div>
</div>

@endsection

@push('service')

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.fullscreen/1.6.0/Control.FullScreen.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const provinceSelect = document.querySelector('#provinceSelect .select-input');
    const provinceDropdown = document.querySelector('#provinceSelect .select-dropdown');
    const provinceSearch = document.getElementById('provinceSearch');
    const provinceSearchInput = document.getElementById('provinceSearchInput');

    // buka/tutup dropdown
    provinceSelect.addEventListener('click', () => {
        provinceDropdown.classList.toggle('show');
        const panel = document.querySelector('.leaflet-control-custom');

        if (provinceDropdown.classList.contains('show')) {

            const dropdownHeight =
                provinceDropdown.scrollHeight;

            panel.style.height =
                (420 + dropdownHeight) + 'px';

        } else {

            panel.style.height = '420px';
        }
    });

    // tutup saat klik luar
    document.addEventListener('click', (e) => {
        if (!document.getElementById('provinceSelect').contains(e.target)) {
            provinceDropdown.classList.remove('show');
        }
    });

    // filter pencarian
    provinceSearchInput.addEventListener('keyup', function () {
        const keyword = this.value.toLowerCase();

        document.querySelectorAll('#provinceList li').forEach(li => {
            const text = li.textContent.toLowerCase();

            li.style.display = text.includes(keyword)
                ? ''
                : 'none';
        });
    });

    // update text input ketika checkbox dipilih
    document.addEventListener('change', function(e) {

        if (e.target.classList.contains('province-checkbox')) {

            const selected = [...document.querySelectorAll('.province-checkbox:checked')]
                .map(cb => cb.parentElement.textContent.trim());

            if (selected.length === 0) {
                provinceSearch.value = '';
                provinceSearch.placeholder = '🔍 Select Region';
            } else if (selected.length <= 2) {
                provinceSearch.value = selected.join(', ');
            } else {
                provinceSearch.value = selected.length + ' Region Selected';
            }
        }
    });

});
</script>

<script>
    // --- Map Initialization ---
    const map = L.map('map').setView([12.50875613415851, 123.23756608747735], 6);

    const osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors', maxZoom: 19
    }).addTo(map);

    const satelliteLayer = L.tileLayer(
        'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
        { attribution: 'Tiles © Esri', maxZoom: 19 }
    );

    L.control.layers(
        { "Street Map": osmLayer, "Satellite Map": satelliteLayer },
        null,
        { position: 'topleft' } // posisi kiri atas
    ).addTo(map);

    L.control.fullscreen({
        position: 'topleft' // tetap di kiri atas
    }).addTo(map);

    const style = document.createElement('style');
    style.textContent = `
        .leaflet-top.leaflet-left .leaflet-control-layers {
            margin-top: 5px !important;
        }
        .leaflet-top.leaflet-left .leaflet-control-zoom {
            margin-top: 10px !important;
        }
        `;
    document.head.appendChild(style);

    // --- Global States ---
    let airportMarkers = L.featureGroup().addTo(map);
    let hospitalMarkers = L.featureGroup().addTo(map);
    let policeMarkers = L.featureGroup().addTo(map);
    let embassyMarkers = L.featureGroup().addTo(map);
    let radiusCircle = null;
    let radiusPinMarker = null;
    let lastClickedLocation = null;
    let drawnPolygonGeoJSON = null;
    let totalHospitals = 0;
    let totalAirports = 0;
    let totalPolice = 0;
    let totalEmbassies = 0;

    // --- Leaflet Draw ---
    const drawnItems = new L.FeatureGroup().addTo(map);
    const drawControl = new L.Control.Draw({
        draw: {
            polygon: { allowIntersection: false, shapeOptions: { color: '#0000FF', fillOpacity: 0.2 } },
            polyline: false, rectangle: false, circle: false, marker: false, circlemarker: false
        },
        edit: { featureGroup: drawnItems }
    });
    map.addControl(drawControl);

    map.on(L.Draw.Event.CREATED, async e => {
        drawnItems.clearLayers();
        drawnItems.addLayer(e.layer);

        drawnPolygonGeoJSON = e.layer.toGeoJSON();

        // console.log('Polygon Created', drawnPolygonGeoJSON);

        await refreshCurrentFilters();
    });
    map.on(L.Draw.Event.EDITED, async e => {

        e.layers.eachLayer(layer => {
            drawnPolygonGeoJSON = layer.toGeoJSON();
        });

        // console.log('Polygon Edited', drawnPolygonGeoJSON);

        await refreshCurrentFilters();
    });
    map.on(L.Draw.Event.DELETED, async () => {

        drawnItems.clearLayers();

        drawnPolygonGeoJSON = null;

        // console.log('Polygon Deleted');

        await refreshCurrentFilters();
    });

    // --- Update Radius ---
    function updateRadiusCircleAndPin(radius = 0) {
        if (radiusCircle) { map.removeLayer(radiusCircle); radiusCircle = null; }
        if (radiusPinMarker) { map.removeLayer(radiusPinMarker); radiusPinMarker = null; }

        if (radius > 0 && lastClickedLocation) {
            radiusCircle = L.circle(lastClickedLocation, {
                color: 'red', fillColor: '#f03', fillOpacity: 0.3, radius: radius * 1000
            }).addTo(map);
            const redIcon = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
            });
            radiusPinMarker = L.marker(lastClickedLocation, { icon: redIcon }).addTo(map);
        }
    }

    map.on('click', e => {
        lastClickedLocation = { lat: e.latlng.lat, lng: e.latlng.lng };
        const radius = parseInt(document.querySelector('#radiusRangeMap')?.value || 0);
        document.querySelector('#radiusValueMap').textContent = radius;
        updateRadiusCircleAndPin(radius);
    });

    // --- Fetch Data ---
    async function fetchData(url, filters = {}) {
        const params = new URLSearchParams();
        Object.entries(filters).forEach(([k, v]) => {
            if (Array.isArray(v)) v.forEach(x => params.append(`${k}[]`, x));
            else if (v !== '' && v != null) params.append(k, v);
        });
        if (drawnPolygonGeoJSON) params.append('polygon', JSON.stringify(drawnPolygonGeoJSON));
        //  console.log(url + '?' + params.toString());

        try {
            const res = await fetch(`${url}?${params.toString()}`);
            return res.ok ? await res.json() : [];
        } catch (e) {
            console.error(`Error fetching ${url}:`, e);
            return [];
        }
    }

    // --- Add Markers ---
    function addMarkers(data, group, defaultIconUrl) {
        group.clearLayers();
        data.forEach(item => {
            if (!item || !item.latitude || !item.longitude) return;

            let iconSize = [24, 24];
            let iconAnchor = [12, 24];

            // Police icon lebih kecil
            if (item.name_police) {
                iconSize = [12, 12];
                iconAnchor = [6, 6];
            }

            const icon = L.icon({
                iconUrl: item.icon || defaultIconUrl || 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
                iconSize: iconSize,
                iconAnchor: iconAnchor,
                popupAnchor: [0, -20]
            });

            const marker = L.marker([item.latitude, item.longitude], { icon }).addTo(group);

            let itemName = '', detailUrl = '', popupContent = '';

            if (item.airport_name) {
                itemName = item.airport_name;
                detailUrl = `/airports/${item.id}/detail`;
                popupContent = `
                    <h5 style="border-bottom:1px solid #cccccc;">${itemName}</h5>
                    <strong>Classification:</strong> ${item.category || 'N/A'}<br>
                    <strong>Address:</strong>
                        ${item.address || 'N/A'}
                        ${item.sub_city ? ', ' + item.sub_city : ''}
                        ${item.city ? ', ' + item.city : ''}
                        ${item.province_name ? ', ' + item.province_name : ''}, Philippines <br>
                    <strong>Website:</strong> ${item.website || 'N/A'} <br>
                `;
            } else if (item.name) {
                itemName = item.name;
                detailUrl = `/hospitals/${item.id}`;
                popupContent = `
                    <h5 style="border-bottom:1px solid #cccccc;">${itemName}</h5>
                    <strong>Global Classification:</strong> ${item.facility_category || 'N/A'}<br>
                    <strong>Country Classification:</strong> ${item.facility_level || 'N/A'}<br>
                    <strong>Address:</strong>
                        ${item.address || 'N/A'}
                        ${item.sub_city ? ', ' + item.sub_city : ''}
                        ${item.city ? ', ' + item.city : ''}
                        ${item.province_name ? ', ' + item.province_name : ''}, Philippines <br>
                `;
            } else if (item.name_police) {
                itemName = item.name_police;
                detailUrl = `/police/${item.id}/detail`;
                popupContent = `
                    <h5 style="border-bottom:1px solid #cccccc;">${itemName}</h5>
                    <strong>Category:</strong> ${item.category || 'N/A'}<br>
                    <strong>Address:</strong>
                        ${item.address || 'N/A'}
                        ${item.sub_city ? ', ' + item.sub_city : ''}
                        ${item.city ? ', ' + item.city : ''}
                        ${item.province_name ? ', ' + item.province_name : ''}, Philippines <br>
                    <strong>Phone:</strong> ${item.telephone || 'N/A'}<br>
                    <strong>Fax:</strong> ${item.fax || 'N/A'}<br>
                    <strong>Email:</strong> ${item.email || 'N/A'}<br>
                    <strong>Website:</strong> ${item.website || 'N/A'}<br>
                `;
            }
            else if (item.name_embassiees) {
                itemName = item.name_embassiees;
                detailUrl = `/embassiees/${item.id}/detail`;
                popupContent = `
                    <h5 style="border-bottom:1px solid #cccccc;">${itemName}</h5>
                    <strong>Address:</strong>
                        ${item.address || 'N/A'}
                        ${item.sub_city ? ', ' + item.sub_city : ''}
                        ${item.city ? ', ' + item.city : ''}
                        ${item.province_name ? ', ' + item.province_name : ''}, Philippines <br>
                    <strong>Phone:</strong> ${item.telephone || 'N/A'}<br>
                    <strong>Fax:</strong> ${item.fax || 'N/A'}<br>
                    <strong>Email:</strong> ${item.email || 'N/A'}<br>
                    <strong>Website:</strong> ${item.website || 'N/A'}<br>
                `;
            }

            if (item.id && detailUrl)
                popupContent += `<a href="${detailUrl}" class="btn btn-primary btn-sm mt-2" style="color:white;">Read More</a>`;

            marker.bindPopup(popupContent);
        });
    }

    // --- Apply Filters ---
    async function applyFiltersWithMapControl(
        facilities = [],
        hospitalLevels = [],
        airportClasses = [],
        provinces = [],
        radius = 0,
        airportName = '',
        hospitalName = ''
    ) {
        let common = { provinces };
        if (radius > 0 && lastClickedLocation) {
            common.radius = radius;
            common.center_lat = lastClickedLocation.lat;
            common.center_lng = lastClickedLocation.lng;
        }

        totalHospitals = 0;
        totalAirports = 0;
        totalPolice = 0;
        totalEmbassies = 0;

        // jika tidak ada checkbox dipilih => tampilkan semua
        const showAllFacilities = facilities.length === 0;

        const showHospital =
            showAllFacilities || facilities.includes('hospital');

        const showAirport =
            showAllFacilities || facilities.includes('airport');

        const showPolice =
            showAllFacilities || facilities.includes('police');

        const showEmbassy =
            showAllFacilities || facilities.includes('embassy');

         // === HOSPITALS ===
        if (showHospital) {
             const result = await fetchData('/api/hospital', {
                ...common,
                name: hospitalName,
                category: hospitalLevels
            });

            addMarkers(result.hospitals, hospitalMarkers, null);

            totalHospitals = result.hospitals.length;
        } else {
            hospitalMarkers.clearLayers();
        }

        // === AIRPORTS ===
       if (showAirport) {

            const airportResponse = await fetchData('/api/airports', {
                ...common,
                name: airportName
            });

            const airports = Array.isArray(airportResponse)
                    ? airportResponse
                    : airportResponse.airports || [];
            const categoryCounts = airportResponse.categoryCounts || {};

            const filteredAirports = airports.filter(a => {

                if (airportClasses.length === 0) {
                    return true;
                }

                if (!a.category) {
                    return false;
                }

                const dbCategories = a.category
                    .split(',')
                    .map(c => c.trim().toLowerCase());

                return airportClasses.some(sel =>
                    dbCategories.includes(sel.toLowerCase())
                );
            });

            addMarkers(
                filteredAirports,
                airportMarkers,
                'https://pg.concordreview.com/wp-content/uploads/2024/10/International-Airport.png'
            );

            totalAirports = filteredAirports.length;
        }else {
            airportMarkers.clearLayers();
        }

        // === POLICE ===
       if (showPolice) {

            const result = await fetchData('/api/polices', {
                ...common
            });

            const police = result.polices || [];
            const categoryCounts = result.categoryCounts || {};

            addMarkers(
                police,
                policeMarkers,
                null
            );

            totalPolice = police.length;

            Object.keys(categoryCounts).forEach(cat => {

                const id = cat.replace(/[^a-zA-Z0-9]/g, '-');

                const el = document.getElementById(`count-${id}`);

                if (el) {
                    el.textContent = categoryCounts[cat];
                }
            });
        } else {
            policeMarkers.clearLayers();
        }

        // === EMBASSY ===
        if (showEmbassy) {

            const embassies = await fetchData('/api/embassy', {
                ...common
            });

            addMarkers(
                embassies,
                embassyMarkers,
                '/images/embassy-icon-new.png'
            );

            totalEmbassies = embassies.length;

        } else {
            embassyMarkers.clearLayers();
        }

        updateRadiusCircleAndPin(radius);
        updateTotalCountDisplay();
    }

    function updateTotalCountDisplay() {
        document.getElementById('airportCount').textContent = totalAirports;
        document.getElementById('hospitalCount').textContent = totalHospitals;
        document.getElementById('policeCount').textContent = totalPolice;
        document.getElementById('embassyCount').textContent = totalEmbassies;

        const el = document.getElementById('totalCountDisplay');
    }

    // === COMBINED PANEL ===
    const CombinedPanel = L.Control.extend({
        options: { position: 'topright' },
        onAdd: function () {
            const div = L.DomUtil.create('div', 'leaflet-bar leaflet-control leaflet-control-custom');
            Object.assign(div.style, {
                background: 'white',
                borderRadius: '8px',
                boxShadow: '0 2px 6px rgba(0,0,0,0.2)',
                minWidth: '260px',
                maxHeight: '85vh',
                overflowY: 'auto'
            });

            div.innerHTML = `
                <button style="background:#007bff;color:white;border:none;width:100%;padding:8px;">Filter & Radius</button>
                <div id="filterPanel" style="
                    padding:10px;
                    display:flex;
                    flex-direction:column;
                    height:100%;
                ">
                    <strong>Radius: <span id="radiusValueMap">0</span> km</strong>
                    <input type="range" id="radiusRangeMap" min="0" max="500" value="0" style="width:100%;margin-bottom:6px;">
                    <div style="display:flex;gap:5px;">
                        <button id="applyRadiusMap" class="btn btn-sm btn-primary flex-fill">Apply</button>
                        <button id="resetRadiusMap" class="btn btn-sm btn-danger flex-fill">Reset</button>
                    </div>
                    <hr>
                    <strong>Facilities</strong>
                    <div class="form-check">
                        <input class="form-check-input facility-checkbox" type="checkbox" value="hospital" id="facilityHospital">
                        <label class="form-check-label" for="facilityHospital">
                            Medical (<span id="hospitalCount">0</span>)
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input facility-checkbox" type="checkbox" value="airport" id="facilityAirport">
                        <label class="form-check-label" for="facilityAirport">
                            Aviation (<span id="airportCount">0</span>)
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input facility-checkbox" type="checkbox" value="police" id="facilityPolice">
                        <label class="form-check-label" for="facilityPolice">
                            Police (<span id="policeCount">0</span>)
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input facility-checkbox" type="checkbox" value="embassy" id="facilityEmbassy">
                        <label class="form-check-label" for="facilityEmbassy">
                            Embassies (<span id="embassyCount">0</span>)
                        </label>
                    </div>

                    <hr>
                    <div class="filter-box" id="provinceSelect">
                        <label class="filter-label">
                            Region
                        </label>

                        <div class="select-input">
                            <input
                                type="text"
                                id="provinceSearch"
                                placeholder="🔍 Select Region"
                                readonly
                            >
                            <i class="bi bi-chevron-down"></i>
                        </div>

                        <div class="select-dropdown">
                            <input
                                type="text"
                                class="dropdown-search"
                                id="provinceSearchInput"
                                placeholder="Search Region..."
                            >

                            <ul id="provinceList">
                                @foreach($provinces as $province)
                                <li>
                                    <label>
                                        <input
                                            type="checkbox"
                                            class="province-checkbox"
                                            value="{{ $province->id }}"
                                        >
                                        {{ $province->provinces_region }}
                                    </label>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <hr>
                    <button id="resetMapFilter"
                            class="btn btn-sm btn-secondary w-100"
                            style="margin-top:auto;">
                        Reset All
                    </button>
                    <div id="totalCountDisplay" style="margin-top:8px;text-align:center;font-size:13px;"></div>
                </div>`;
            L.DomEvent.disableClickPropagation(div);
            return div;
        }
    });
    map.addControl(new CombinedPanel());

    // === INIT SELECT2 ===
    setTimeout(() => {
        if (typeof $ !== 'undefined' && $.fn.select2) {
            $('.select-search-airport').select2({ placeholder: 'Select Airport', width: '100%' });
            $('.select-search-hospital').select2({ placeholder: 'Select Hospital', width: '100%' });
        }
    }, 300);

    function getCurrentFiltersFromUI() {
        const facilities = [...document.querySelectorAll('.facility-checkbox:checked')].map(el => el.value);
        const hLevels = [...document.querySelectorAll('input[name="hospitalLevel"]:checked')].map(e => e.value);
        const aClasses = [...document.querySelectorAll('input[name="airportClass"]:checked')].map(e => e.value);
        const provs = [...document.querySelectorAll('.province-checkbox:checked')].map(e => e.value);
        const radius = parseInt(document.getElementById('radiusRangeMap')?.value || 0);
        // untuk select2, .value akan tetap bekerja because Select2 keeps value in the <select>
        const airportName = document.getElementById('airport_name_map')?.value || '';
        const hospitalName = document.getElementById('hospital_name_map')?.value || '';
        return { facilities, hLevels, aClasses, provs, radius, airportName, hospitalName };
    }

    async function refreshCurrentFilters() {
        const {
            facilities,
            hLevels,
            aClasses,
            provs,
            radius,
            airportName,
            hospitalName
        } = getCurrentFiltersFromUI();

        await applyFiltersWithMapControl(
            facilities,
            hLevels,
            aClasses,
            provs,
            radius,
            airportName,
            hospitalName
        );
    }

    // === Event Logic ===
    document.addEventListener('change', async e => {
        const facilities = [...document.querySelectorAll('.facility-checkbox:checked')].map(el => el.value);
        const hLevels = [...document.querySelectorAll('input[name="hospitalLevel"]:checked')].map(e => e.value);
        const aClasses = [...document.querySelectorAll('input[name="airportClass"]:checked')].map(e => e.value);
        const provs = [...document.querySelectorAll('.province-checkbox:checked')].map(e => e.value);
        const radius = parseInt(document.getElementById('radiusRangeMap').value || 0);
        const airportName = document.getElementById('airport_name_map')?.value || '';
        const hospitalName = document.getElementById('hospital_name_map')?.value || '';

        await applyFiltersWithMapControl(facilities, hLevels, aClasses, provs, radius, airportName, hospitalName);
    });

    // === INPUT: update tampilan radius saat slider digeser (live) ===
document.addEventListener('input', (e) => {
    if (e.target && e.target.id === 'radiusRangeMap') {
        const r = parseInt(e.target.value || 0);
        const el = document.getElementById('radiusValueMap');
        if (el) el.textContent = r;
        // hanya update tampilan lingkaran saja (belum apply ke filter)
        updateRadiusCircleAndPin(r);
    }
});

// === CLICK: apply / reset radius dan reset all ===
document.addEventListener('click', async (e) => {
    if (!e.target) return;

    // APPLY RADIUS => ambil filter sekarang lalu panggil applyFiltersWithMapControl dengan radius
    if (e.target.id === 'applyRadiusMap') {
        const {
            facilities,
            hLevels,
            aClasses,
            provs,
            radius,
            airportName,
            hospitalName
        } = getCurrentFiltersFromUI();
        // pastikan lastClickedLocation ada jika radius > 0
        if (radius > 0 && !lastClickedLocation) {
            alert('Tentukan titik di peta terlebih dahulu dengan klik peta untuk menggunakan filter radius.');
            return;
        }
        await applyFiltersWithMapControl(facilities, hLevels, aClasses, provs, radius, airportName, hospitalName);
        return;
    }

    // RESET RADIUS (hanya reset radius visual & reapply tanpa radius)
    if (e.target.id === 'resetRadiusMap') {
        // reset slider & tampilan
        const rEl = document.getElementById('radiusRangeMap');
        const rValEl = document.getElementById('radiusValueMap');
        if (rEl) rEl.value = 0;
        if (rValEl) rValEl.textContent = '0';

        // hapus circle & pin
        if (radiusCircle) { map.removeLayer(radiusCircle); radiusCircle = null; }
        if (radiusPinMarker) { map.removeLayer(radiusPinMarker); radiusPinMarker = null; }
        lastClickedLocation = null;

        // apply ulang tanpa radius (tetap simpan filter lain)
        const { facilities, hLevels, aClasses, provs, airportName, hospitalName } = getCurrentFiltersFromUI();
        await applyFiltersWithMapControl(facilities, hLevels, aClasses, provs, 0, airportName, hospitalName);
        return;
    }

    // RESET ALL FILTERS (tombol Reset All) -> gunakan handler yang sudah komprehensif
    if (e.target.id === 'resetMapFilter') {
        // 1) UI reset
        document.querySelectorAll('#filterPanel input[type="checkbox"]').forEach(cb => {
            cb.checked = false;
        });

        // reset province dropdown text
        const provinceSearch = document.getElementById('provinceSearch');
        if (provinceSearch) {
            provinceSearch.value = '';
        }

        // reset search keyword pada dropdown
        const provinceSearchInput = document.getElementById('provinceSearchInput');
        if (provinceSearchInput) {
            provinceSearchInput.value = '';
        }

        // tampilkan kembali semua province
        document.querySelectorAll('#provinceList li').forEach(li => {
            li.style.display = '';
        });

        // sembunyikan sub-panels
        const af = document.getElementById('airportFilter');
        const hf = document.getElementById('hospitalFilter');
        if (af) af.style.display = 'none';
        if (hf) hf.style.display = 'none';

        // 2) Reset Select2 (jika ada)
        if (typeof $ !== 'undefined' && $.fn && $.fn.select2) {
            $('.select-search-airport').each(function () { $(this).val(null).trigger('change'); });
            $('.select-search-hospital').each(function () { $(this).val(null).trigger('change'); });
        } else {
            const airportSel = document.getElementById('airport_name_map');
            const hospitalSel = document.getElementById('hospital_name_map');
            if (airportSel) airportSel.value = '';
            if (hospitalSel) hospitalSel.value = '';
        }

        // 3) Reset radius visual
        const radiusRange = document.getElementById('radiusRangeMap');
        const radiusValue = document.getElementById('radiusValueMap');
        if (radiusRange) radiusRange.value = 0;
        if (radiusValue) radiusValue.textContent = '0';
        if (radiusCircle) { map.removeLayer(radiusCircle); radiusCircle = null; }
        if (radiusPinMarker) { map.removeLayer(radiusPinMarker); radiusPinMarker = null; }
        lastClickedLocation = null;

        // 4) Remove drawn polygon and layers
        if (drawnItems) drawnItems.clearLayers();
        drawnPolygonGeoJSON = null;

        // 5) Clear markers and counters
        if (airportMarkers) airportMarkers.clearLayers();
        if (hospitalMarkers) hospitalMarkers.clearLayers();
        if (policeMarkers) policeMarkers.clearLayers();
        if (embassyMarkers) embassyMarkers.clearLayers();
        totalAirports = 0;
        totalHospitals = 0;
        totalPolice = 0;
        totalEmbassies = 0;
        updateTotalCountDisplay();

        // 6) Re-fetch semua data
        await applyFiltersWithMapControl(
            [],
            [],
            [],
            [],
            0,
            '',
            ''
        );

        e.stopPropagation();
        e.preventDefault();
        return;
    }
});

// === LISTEN TO CHANGE on filter inputs (kategori/provinsi/select nama) ===
// Ini memastikan ketika user change checkbox / select2, filter langsung ter-apply
function bindFilterChangeAutoApply() {
    // checkbox change
    document.querySelectorAll('#filterPanel input[type="checkbox"]').forEach(el => {
        el.addEventListener('change', async () => {
            const { facilities, hLevels, aClasses, provs, radius, airportName, hospitalName } = getCurrentFiltersFromUI();
            await applyFiltersWithMapControl(facilities, hLevels, aClasses, provs, radius, airportName, hospitalName);
        });
    });

    // select2 change (nama)
    // if Select2 is used, listen with jQuery; otherwise plain change event above covers plain <select>
    if (typeof $ !== 'undefined' && $.fn && $.fn.select2) {
        $(document).on('change', '#airport_name_map, #hospital_name_map', async function () {
            const { facilities, hLevels, aClasses, provs, radius, airportName, hospitalName } = getCurrentFiltersFromUI();
            await applyFiltersWithMapControl(facilities, hLevels, aClasses, provs, radius, airportName, hospitalName);
        });
    } else {
        document.getElementById('airport_name_map')?.addEventListener('change', async () => {
            const { facilities, hLevels, aClasses, provs, radius, airportName, hospitalName } = getCurrentFiltersFromUI();
            await applyFiltersWithMapControl(facilities, hLevels, aClasses, provs, radius, airportName, hospitalName);
        });
        document.getElementById('hospital_name_map')?.addEventListener('change', async () => {
            const { facilities, hLevels, aClasses, provs, radius, airportName, hospitalName } = getCurrentFiltersFromUI();
            await applyFiltersWithMapControl(facilities, hLevels, aClasses, provs, radius, airportName, hospitalName);
        });
    }
}

// call binding after panel is rendered
setTimeout(bindFilterChangeAutoApply, 350);

    // --- Initial Load ---
    refreshCurrentFilters();
</script>

@endpush
